<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\ContactRelationship;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        if ($user) {
            $this->seedForUser($user);
        }
    }

    public function seedForUser(User $user, bool $limited = false): void
    {
        $familyTag = Tag::where('name', 'Family')->first();
        $friendsTag = Tag::where('name', 'Friends')->first();
        $richUniTag = Tag::where('name', 'Rich Uni')->first();
        $florenceSchoolTag = Tag::where('name', 'Florence School')->first();
        $workTag = Tag::where('name', 'Work')->first();

        if ($limited) {
            $contact1 = Contact::create([
                'user_id' => $user->id,
                'is_shared' => true,
                'first_name' => 'Alice',
                'last_name' => 'Johnson',
                'date_of_birth' => '1990-03-15',
                'email' => 'alice.johnson@example.com',
                'phone_number' => '+44 20 7946 1234',
                'notes' => 'Colleague from marketing department.',
                'address_id' => 2,
            ]);
            $contact1->tags()->attach([$workTag?->id]);

            $contact2 = Contact::create([
                'user_id' => $user->id,
                'is_shared' => false,
                'first_name' => 'Bob',
                'last_name' => 'Williams',
                'date_of_birth' => '1985-07-22',
                'email' => 'bob.williams@example.com',
                'phone_number' => '+44 20 7946 5678',
                'notes' => 'Old friend from college.',
                'address_id' => 3,
            ]);
            $contact2->tags()->attach([$friendsTag?->id]);

            return;
        }

        // Create full family for the main user
        // Create family members sharing the same address
        $john = Contact::create([
            'user_id' => $user->id,
            'is_shared' => false,
            'first_name' => 'John',
            'last_name' => 'Smith',
            'date_of_birth' => '1980-05-15',
            'anniversary_date' => '2005-06-20',
            'email' => 'john.smith@example.com',
            'phone_number' => '+44 20 7946 0958',
            'notes' => 'My spouse. Works as a software engineer. Loves hiking and photography.',
            'address_id' => 1,
        ]);
        $john->tags()->attach([$familyTag->id]);

        $sarah = Contact::create([
            'user_id' => $user->id,
            'is_shared' => false,
            'first_name' => 'Sarah',
            'last_name' => 'Smith',
            'date_of_birth' => '1982-08-22',
            'anniversary_date' => '2005-06-20',
            'email' => 'sarah.smith@example.com',
            'phone_number' => '+44 20 7946 0959',
            'notes' => 'Doctor at St. Mary\'s Hospital. Very organized and caring.',
            'address_id' => 1,
        ]);
        $sarah->tags()->attach([$familyTag->id]);

        // Create children
        $emma = Contact::create([
            'user_id' => $user->id,
            'is_shared' => false,
            'first_name' => 'Emma',
            'last_name' => 'Smith',
            'date_of_birth' => '2010-03-12',
            'email' => 'emma.smith@example.com',
            'notes' => 'Daughter. Loves art and music. Attending Florence School.',
            'address_id' => 1,
        ]);
        $emma->tags()->attach([$familyTag->id, $florenceSchoolTag->id]);

        $oliver = Contact::create([
            'user_id' => $user->id,
            'is_shared' => false,
            'first_name' => 'Oliver',
            'last_name' => 'Smith',
            'date_of_birth' => '2012-11-05',
            'notes' => 'Son. Passionate about football and video games.',
            'address_id' => 1,
        ]);
        $oliver->tags()->attach([$familyTag->id, $florenceSchoolTag->id]);

        // Create relationships
        ContactRelationship::create([
            'contact_id' => $john->id,
            'related_contact_id' => $sarah->id,
            'relationship_type' => 'spouse',
        ]);

        ContactRelationship::create([
            'contact_id' => $sarah->id,
            'related_contact_id' => $john->id,
            'relationship_type' => 'spouse',
        ]);

        ContactRelationship::create([
            'contact_id' => $john->id,
            'related_contact_id' => $emma->id,
            'relationship_type' => 'child',
        ]);

        ContactRelationship::create([
            'contact_id' => $sarah->id,
            'related_contact_id' => $emma->id,
            'relationship_type' => 'child',
        ]);

        ContactRelationship::create([
            'contact_id' => $john->id,
            'related_contact_id' => $oliver->id,
            'relationship_type' => 'child',
        ]);

        ContactRelationship::create([
            'contact_id' => $sarah->id,
            'related_contact_id' => $oliver->id,
            'relationship_type' => 'child',
        ]);

        ContactRelationship::create([
            'contact_id' => $emma->id,
            'related_contact_id' => $john->id,
            'relationship_type' => 'parent',
        ]);

        ContactRelationship::create([
            'contact_id' => $emma->id,
            'related_contact_id' => $sarah->id,
            'relationship_type' => 'parent',
        ]);

        ContactRelationship::create([
            'contact_id' => $oliver->id,
            'related_contact_id' => $john->id,
            'relationship_type' => 'parent',
        ]);

        ContactRelationship::create([
            'contact_id' => $oliver->id,
            'related_contact_id' => $sarah->id,
            'relationship_type' => 'parent',
        ]);

        ContactRelationship::create([
            'contact_id' => $emma->id,
            'related_contact_id' => $oliver->id,
            'relationship_type' => 'sibling',
        ]);

        ContactRelationship::create([
            'contact_id' => $oliver->id,
            'related_contact_id' => $emma->id,
            'relationship_type' => 'sibling',
        ]);

        // Create university friends
        $michael = Contact::create([
            'user_id' => $user->id,
            'is_shared' => true, // Shared contact
            'first_name' => 'Michael',
            'last_name' => 'Johnson',
            'date_of_birth' => '1981-02-14',
            'email' => 'michael.johnson@example.com',
            'phone_number' => '+44 161 496 0000',
            'notes' => 'Met at Rich University. Now works in finance. Great sense of humor.',
            'address_id' => 2,
        ]);
        $michael->tags()->attach([$friendsTag->id, $richUniTag->id]);

        $lucy = Contact::create([
            'user_id' => $user->id,
            'is_shared' => true, // Shared contact
            'first_name' => 'Lucy',
            'last_name' => 'Williams',
            'date_of_birth' => '1982-09-30',
            'email' => 'lucy.williams@example.com',
            'phone_number' => '+44 121 496 0000',
            'notes' => 'Best friend from Rich University. Marketing director. Always up for coffee.',
            'address_id' => 3,
        ]);
        $lucy->tags()->attach([$friendsTag->id, $richUniTag->id]);

        // Create work colleagues
        $david = Contact::create([
            'user_id' => $user->id,
            'is_shared' => false,
            'first_name' => 'David',
            'last_name' => 'Brown',
            'date_of_birth' => '1985-07-18',
            'email' => 'david.brown@example.com',
            'phone_number' => '+44 131 496 0000',
            'notes' => 'Work colleague. Team lead. Very knowledgeable about tech.',
            'address_id' => 4,
        ]);
        $david->tags()->attach([$workTag->id]);

        $jessica = Contact::create([
            'user_id' => $user->id,
            'is_shared' => false,
            'first_name' => 'Jessica',
            'last_name' => 'Davis',
            'date_of_birth' => '1988-12-25',
            'email' => 'jessica.davis@example.com',
            'phone_number' => '+44 117 496 0000',
            'notes' => 'Project manager at work. Very organized and professional.',
            'address_id' => 5,
        ]);
        $jessica->tags()->attach([$workTag->id]);

        // Create some friend relationships
        ContactRelationship::create([
            'contact_id' => $john->id,
            'related_contact_id' => $michael->id,
            'relationship_type' => 'friend',
        ]);

        ContactRelationship::create([
            'contact_id' => $sarah->id,
            'related_contact_id' => $lucy->id,
            'relationship_type' => 'friend',
        ]);
    }
}