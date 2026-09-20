<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /**
     * Show the student registration form.
     */
    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request.
     */
    public function register(Request $request): RedirectResponse
    {
        $nameRule = ['required', 'string', 'max:100', 'regex:/^[\pL\pM]+(?: [\pL\pM]+)*$/u'];

        $validated = $request->validate([
            'first_name' => $nameRule,
            'middle_name' => ['nullable', 'string', 'max:100', 'regex:/^[\pL\pM]+(?: [\pL\pM]+)*$/u'],
            'last_name' => $nameRule,
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'first_name.regex' => 'The first name may contain letters only. A single space is allowed between compound names.',
            'middle_name.regex' => 'The middle name may contain letters only. A single space is allowed between compound names.',
            'last_name.regex' => 'The last name may contain letters only. A single space is allowed between compound names.',
        ]);

        $firstName = trim(preg_replace('/\s+/u', ' ', $validated['first_name']));
        $middleName = isset($validated['middle_name'])
            ? trim(preg_replace('/\s+/u', ' ', $validated['middle_name']))
            : null;
        $lastName = trim(preg_replace('/\s+/u', ' ', $validated['last_name']));
        $fullName = implode(' ', array_filter([$firstName, $middleName, $lastName]));

        $user = User::create([
            'name'     => $fullName,
            'first_name' => $firstName,
            'middle_name' => $middleName ?: null,
            'last_name' => $lastName,
            'email'    => $validated['email'],
            'role'     => 'student',
            'password' => Hash::make($validated['password']),
        ]);

        event(new Registered($user));

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('feedback.create');
    }
}
