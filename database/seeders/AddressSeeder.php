<?php

namespace Database\Seeders;

use App\Models\Address;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('en_GB');
        
        // Create 50 diverse addresses
        $ukCities = [
            ['city' => 'London', 'postcode' => 'SW1A'],
            ['city' => 'Manchester', 'postcode' => 'M1'],
            ['city' => 'Birmingham', 'postcode' => 'B1'],
            ['city' => 'Leeds', 'postcode' => 'LS1'],
            ['city' => 'Liverpool', 'postcode' => 'L1'],
            ['city' => 'Sheffield', 'postcode' => 'S1'],
            ['city' => 'Bristol', 'postcode' => 'BS1'],
            ['city' => 'Edinburgh', 'postcode' => 'EH1'],
            ['city' => 'Glasgow', 'postcode' => 'G1'],
            ['city' => 'Cardiff', 'postcode' => 'CF1'],
            ['city' => 'Newcastle', 'postcode' => 'NE1'],
            ['city' => 'Nottingham', 'postcode' => 'NG1'],
            ['city' => 'Southampton', 'postcode' => 'SO14'],
            ['city' => 'Leicester', 'postcode' => 'LE1'],
            ['city' => 'Brighton', 'postcode' => 'BN1'],
        ];

        for ($i = 0; $i < 50; $i++) {
            $location = $faker->randomElement($ukCities);
            
            Address::create([
                'street' => $faker->streetAddress,
                'address_line_2' => $faker->boolean(20) ? $faker->secondaryAddress : null,
                'city' => $location['city'],
                'postcode' => $location['postcode'] . ' ' . $faker->randomNumber(1) . strtoupper($faker->randomLetter) . strtoupper($faker->randomLetter),
                'country' => 'United Kingdom',
            ]);
        }
    }
}