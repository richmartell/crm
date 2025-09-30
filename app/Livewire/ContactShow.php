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

    public function mount(Contact $contact): void
    {
        $this->contact = Contact::with(['address', 'tags', 'relationships.relatedContact', 'inverseRelationships.contact'])
            ->visibleTo()
            ->findOrFail($contact->id);

        $this->availableContacts = Contact::visibleTo()
            ->where('id', '!=', $this->contact->id)
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
    }

    public function addRelationship()
    {
        $this->validate([
            'relationshipType' => 'required|string',
            'relatedContactId' => 'required|exists:contacts,id',
        ]);

        ContactRelationship::create([
            'contact_id' => $this->contact->id,
            'related_contact_id' => $this->relatedContactId,
            'relationship_type' => $this->relationshipType,
        ]);

        $this->closeRelationshipModal();
        $this->contact->refresh();
        session()->flash('success', 'Relationship added successfully.');
    }

    public function deleteRelationship($relationshipId)
    {
        ContactRelationship::findOrFail($relationshipId)->delete();
        $this->contact->refresh();
        session()->flash('success', 'Relationship deleted successfully.');
    }

    public function render()
    {
        return view('livewire.contact-show');
    }
}