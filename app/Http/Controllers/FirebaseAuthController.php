<?php 
namespace App\Http\Controllers;

use App\Models\User; 
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;

class FirebaseAuthController extends Controller
{
    protected $auth;

    public function __construct()
    {
        $path = base_path(env('FIREBASE_CREDENTIALS'));

        if (!file_exists($path)) {
            die("This file path ($path) does not exist");
        }

        $this->auth = (new Factory)
            ->withServiceAccount($path)
            ->createAuth();
    }

    public function registerForm()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        // Validate the form input
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed'],
        ]);

        try {
            // Create user in Firebase Auth
            $this->auth->createUser([
                'email' => $request->email,
                'password' => $request->password,
                'displayName' => $request->name,
            ]);

            // ✅ Also create the user in local Laravel MySQL database
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password), // Hashed for Laravel auth
            ]);

            // Dispatch registered event (for email verification, etc.)
            event(new Registered($user));

            // Log the user in using Laravel Breeze session
            Auth::login($user);

            // Redirect to dashboard
            return redirect()->route('dashboard');

        } catch (\Throwable $e) {
            return back()->withErrors(['firebase_error' => 'Registration failed: ' . $e->getMessage()]);
        }
    }
}