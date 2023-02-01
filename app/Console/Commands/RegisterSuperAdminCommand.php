<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Rules\OTPValidationRule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterSuperAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'superadmin:register';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates super admin account if not exists.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Please provide Name, Email, OTP and Password for super admin account');
        $name = $this->ask('Name?');
        $email = $this->ask('Email?');
        $otp = $this->ask('OTP?');
        $password = $this->secret('Password?');
        $password_confirmation = $this->secret('Confirm Password?');

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'otp' => $otp,
            'password_confirmation' => $password_confirmation,
        ], [
            'name' => ['required'],
            'otp' => ['required', 'digits:4', new OTPValidationRule($email, $otp)],
            'email' => ['required', 'email', 'unique:users,email', 'exists:pending_users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            $this->newLine();

            return 1;
        }

        $user = User::where('user_type', 'super_admin')->first();

        if ($user) {
            $this->error('Supers Admin account already exists!');
            $this->newLine();

            return 0;
        }

        if ($this->confirm('All Ok! Do you wish to create super admin account?')) {
            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'user_type' => 'super_admin',
            ]);
            $this->info('Success! Super Admin Account has been successfully created');
            $this->newLine();

            return true;
        }

        $this->info('Super Admin Account was not created!');
        $this->newLine();

        return 0;
    }
}
