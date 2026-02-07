<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class UpdateUserRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:update-role {email} {role}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update a user role. Usage: php artisan user:update-role user@example.com admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $role = $this->argument('role');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email '{$email}' not found.");
            return 1;
        }

        $validRoles = ['admin', 'inventory_staff', 'temperature_staff', 'payment_staff', 'delivery_personnel'];
        
        if (!in_array($role, $validRoles)) {
            $this->error("Invalid role. Valid roles are: " . implode(', ', $validRoles));
            return 1;
        }

        $oldRole = $user->role;
        $user->update(['role' => $role]);

        $this->info("✓ User '{$user->name}' ({$email}) role updated from '{$oldRole}' to '{$role}'");
        
        return 0;
    }
}
