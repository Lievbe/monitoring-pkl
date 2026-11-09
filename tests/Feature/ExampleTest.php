<?php

it('returns a successful response', function () {
    $response = $this->get('/');

    $response->assertOk()
        ->assertSee('PKL lebih terarah')
        ->assertSee('Buat akun')
        ->assertSee(route('login'))
        ->assertSee(route('register'));
});
