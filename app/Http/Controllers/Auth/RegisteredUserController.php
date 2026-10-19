<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming customer registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'regex:/^[6-9]\d{9}$/'],
            'state' => ['required', 'string', 'max:10'],
            'city' => ['required', 'string', 'max:100'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()],
            'terms_agreed' => ['accepted'],
        ], [
            'phone.regex' => 'Please provide a valid 10-digit Indian mobile number.',
            'terms_agreed.accepted' => 'You must accept the Terms of Service and Privacy Policy to create an account.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'state' => strtoupper($validated['state']),
            'city' => $validated['city'],
            'password' => Hash::make($validated['password']),
            'role' => User::ROLE_CUSTOMER,
            'is_active' => true,
            'last_login_at' => now(),
        ]);

        // Automatically associate any prior guest enquiries matching this email
        Enquiry::where('email', $user->email)
            ->whereNull('user_id')
            ->update(['user_id' => $user->id]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('status', 'Your customer account has been created successfully.');
    }
}
