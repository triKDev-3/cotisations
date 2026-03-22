<?php

namespace App\Console\Commands;

use App\Services\FirebaseService;
use Illuminate\Console\Command;

class TestFirebase extends Command
{
    protected $signature = 'firebase:test';
    protected $description = 'Teste la connexion à Firestore';

    public function handle(FirebaseService $firebase)
    {
        $this->info("Tentative d'écriture dans Firestore...");
        try {
            $database = $firebase->getFirestore()->database();
            $database->collection('test_connection')->add([
                'status' => 'success',
                'time' => new \Google\Cloud\Core\Timestamp(new \DateTime()),
                'message' => 'Liaison Laravel-Firebase réussie !'
            ]);
            $this->info("Réussite ! Document créé dans la collection 'test_connection'.");
        } catch (\Exception $e) {
            $this->error("Échec : " . $e->getMessage());
        }
    }
}
