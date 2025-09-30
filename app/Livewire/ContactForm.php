<?php

namespace App\Livewire;

use App\Models\Address;
use App\Models\Contact;
use App\Models\Tag;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layout')]
class ContactForm extends Component
{
    use WithFileUploads;

    public $contactId = null;
    public $first_name = '';
    public $last_name = '';
    public $date_of_birth = '';
    public $anniversary_date = '';
    public $email = '';
    public $phone_number = '';
    public $notes = '';
    public $photo;
    public $existingPhoto = '';
    public $selectedTags = [];
    public $is_shared = false;
    
    // Address fields
    public $address_id = null;
    public $street = '';
    public $address_line_2 = '';
    public $city = '';
    public $postcode = '';
    public $country = '';
    public $createNewAddress = false;

    public function mount($contact = null)
    {
        if ($contact) {
            $contact = Contact::with(['address', 'tags'])
                ->visibleTo() // Only allow editing contacts visible to current user
                ->findOrFail($contact);

            // Allow editing if user owns the contact OR if it's shared
            abort_unless($contact->user_id === Auth::id() || $contact->is_shared, 403);
            
            $this->contactId = $contact->id;
            $this->first_name = $contact->first_name;
            $this->last_name = $contact->last_name;
            $this->date_of_birth = $contact->date_of_birth?->format('Y-m-d');
            $this->anniversary_date = $contact->anniversary_date?->format('Y-m-d');
            $this->email = $contact->email;
            $this->phone_number = $contact->phone_number;
            $this->notes = $contact->notes;
            $this->existingPhoto = $contact->photo;
            $this->selectedTags = $contact->tags->pluck('id')->toArray();
            $this->is_shared = $contact->is_shared;
            
            if ($contact->address) {
                $this->address_id = $contact->address_id;
                $this->street = $contact->address->street;
                $this->address_line_2 = $contact->address->address_line_2;
                $this->city = $contact->address->city;
                $this->postcode = $contact->address->postcode;
                $this->country = $contact->address->country;
            }
        }
    }

    protected function rules()
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'anniversary_date' => 'nullable|date',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'street' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postcode' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
        ];
    }

    public function save()
    {
        $this->validate();

        // Handle address
        $addressId = $this->address_id;
        
        if ($this->createNewAddress || ($this->street || $this->address_line_2 || $this->city || $this->postcode || $this->country)) {
            if (!$this->address_id || $this->createNewAddress) {
                $address = Address::create([
                    'street' => $this->street,
                    'address_line_2' => $this->address_line_2,
                    'city' => $this->city,
                    'postcode' => $this->postcode,
                    'country' => $this->country,
                ]);
                $addressId = $address->id;
            } else {
                // Update existing address
                Address::find($this->address_id)->update([
                    'street' => $this->street,
                    'address_line_2' => $this->address_line_2,
                    'city' => $this->city,
                    'postcode' => $this->postcode,
                    'country' => $this->country,
                ]);
            }
        }

        // Handle photo upload
        $photoPath = $this->existingPhoto;
        if ($this->photo) {
            $photoPath = $this->photo->store('photos', 'public');
        }

        // Create or update contact
        $contactData = [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'date_of_birth' => $this->date_of_birth ?: null,
            'anniversary_date' => $this->anniversary_date ?: null,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'notes' => $this->notes,
            'photo' => $photoPath,
            'address_id' => $addressId,
            'is_shared' => $this->is_shared,
        ];

        if ($this->contactId) {
            // Allow updating if user owns the contact OR if it's shared
            $contact = Contact::where('id', $this->contactId)
                ->where(function($query) {
                    $query->where('user_id', Auth::id())
                          ->orWhere('is_shared', true);
                })
                ->firstOrFail();
            $contact->update($contactData);
            $message = 'Contact updated successfully.';
        } else {
            $contactData['user_id'] = Auth::id();
            $contact = Contact::create($contactData);
            $message = 'Contact created successfully.';
        }

        // Sync tags
        $contact->tags()->sync($this->selectedTags);

        session()->flash('success', $message);
        return redirect()->route('contacts.show', $contact->id);
    }

    public function render()
    {
        $tags = Tag::all();
        $addresses = Address::all();

        return view('livewire.contact-form', [
            'tags' => $tags,
            'addresses' => $addresses,
        ]);
    }
}