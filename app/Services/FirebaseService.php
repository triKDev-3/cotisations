<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Firestore;

class FirebaseService
{
    protected $firestore;

    public function __construct()
    {
        $serviceAccount = config('services.firebase.service_account');
        $projectId = config('services.firebase.project_id');

        $factory = (new Factory);
        if ($projectId) {
            $factory = $factory->withProjectId($projectId);
        }
        
        // Si le serviceAccount est un chemin vers un fichier JSON
        if (is_string($serviceAccount) && file_exists($serviceAccount)) {
            $factory = $factory->withServiceAccount($serviceAccount);
        } 
        // Sinon, on tente de le charger comme un JSON brut (pour .env string)
        elseif (is_string($serviceAccount) && str_starts_with(trim((string)$serviceAccount), '{')) {
            $factory = $factory->withServiceAccount(json_decode($serviceAccount, true));
        }

        try {
            $this->firestore = $factory->createFirestore();
        } catch (\Exception $e) {
            // Si la config est manquante sur Render, on ne stoppe pas l'appli
            logger()->error("Erreur Firebase: " . $e->getMessage());
            $this->firestore = null;
        }
    }

    public function getFirestore()
    {
        return $this->firestore;
    }

    /**
     * Push a new cotisation to Firestore for real-time notifications.
     */
    public function notifyCotisation($cotisation)
    {
        if (!$this->firestore) return;

        $database = $this->firestore->database();
        $database->collection('cotisations')->add([
            'id' => $cotisation->id,
            'user_id' => (int) $cotisation->user_id,
            'montant' => (float) $cotisation->montant,
            'date_paiement' => $cotisation->date_paiement->format('Y-m-d'), // Format simple
            'date_enregistrement' => new \Google\Cloud\Core\Timestamp(new \DateTime()), // Pour le orderBy
            'username' => $cotisation->user->name ?? 'Anonyme',
        ]);
    }
}
