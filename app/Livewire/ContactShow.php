<?php

namespace App\Livewire;

use App\Models\Contact;
use App\Models\ContactRelationship;
use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\Tag;

#[Layout('components.layout')]
class ContactShow extends Component
{
    public Contact $contact;
    public $availableContacts = [];
    public $showRelationshipModal = false;
    public $relationshipType = '';
    public $relatedContactId = '';
    public $relatedContactName = '';
    public $isCreatingNewContact = false;
    public $newContactFirstName = '';
    public $newContactLastName = '';
    public $newContactDateOfBirth = '';
    public $newContactEmail = '';
    public $sameAddress = false;

    public function mount(Contact $contact): void
    {
        $this->contact = Contact::with(['address', 'tags', 'relationships.relatedContact', 'inverseRelationships.contact'])
            ->visibleTo()
            ->findOrFail($contact->id);

        // Get IDs of contacts that already have relationships with this contact
        $relatedContactIds = $this->contact->relationships->pluck('related_contact_id')
            ->merge($this->contact->inverseRelationships->pluck('contact_id'))
            ->unique()
            ->toArray();

        $this->availableContacts = Contact::visibleTo()
            ->where('id', '!=', $this->contact->id)
            ->whereNotIn('id', $relatedContactIds)
            ->orderBy('last_name')
            ->get();
    }

    public function openRelationshipModal()
    {
        $this->showRelationshipModal = true;
    }

    public function closeRelationshipModal()
    {
        $this->showRelationshipModal = false;
        $this->relationshipType = '';
        $this->relatedContactId = '';
        $this->relatedContactName = '';
        $this->isCreatingNewContact = false;
        $this->newContactFirstName = '';
        $this->newContactLastName = '';
        $this->newContactDateOfBirth = '';
        $this->newContactEmail = '';
        $this->sameAddress = false;
    }

    public function toggleCreateNewContact()
    {
        $this->isCreatingNewContact = !$this->isCreatingNewContact;
        $this->relatedContactId = '';
        $this->relatedContactName = '';
        $this->newContactFirstName = '';
        $this->newContactLastName = '';
        $this->newContactDateOfBirth = '';
        $this->newContactEmail = '';
        $this->sameAddress = false;
    }

    protected function getReciprocalRelationship($type): ?string
    {
        $reciprocals = [
            'Parent' => 'Child',
            'Child' => 'Parent',
            'Spouse' => 'Spouse',
        ];

        return $reciprocals[$type] ?? null;
    }

    public function addRelationship()
    {
        if ($this->isCreatingNewContact) {
            // Validate new contact fields
            $this->validate([
                'relationshipType' => 'required|string|in:Parent,Child,Spouse',
                'newContactFirstName' => 'required|string|max:255',
                'newContactLastName' => 'required|string|max:255',
                'newContactDateOfBirth' => 'nullable|date',
                'newContactEmail' => 'nullable|email|max:255',
            ]);

            // Create the new contact
            $newContact = Contact::create([
                'user_id' => auth()->id(),
                'first_name' => $this->newContactFirstName,
                'last_name' => $this->newContactLastName,
                'date_of_birth' => $this->newContactDateOfBirth ?: null,
                'email' => $this->newContactEmail ?: null,
                'address_id' => $this->sameAddress ? $this->contact->address_id : null,
                'is_shared' => false,
            ]);

            $this->relatedContactId = $newContact->id;
        } else {
            // Validate and find existing contact by name
            $this->validate([
                'relationshipType' => 'required|string|in:Parent,Child,Spouse',
                'relatedContactName' => 'required|string',
            ]);

            // Find contact by full name
            $relatedContact = Contact::visibleTo()
                ->whereRaw("CONCAT(first_name, ' ', last_name) = ?", [$this->relatedContactName])
                ->first();

            if (!$relatedContact) {
                $this->addError('relatedContactName', 'Please select a valid contact from the list.');
                return;
            }

            $this->relatedContactId = $relatedContact->id;
        }

        // Check if relationship already exists
        $existingRelationship = ContactRelationship::where('contact_id', $this->contact->id)
            ->where('related_contact_id', $this->relatedContactId)
            ->first();

        if ($existingRelationship) {
            $this->addError('relationshipType', 'A relationship with this contact already exists.');
            return;
        }

        // Create the primary relationship
        ContactRelationship::create([
            'contact_id' => $this->contact->id,
            'related_contact_id' => $this->relatedContactId,
            'relationship_type' => $this->relationshipType,
        ]);

        // Create the reciprocal relationship
        $reciprocalType = $this->getReciprocalRelationship($this->relationshipType);
        if ($reciprocalType) {
            // Check if reciprocal already exists (shouldn't happen, but just in case)
            $existingReciprocal = ContactRelationship::where('contact_id', $this->relatedContactId)
                ->where('related_contact_id', $this->contact->id)
                ->first();

            if (!$existingReciprocal) {
                ContactRelationship::create([
                    'contact_id' => $this->relatedContactId,
                    'related_contact_id' => $this->contact->id,
                    'relationship_type' => $reciprocalType,
                ]);
            }
        }

        $this->closeRelationshipModal();
        $this->contact->refresh();
        session()->flash('success', 'Relationship added successfully.');
    }

    public function deleteRelationship($relationshipId)
    {
        $relationship = ContactRelationship::findOrFail($relationshipId);
        
        // Also delete the reciprocal relationship
        ContactRelationship::where('contact_id', $relationship->related_contact_id)
            ->where('related_contact_id', $relationship->contact_id)
            ->delete();
        
        $relationship->delete();
        $this->contact->refresh();
        session()->flash('success', 'Relationship deleted successfully.');
    }

    public function render()
    {
        return view('livewire.contact-show');
    }
}