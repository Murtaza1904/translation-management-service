<?php

declare(strict_types=1);

it('can register', function (): void {
    $name = fake()->name();
    $email = fake()->email();

    $response = $this->postJson('/api/v1/auth/register', [
        'name' => $name,
        'email' => $email,
        'password' => 'Pa$$w0rd1#$',
        'password_confirmation' => 'Pa$$w0rd1#$',
    ]);

    $response->assertStatus(201);

    expect($response->json('token'))->not->toBeNull();
    expect($response->json('message'))->toBe('User registered successfully');
    expect($response->json('user'))
        ->name->toBe($name)
        ->email->toBe($email);

    $_SESSION['token'] = $response->json('token');
});

it('can logout', function (): void {
    $response = $this
        ->withHeaders(['Authorization' => 'Bearer ' . $_SESSION['token']])
        ->postJson('/api/v1/auth/logout');

    $response->assertOk();

    expect($response->json('message'))->toBe('Logged out successfully');
});

it('can login', function (): void {
    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'johndoe@example.com',
        'password' => 'Pa$$w0rd1#$',
    ]);

    $response->assertOk();

    expect($response->json('token'))->not->toBeNull();
    expect($response->json('user'))->not->toBeNull();
    expect($response->json('user'))
        ->name->toBe('John Doe')
        ->email->toBe('johndoe@example.com');

    $_SESSION['token'] = $response->json('token');
});
