<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use PhpParser\Node\Stmt\TryCatch;

class FirebaseConnectionController extends Controller
{
    public function index(){
        $path = base_path(env('FIREBASE_CREDENTIALS'));

        if(!file_exists($path)){
            die("This File Path .($path). does not Exist");
        }

        try{

            $factory = (new Factory)
            ->withServiceAccount($path)
            ->withDatabaseUri('https://airhack-7b64d-default-rtdb.firebaseio.com/');

            $database = $factory->createDatabase();
            $reference = $database->getReference('contacts');
            $reference->set(['connection' => true]);
            $snapShot = $reference->getSnapshot();
            $value = $snapShot->getValue();

            return response([
                'message' => true,
                'value' => $value
            ]);

        }catch(Exception $e){
            return response([
                'message' => $e->getMessage(),
                'status' => 'False',
            ]);
        }
    }
}
