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
            ->withDatabaseUri('https://airhack-7b64d-default-rtdb.firebaseio.com/');

        $this->database = $factory->createDatabase();
    }

    public function getAllDevices()
    {
        return $this->database->getReference('devices')->getValue();
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