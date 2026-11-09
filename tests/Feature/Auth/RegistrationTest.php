<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'jurusan' => 'Rekayasa Perangkat Lunak',
        'kelas' => 'XII RPL 1',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'admin_hubin',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
        'role' => 'siswa_pkl',
    ]);
    $this->assertDatabaseHas('siswa_profiles', [
        'nama_lengkap' => 'Test User',
        'jurusan' => 'Rekayasa Perangkat Lunak',
        'kelas' => 'XII RPL 1',
    ]);
});

test('student registration requires the academic profile fields', function () {
    $response = $this->from('/register')->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors(['jurusan', 'kelas']);
    $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
});
