<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\PendingUser;
use App\Models\User;
use App\Notifications\SendOTPBeforePasswordResetNotification;
use App\Notifications\SendOTPBeforeRegistrationNotification;
use App\Rules\OTPValidationRule;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Rules\Password;

class AuthController extends Controller
{

    /**
     * Login
     *
     * @unauthenticated
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return response()->json([
            'token' => $user->createToken($request->device_name)->plainTextToken,
            'user' => new UserResource($user),
        ], 201);
    }

    /**
     * Logout
     *
     */
    public function logout(Request $request)
    {
        return $request->user()->tokens()->delete();
    }

    /**
     * Register Send OTP
     *
     * @unauthenticated
     */
    public function registerSendOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        ]);

        PendingUser::firstOrCreate([
            'email' => $request->email,
            'user_type' => 'user',
        ])
            ->notify(new SendOTPBeforeRegistrationNotification);

        return response()->json([
            'message' => 'OTP sent successfully!',
        ], 201);
    }

    /**
     * Register Verify OTP
     *
     * @unauthenticated
     */
    public function registerVerifyOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'otp' => ['required', 'digits:4', new OTPValidationRule($request->email, $request->otp)],
        ]);

        return response()->json([
            'message' => 'OTP verified successfully!',
        ]);
    }

    /**
     * Register
     *
     * @unauthenticated
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email', 'exists:pending_users,email'],
            'otp' => ['required', 'digits:4', new OTPValidationRule($request->email, $request->otp)],
            'password' => ['required', 'string', new Password, 'confirmed'],
            'device_name' => ['required'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => 'user',
        ]);

        event(new Registered($user));

        return response()->json([
            'token' => $user->createToken($request->device_name)->plainTextToken,
            'user' => new UserResource($user),
        ], 201);
    }

    /**
     * Reset Password Send Otp
     *
     * @unauthenticated
     */
    public function resetPasswordSendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => ['This email ID is not registered.'],
            ]);
        }

        $user->update([
            'otp' => mt_rand(1000, 9999),
        ]);

        $user->notify(new SendOTPBeforePasswordResetNotification);

        return response()->json([
            'message' => 'OTP sent successfully to your registered email.',
        ], 201);
    }

    /**
     * Reset Password Verify Otp
     *
     * @unauthenticated
     */
    public function resetPasswordVerifyOTP(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'otp' => ['required', 'digits:4'],
        ]);

        $user = User::where([
            'email' => $request->email,
            'otp' => $request->otp,
        ])->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'otp' => ['OTP incorrect!'],
            ]);
        }

        $user->update([
            'otp' => null,
        ]);

        return response()->json([
            'message' => 'OTP verified successfully!',
        ], 200);
    }

    /**
     * Reset Password
     *
     * @unauthenticated
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', new Password, 'confirmed'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'otp' => null,
        ]);

        return response()->json([
            'message' => 'Password updated successfully!',
        ], 201);
    }

    /**
     * Set Account Pin
     *
     */
    public function setAccountPin(Request $request)
    {
        $request->validate([
            'pin' => ['required', 'digits:4'],
        ]);

        auth()->user()->update([
            'pin' => $request->pin,
        ]);

        return response()->json([
            'message' => 'Account pin set successfully!',
        ], 201);
    }

    /**
     * Verify Account Pin
     *
     */
    public function verifyAccountPin(Request $request)
    {
        $request->validate([
            'pin' => ['required', 'digits:4'],
        ]);

        if (auth()->user()->pin !== $request->pin) {
            throw ValidationException::withMessages([
                'pin' => ['Invalid account pin.'],
            ]);
        }

        return response()->json([
            'message' => 'Account pin verified successfully!',
        ], 200);
    }

    /**
     * Send OTP
     *
     * @unauthenticated
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => ['This email ID is not registered.'],
            ]);
        }

        $user->update([
            'otp' => mt_rand(1000, 9999),
        ]);

        $user->notify(new SendOTPBeforePasswordResetNotification);

        return response()->json([
            'message' => 'OTP sent successfully to your registered email.',
        ], 201);
    }

    /**
     * Verify OTP
     *
     * @unauthenticated
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'otp' => ['required', 'digits:4'],
        ]);

        $user = User::where([
            'email' => $request->email,
            'otp' => $request->otp,
        ])->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'otp' => ['OTP incorrect!'],
            ]);
        }

        $user->update([
            'otp' => null,
        ]);

        return response()->json([
            'message' => 'OTP verified successfully!',
        ], 200);
    }


}
