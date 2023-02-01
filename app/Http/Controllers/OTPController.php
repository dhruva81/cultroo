<?php

namespace App\Http\Controllers;

use App\Models\PendingUser;
use App\Models\User;

class OTPController extends Controller
{
    public function __invoke()
    {
        $pendingUserOtps = PendingUser::whereNotNull('otp')
                                        ->whereDate('created_at', today())
                                        ->whereUserType('user')
                                        ->get();
        $passwordResetOtps = User::whereNotNull('otp')
                                        ->whereDate('updated_at', today())
                                        ->get();

        return view('otp', compact('pendingUserOtps', 'passwordResetOtps'));
    }
}
