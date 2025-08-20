<?php

declare(strict_types=1);

beforeAll(function () {
    $response = test()->postJson('/api/v1/auth/login', [
        'email' => 'johndoe1@example.com',
        'password' => 'Pa$$w0rd1#$',
    ]);
    
    $this->headers = ['Authorization' => 'Bearer '. $response->json('token')];
    $this->translationId = null;
});

it('creates a translation', function (): void {
    $time = microtime(true);

    $response = $this
        ->withHeaders($this->headers)
        ->postJson('/api/v1/translations', [
            'locale' => 'en',
            'key' => 'greeting.hello',
            'value' => 'Hello',
            'tags' => ['web'],
        ]);

    $this->assertTrue((microtime(true) - $time) * 1000 < 200);

    $response->assertCreated();

    $this->translationId = $response->json('id');
});

it('views a translation', function (): void {
    $time = microtime(true);

    $response = $this
        ->withHeaders($this->headers)
        ->getJson('/api/v1/translations/' . $this->translationId);

    $this->assertTrue((microtime(true) - $time) * 1000 < 200);

    $translation = $response->json();

    expect($translation)
        ->key->toBe('greeting.hello')
        ->value->toBe('Hello')
        ->locale->code->toBe('en');

    expect(array_column($translation['tags'], 'name'))->toContain('web');

    expect($translation['id'])->not->toBeEmpty();
    expect($translation['namespace'])->not->toBeEmpty();

    $response->assertOk();
});

it('edits a translation', function (): void {
    $time = microtime(true);

    $response = $this
        ->withHeaders($this->headers)
        ->putJson('/api/v1/translations/' . $this->translationId, [
            'locale' => 'en',
            'key' => 'greeting.hello',
            'value' => 'Hello World',
            'tags' => ['web'],
        ]);

    $this->assertTrue((microtime(true) - $time) * 1000 < 200);

    $response->assertOk();
});

it('deletes a translation', function (): void {
    $time = microtime(true);

    $response = $this
        ->withHeaders($this->headers)
        ->deleteJson('/api/v1/translations/' . $this->translationId);

    $this->assertTrue((microtime(true) - $time) * 1000 < 200);

    $response->assertNoContent();
});
