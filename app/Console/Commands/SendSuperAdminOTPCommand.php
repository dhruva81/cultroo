<?php

namespace App\Console\Commands;

use App\Models\PendingUser;
use App\Models\User;
use App\Notifications\SendOTPBeforeRegistrationNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class SendSuperAdminOTPCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'superadmin:otp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send OTP for super admin account if not exists.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Please provide super admin email.');
        $email = $this->ask('Email?');

        $validator = Validator::make([
            'email' => $email,
        ], [
            'email' => ['required', 'email', 'unique:users,email'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            $this->newLine();

            return 1;
        }
        $user = User::where('user_type', 'super_admin')->get();

        if ($user->count() > 0) {
            $this->error('Super Admin account already exists!');
            $this->newLine();

            return 0;
        }

        PendingUser::firstOrCreate([
            'email' => $email,
            'user_type' => 'super_admin',
        ])->notify(new SendOTPBeforeRegistrationNotification);

        $this->info('A verification email has been sent to you.');
        $this->newLine();

        return 0;
    }
}
