<?php

namespace Database\Seeders;

use App\Models\Address;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        $addresses = [
            [
                'street' => '123 Oak Street',
                'city' => 'London',
                'postcode' => 'SW1A 1AA',
                'country' => 'United Kingdom',
            ],
            [
                'street' => '456 Maple Avenue',
                'city' => 'Manchester',
                'postcode' => 'M1 1AA',
                'country' => 'United Kingdom',
            ],
            [
                'street' => '789 Elm Road',
                'city' => 'Birmingham',
                'postcode' => 'B1 1AA',
                'country' => 'United Kingdom',
            ],
            [
                'street' => '321 Pine Close',
                'city' => 'Edinburgh',
                'postcode' => 'EH1 1AA',
                'country' => 'United Kingdom',
            ],
            [
                'street' => '654 Cedar Lane',
                'city' => 'Bristol',
                'postcode' => 'BS1 1AA',
                'country' => 'United Kingdom',
            ],
        ];

        foreach ($addresses as $address) {
            Address::create($address);
        }
    }
}