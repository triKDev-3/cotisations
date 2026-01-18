<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Collecteur
        $collecteur = User::create([
            'name' => 'Collecteur Principal',
            'numero_compte' => 'ADMIN01',
            'email' => 'admin@cotisations.com',
            'role' => 'collecteur',
            'password' => bcrypt('password'),
        ]);

        // Jeune standard
        $jeune = User::create([
            'name' => 'Jean Dupont',
            'numero_compte' => 'JEUNE01',
            'email' => 'jean@example.com',
            'role' => 'jeune',
            'password' => bcrypt('password'),
        ]);

        // Jeune triK (User Request)
        $trik = User::create([
            'name' => 'triK',
            'numero_compte' => 'JEUNE02',
            'email' => 'triK@gmail.com',
            'role' => 'jeune',
            'password' => bcrypt('password'),
        ]);

        // Cotisation Example for Jeune 1
        \App\Models\Cotisation::create([
            'user_id' => $jeune->id,
            'montant' => 5000,
            'date_paiement' => now()->subDays(2),
            'statut' => 'payé',
            'collecteur_id' => $collecteur->id,
        ]);

        // Cotisation Example for triK
        \App\Models\Cotisation::create([
            'user_id' => $trik->id,
            'montant' => 10000,
            'date_paiement' => now()->subDay(),
            'statut' => 'payé',
            'collecteur_id' => $collecteur->id,
        ]);
    }
}
