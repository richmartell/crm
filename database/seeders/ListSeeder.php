<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\ContactList;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ListSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        $users->each(function (User $user) {
            $lists = [
                [
                    'name' => 'Christmas Card List',
                    'description' => 'Friends and family to receive holiday cards.',
                ],
                [
                    'name' => 'Event Invitees',
                    'description' => 'Invitations for the upcoming summer BBQ.',
                ],
                [
                    'name' => 'VIP Clients',
                    'description' => 'High-priority clients that require personal follow-up.',
                ],
            ];

            $pivotColumns = ContactList::query()->getModel()->contacts()->getPivotColumns();

            foreach ($lists as $data) {
                $list = ContactList::create([
                    'user_id' => $user->id,
                    'name' => $data['name'],
                    'description' => $data['description'],
                ]);

                $contacts = Contact::visibleTo($user)
                    ->inRandomOrder()
                    ->take(3)
                    ->pluck('id');

                $timestamp = Carbon::now();

                $payload = $contacts->mapWithKeys(fn ($id) => [$id => ['added_at' => $timestamp]])->toArray();

                $list->contacts()->syncWithoutDetaching($payload);
            }
        });
    }
}
