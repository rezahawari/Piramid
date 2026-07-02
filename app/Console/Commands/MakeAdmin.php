<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeAdmin extends Command
{
    protected $signature = 'user:make-admin {email}';

    protected $description = 'Promote an existing user to the admin role';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->error("User with email {$this->argument('email')} not found.");

            return self::FAILURE;
        }

        $user->update(['role' => 'admin']);
        $this->info("{$user->email} is now an admin.");

        return self::SUCCESS;
    }
}
