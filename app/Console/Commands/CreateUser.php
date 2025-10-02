<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user account';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Create a new user account');
        $this->newLine();

        // Get name
        $name = $this->ask('Name');
        
        // Get email
        $email = $this->ask('Email');
        
        // Validate email doesn't already exist
        if (User::where('email', $email)->exists()) {
            $this->error('A user with this email already exists!');
            return Command::FAILURE;
        }

        // Get password
        $password = $this->secret('Password');
        $passwordConfirmation = $this->secret('Confirm Password');

        // Validate inputs
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            $this->newLine();
            $this->error('Validation failed:');
            foreach ($validator->errors()->all() as $error) {
                $this->error('  ' . $error);
            }
            return Command::FAILURE;
        }

        // Check if passwords match
        if ($password !== $passwordConfirmation) {
            $this->error('Passwords do not match!');
            return Command::FAILURE;
        }

        // Create the user
        try {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
            ]);

            $this->newLine();
            $this->info('✓ User created successfully!');
            $this->newLine();
            $this->table(
                ['ID', 'Name', 'Email', 'Created At'],
                [[$user->id, $user->name, $user->email, $user->created_at->format('Y-m-d H:i:s')]]
            );

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to create user: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

