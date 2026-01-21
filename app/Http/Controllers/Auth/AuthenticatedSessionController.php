<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Personnel;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{


    public function userLogin(Request $request)
    {


        // Find user by matricule in Personnel table
        $user = Personnel::where('pers_no', $request->matricule)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'matricule' => trans('auth.failed'),
            ]);
        }

        // Log in the user manually using the personnel guard
        Auth::guard('personnel')->login($user);

        // Regenerate session for security
        $request->session()->regenerate();

        Log::info("User logged in successfully", ['user_id' => $user->pers_no]);

        return redirect()->route('user.dashboard');
    }

    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        // Redirect based on user role
        if ($user->role === 'super_admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'agent') {
            return redirect()->route('agent.dashboard');
        } elseif ($user->role === 'user') {
            return redirect()->route('user.dashboard');
        }

        // Default fallback
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
