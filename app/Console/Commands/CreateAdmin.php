<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateAdmin extends Command
{
    protected $signature = 'app:create-admin {name} {email}';

    protected $description = 'Create the first administrator account for the PKL monitoring app';

    public function handle(): int
    {
        if (User::query()->where('role', 'admin_hubin')->exists()) {
            $this->components->error('Akun admin sudah tersedia. Buat akun staf berikutnya dari dashboard admin.');

            return self::FAILURE;
        }

        $validator = Validator::make([
            'name' => $this->argument('name'),
            'email' => $this->argument('email'),
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        ]);

        if ($validator->fails()) {
            $this->components->error($validator->errors()->first());

            return self::FAILURE;
        }

        $password = $this->secret('Password admin');
        $passwordConfirmation = $this->secret('Ulangi password admin');

        $passwordValidator = Validator::make([
            'password' => $password,
            'password_confirmation' => $passwordConfirmation,
        ], [
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if ($passwordValidator->fails()) {
            $this->components->error($passwordValidator->errors()->first());

            return self::FAILURE;
        }

        User::query()->create([
            'name' => $this->argument('name'),
            'email' => $this->argument('email'),
            'password' => Hash::make($password),
            'role' => 'admin_hubin',
        ]);

        $this->components->info('Akun admin berhasil dibuat. Silakan masuk melalui halaman login.');

        return self::SUCCESS;
    }
}
