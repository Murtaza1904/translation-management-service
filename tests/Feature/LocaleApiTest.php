<?php

declare(strict_types=1);


it('get locales', function (): void {
    $response = $this->withHeaders(['Authorization' => 'Bearer ' . $_SESSION['token']])
        ->getJson('/api/v1/locales');

    $response->assertOk();
});

it('creates a locale', function (): void {
    $code = fake()->unique()->languageCode();
    $name = \Locale::getDisplayLanguage($code, 'en');

    $response = $this->withHeaders(['Authorization' => 'Bearer ' . $_SESSION['token']])
        ->postJson('/api/v1/locales', [
            'code' => $code,
            'name' => $name,
        ]);

    $response->assertCreated();

    $locale = $response->json('locale');

    expect($response->json('message'))->toBe('Locale created successfully');
    expect($locale['code'])->toBe($code);
    expect($locale['name'])->toBe($name);

    $_SESSION['locale_id'] = $response->json('locale.id');
});

it('views a locale', function (): void {
    $response = $this
        ->withHeaders(['Authorization' => 'Bearer ' . $_SESSION['token']])
        ->getJson('/api/v1/locales/' . $_SESSION['locale_id']);

    $locale = $response->json('locale');

    expect($locale['code'])->not->toBeEmpty();
    expect($locale['name'])->not->toBeEmpty();
    expect($locale['id'])->not->toBeEmpty();

    $response->assertOk();
});

it('edits a locale', function (): void {
    $code = fake()->unique()->languageCode();
    $name = \Locale::getDisplayLanguage($code, 'en');

    $response = $this
        ->withHeaders(['Authorization' => 'Bearer ' . $_SESSION['token']])
        ->putJson('/api/v1/locales/' . $_SESSION['locale_id'], [
            'code' => $code,
            'name' => $name,
        ]);

    $response->assertOk();

    expect($response->json('message'))->toBe('Locale updated successfully');

    $locale = $response->json('locale');

    expect($locale['code'])->toBe($code);
    expect($locale['name'])->toBe($name);
    expect($locale['id'])->toBe($_SESSION['locale_id']);
});

it('deletes a locale', function (): void {
    $response = $this
        ->withHeaders(['Authorization' => 'Bearer ' . $_SESSION['token']])
        ->deleteJson('/api/v1/locales/' . $_SESSION['locale_id']);

    $response->assertOk();

    expect($response->json('message'))->toBe('Locale deleted successfully');

    unset($_SESSION['locale_id']);
});

it('benchmarks locales five times under 200ms', function (): void {
    for ($i = 1; $i <= 5; $i++) {
        $start = microtime(true);

        $response = $this
            ->withHeaders(['Authorization' => 'Bearer ' . $_SESSION['token']])
            ->getJson('/api/v1/locales');

        $elapsed = (microtime(true) - $start) * 1000;

        $response->assertOk();
        expect($elapsed)->toBeLessThan(200, "Run #{$i} took {$elapsed}ms");
    }
});
