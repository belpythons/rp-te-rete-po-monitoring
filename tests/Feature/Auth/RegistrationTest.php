<?php

test('registration screen returns 404 as registration is disabled', function () {
    $response = $this->get('/register');

    $response->assertStatus(404);
});

test('submitting registration returns 404 as registration is disabled', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertStatus(404);
    $this->assertGuest();
});
