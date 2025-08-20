<?php

use App\Models\Locale;
use App\Models\Translation;

it('export stream and etag', function (): void {
    $login = $this->postJson('/api/v1/auth/login', [
        'email' => 'johndoe1@example.com',
        'password' => 'Pa$$w0rd1#$',
    ]);

    $token = $login->json('token');
    expect($token)->not->toBeNull();

    $loc = Locale::first();
    Translation::create(['locale_id' => $loc->id, 'key' => 'a', 'value' => 'b']);

    $first = $this
        ->withHeader('Authorization', 'Bearer ' . $token)
        ->get('/api/v1/export/en');

    $etag = $first->headers->get('ETag');
    expect($etag)->not->toBeEmpty();

    $second = $this
        ->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'If-None-Match' => $etag,
        ])
        ->get('/api/v1/export/en');

    $second->assertStatus(304);
});
