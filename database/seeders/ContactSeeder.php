<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\ContactRelationship;
use App\Models\Tag;
use App\Models\User;
use App\Models\Address;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('en_GB');
        $users = User::all();
        $tags = Tag::all();
        $addresses = Address::all();

        if ($users->isEmpty()) {
            return;
        }

        // Generate 120 contacts across all users
        $contactsToCreate = 120;
        $contactsPerUser = ceil($contactsToCreate / $users->count());

        foreach ($users as $userIndex => $user) {
            $contacts = [];
            
            // Determine how many contacts this user should have
            $numContacts = min($contactsPerUser, $contactsToCreate - (count($contacts) * $userIndex));
            
            for ($i = 0; $i < $numContacts; $i++) {
                // Randomly decide if contact should be shared (30% chance)
                $isShared = $faker->boolean(30);
                
                // Generate a birthday (spread across all months for good birthday page testing)
                $birthday = $faker->dateTimeBetween('-80 years', '-18 years')->format('Y-m-d');
                
                // 30% chance of having an anniversary date
                $anniversaryDate = $faker->boolean(30) 
                    ? $faker->dateTimeBetween('-30 years', 'now')->format('Y-m-d')
                    : null;
                
                $contact = Contact::create([
                    'user_id' => $user->id,
                    'is_shared' => $isShared,
                    'first_name' => $faker->firstName,
                    'last_name' => $faker->lastName,
                    'date_of_birth' => $birthday,
                    'anniversary_date' => $anniversaryDate,
                    'email' => $faker->unique()->email,
                    'phone_number' => $faker->boolean(80) ? $faker->phoneNumber : null,
                    'notes' => $faker->boolean(40) ? $faker->sentence(10) : null,
                    'address_id' => $faker->boolean(70) ? $addresses->random()->id : null,
                ]);

                // Attach 1-3 random tags to some contacts
                if ($faker->boolean(60)) {
                    $numTags = $faker->numberBetween(1, 3);
                    $contact->tags()->attach($tags->random($numTags)->pluck('id'));
                }

                $contacts[] = $contact;
            }

            // Create some relationships between contacts (10% of contacts)
            $numRelationships = (int)($numContacts * 0.1);
            for ($i = 0; $i < $numRelationships; $i++) {
                if (count($contacts) < 2) break;

                $contact1 = $faker->randomElement($contacts);
                $contact2 = $faker->randomElement($contacts);

                // Make sure we don't create a relationship with itself
                if ($contact1->id === $contact2->id) continue;

                // Check if relationship already exists
                $existingRelationship = ContactRelationship::where('contact_id', $contact1->id)
                    ->where('related_contact_id', $contact2->id)
                    ->exists();

                if (!$existingRelationship) {
                    $relationshipTypes = ['Parent', 'Child', 'Spouse'];
                    $type = $faker->randomElement($relationshipTypes);
                    
                    // Create primary relationship
                    ContactRelationship::create([
                        'contact_id' => $contact1->id,
                        'related_contact_id' => $contact2->id,
                        'relationship_type' => $type,
                    ]);

                    // Create reciprocal relationship
                    $reciprocals = [
                        'Parent' => 'Child',
                        'Child' => 'Parent',
                        'Spouse' => 'Spouse',
                    ];

                    ContactRelationship::create([
                        'contact_id' => $contact2->id,
                        'related_contact_id' => $contact1->id,
                        'relationship_type' => $reciprocals[$type],
                    ]);
                }
            }
        }
    }
}