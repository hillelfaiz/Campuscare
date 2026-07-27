<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http; 
use Illuminate\Support\Facades\Log;  
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showLogin() {
        if (Auth::check()) return redirect()->route('lost-found.index');
        return view('auth.login');
    }

    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $request->session()->regenerate();

            // Sinkronisasi user lama ke backend facility
            $this->syncUserToFacilityApi(Auth::user());

            return redirect()->route('lost-found.index');
        }

        return back()->withErrors(['email' => 'Email atau password salah.']);
    }

    public function showRegister() { return view('auth.register'); }

    public function register(Request $request) {
        $request->validate(['name' => 'required', 'email' => 'required|email|unique:users', 'password' => 'required|min:6|confirmed', 'nim' => 'required']);
        
        $user = User::create(['name' => $request->name, 'email' => $request->email, 'nim' => $request->nim, 'password' => Hash::make($request->password), 'role' => 'mahasiswa']);
        
        // SINKRONISASI EAI AUTOMATIC REGISTER
        $this->syncUserToFacilityApi($user);

        Auth::login($user);
        return redirect()->route('lost-found.index');
    }

    public function redirectToGoogle() { return Socialite::driver('google')->redirect(); }

    public function handleGoogleCallback() {
        try {
            $userG = Socialite::driver('google')->stateless()->setHttpClient(new \GuzzleHttp\Client(['verify' => false]))->user();
            $existingUser = User::where('email', $userG->getEmail())->first();
            if (!$existingUser) {
                session(['google_email' => $userG->getEmail(), 'google_name' => $userG->getName(), 'google_id' => $userG->getId()]);
                return redirect()->route('auth.complete-profile');
            }
            if(!$existingUser->google_id) $existingUser->update(['google_id' => $userG->getId()]);
            
            // SINKRONISASI EAI AUTOMATIC GOOGLE LOGIN USER LAMA
            $this->syncUserToFacilityApi($existingUser);

            Auth::login($existingUser);
            return redirect()->route('lost-found.index');
        } catch (\Exception $e) { return redirect()->route('login')->withErrors(['email' => 'Gagal login dengan Google.']); }
    }

    public function showCompleteProfile() { 
        if(!session('google_email')) return redirect()->route('login');
        return view('auth.complete-profile'); 
    }

    public function storeCompleteProfile(Request $request) {
        $request->validate(['name' => 'required', 'nim' => 'required', 'password' => 'required|min:6|confirmed']);
        
        $user = User::create(['name' => $request->name, 'email' => session('google_email'), 'nim' => $request->nim, 'password' => Hash::make($request->password), 'role' => 'mahasiswa', 'google_id' => session('google_id')]);
        
        // SINKRONISASI EAI AUTOMATIC GOOGLE USER BARU LENGKAPI PROFIL
        $this->syncUserToFacilityApi($user);

        Auth::login($user);
        session()->forget(['google_email', 'google_name', 'google_id']);
        return redirect()->route('lost-found.index');
    }

    public function logout(Request $request) { 
        Auth::logout(); $request->session()->invalidate(); $request->session()->regenerateToken();
        return redirect()->route('login'); 
    }

    private function syncUserToFacilityApi($user)
    {
        try {
            $response = Http::timeout(5)->acceptJson()->post('http://127.0.0.1:8001/api/eai/sync-user', [
                'nama'          => $user->name,
                'name'          => $user->name,
                'email'         => $user->email,
                'password_hash' => $user->password,
                'nim'           => $user->nim ?? null,
                'role'          => $user->role ?? 'mahasiswa',
            ]);

            if ($response->failed()) {
                Log::error('Sinkronisasi EAI gagal. Status: ' . $response->status() . ' Body: ' . $response->body());
            }

        } catch (\Exception $e) {
            Log::error('Gagal Sinkronisasi EAI User ke Port 8001: ' . $e->getMessage());
        }
    }
}