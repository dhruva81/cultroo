<?php

namespace App\Rules;

use App\Models\PendingUser;
use Illuminate\Contracts\Validation\Rule;

class OTPValidationRule implements Rule
{
    protected $email;

    protected $otp;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($email, $otp)
    {
        $this->email = $email;
        $this->otp = $otp;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return (bool) PendingUser::where([
            'email' => $this->email,
            'otp' => $this->otp,
        ])->first();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid OTP.';
    }
}
