<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SmsSender;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Models\Personnel;
use Illuminate\Support\Facades\Session;
class AuthController extends Controller
{
    public function requestOtp(Request $request)
{
    $request->validate(['matricule' => 'required|string']);

    $user = Personnel::where('pers_no', $request->matricule)->first();

    if (!$user) {
        throw ValidationException::withMessages([
            'matricule' => trans('auth.failed'),
        ]);
    }

    // Generate OTP
    $otp = rand(100000, 999999);
    $user->otp_code = $otp;
    $user->otp_expires_at = now()->addMinutes(5);
    $user->save();

    // Send SMS using L2T API
    $toPhone = '216' . ltrim($user->phone, '0'); // Make sure it's in international format!
    $message = "Votre code de connexion Leoni: $otp";
    $sender = 'LEONI'; // or customize

    $smsSender = new SmsSender();
    $result = $smsSender->send($toPhone, $message, $sender);

    Log::info("OTP sent by SMS", [
        'matricule' => $user->pers_no,
        'otp'       => $otp,
        'phone'     => $toPhone,
        'apiResult' => $result,
    ]);

    Session::put('otp_matricule', $user->pers_no);

    return response()->json(['success' => true, 'message' => 'OTP sent to your phone']);
}

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);

        $matricule = Session::get('otp_matricule');
        if (!$matricule) {
            return response()->json(['success' => false, 'message' => 'Session expired. Please try again.'], 401);
        }

        $user = Personnel::where('pers_no', $matricule)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found.'], 404);
        }

        if (
            $user->otp_code !== $request->otp ||
            !$user->otp_expires_at ||
            now()->greaterThan($user->otp_expires_at)
        ) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired OTP code.'], 401);
        }

        // Clear OTP for security
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        // Login the user
        Auth::guard('personnel')->login($user);
        $request->session()->regenerate();

        // Remove from session
        Session::forget('otp_matricule');

        return response()->json(['success' => true, 'redirect' => route('user.dashboard')]);
    }
}