<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Exception;

class FirebaseService
{
    protected $database;

    public function __construct()
    {
        $path = base_path(env('FIREBASE_CREDENTIALS'));

        if (!file_exists($path)) {
            throw new \Exception("Firebase credentials file does not exist at path: $path");
        }

        $factory = (new Factory)
            ->withServiceAccount($path)
            ->withDatabaseUri('https://airsentinel-6d53a-default-rtdb.asia-southeast1.firebasedatabase.app/');

        $this->database = $factory->createDatabase();
    }

    public function getAllDevices()
    {
        return $this->database->getReference('DEVICES')->getValue();
    }

    public function checkConnection()
    {
        try {
            $reference = $this->database->getReference('contacts');
            $reference->set(['connection' => true]);
            $snapShot = $reference->getSnapshot();
            return $snapShot->getValue();
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}