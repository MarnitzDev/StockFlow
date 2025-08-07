<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contact;

class ContactSeeder extends Seeder
{
    public function run()
    {
        $types = ['supplier', 'customer'];

        foreach (range(1, 30) as $index) {
            Contact::create([
                'name' => "Contact $index",
                'type' => $types[array_rand($types)],
                'email' => "contact$index@example.com",
                'phone' => "+" . rand(1, 999) . rand(1000000000, 9999999999),
                'address' => "Address for Contact $index",
            ]);
        }
    }
}
