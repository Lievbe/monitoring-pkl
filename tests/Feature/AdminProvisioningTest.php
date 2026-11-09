<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('creates the first administrator with a prompted password', function () {
    $this->artisan('app:create-admin', [
        'name' => 'Admin Hubin',
        'email' => 'admin@example.com',
    ])
        ->expectsQuestion('Password admin', 'LongPassword!123')
        ->expectsQuestion('Ulangi password admin', 'LongPassword!123')
        ->assertExitCode(0);

    $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();

    expect($admin->role)->toBe('admin_hubin')
        ->and(Hash::check('LongPassword!123', $admin->password))->toBeTrue();
});

it('does not create another administrator through the bootstrap command', function () {
    User::factory()->create(['role' => 'admin_hubin']);

    $this->artisan('app:create-admin', [
        'name' => 'Second Admin',
        'email' => 'second-admin@example.com',
    ])->assertExitCode(1);

    $this->assertDatabaseMissing('users', ['email' => 'second-admin@example.com']);
});
