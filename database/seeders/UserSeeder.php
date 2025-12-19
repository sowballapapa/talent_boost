<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $senegaleseNames = [
            ['firstname' => 'Moussa', 'lastname' => 'Diop', 'sex' => 'M'],
            ['firstname' => 'Fatou', 'lastname' => 'Ndiaye', 'sex' => 'F'],
            ['firstname' => 'Mamadou', 'lastname' => 'Sow', 'sex' => 'M'],
            ['firstname' => 'Aminata', 'lastname' => 'Fall', 'sex' => 'F'],
            ['firstname' => 'Cheikh', 'lastname' => 'Gueye', 'sex' => 'M'],
            ['firstname' => 'Awa', 'lastname' => 'Ba', 'sex' => 'F'],
            ['firstname' => 'Ibrahima', 'lastname' => 'Sy', 'sex' => 'M'],
            ['firstname' => 'Khady', 'lastname' => 'Cisse', 'sex' => 'F'],
            ['firstname' => 'Ousmane', 'lastname' => 'Diallo', 'sex' => 'M'],
            ['firstname' => 'Mariama', 'lastname' => 'Faye', 'sex' => 'F'],
        ];

        foreach ($senegaleseNames as $index => $person) {
            $user = User::create([
                'firstname' => $person['firstname'],
                'lastname' => $person['lastname'],
                'email' => strtolower($person['firstname'] . '.' . $person['lastname'] . '@example.com'),
                'password' => Hash::make('password'),
                'phone' => '77' . str_pad($index, 7, '0', STR_PAD_LEFT),
                'sex' => $person['sex'],
                'address' => 'Dakar, Senegal',
                'role' => $index < 5 ? 'vendeur' : 'acheteur',
                'id_card_recto' => 'path/to/recto.jpg',
                'id_card_verso' => 'path/to/verso.jpg',
                'is_active' => true,
            ]);


        }
    }
}
