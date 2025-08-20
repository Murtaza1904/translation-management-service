<?php

declare(strict_types=1);

beforeAll(function () {
    $this->token = null;
    $this->headers = [];
});

it('can register', function (): void {
    $response = $this->postJson('/api/v1/auth/register', [
        'name' => 'John Doe',
        'email' => fake()->email(),
        'password' => 'Pa$$w0rd1#$',
        'password_confirmation' => 'Pa$$w0rd1#$',
    ]);

    $response->assertStatus(201);

    $this->token = $response->json('token');
    $this->headers = ['Authorization' => 'Bearer ' . $this->token];

    expect($this->token)->not->toBeNull();
});

it('can logout', function (): void {
    $response = $this
        ->withHeaders($this->headers)
        ->postJson('/api/v1/auth/logout');

    $response->assertStatus(200);

    expect($response->json('message'))->toBe('Logged out successfully');
});

it('can login', function (): void {
    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'johndoe1@example.com',
        'password' => 'Pa$$w0rd1#$',
    ]);

    $response->assertStatus(200);

    $this->token = $response->json('token');
    $this->headers = ['Authorization' => 'Bearer ' . $this->token];

    expect($this->token)->not->toBeNull();
});
