<?php

declare(strict_types=1);

use App\Models\Tag;
use App\Models\Locale;

it('get translations', function (): void {
    $response = $this->withHeaders(['Authorization' => 'Bearer ' . $_SESSION['token']])
        ->getJson('/api/v1/translations');

    $response->assertOk();
});

it('creates a translation', function (): void {
    $locales = Locale::pluck('code')->toArray();
    $tags = Tag::pluck('name')->toArray();

    $response = $this->withHeaders(['Authorization' => 'Bearer ' . $_SESSION['token']])
        ->postJson('/api/v1/translations', [
            'locale' => array_rand(array_flip($locales)),
            'key' => fake()->unique()->word(),
            'value' => fake()->sentence(),
            'tags' => [array_rand(array_flip($tags))],
        ]);

    $response->assertCreated();

    expect($response->json('message'))->toBe('Translation created successfully');
    $_SESSION['translation_id'] = $response->json('translation.id');
});

it('views a translation', function (): void {
    $response = $this
        ->withHeaders(['Authorization' => 'Bearer ' . $_SESSION['token']])
        ->getJson('/api/v1/translations/' . $_SESSION['translation_id']);

    $translation = $response->json('translation');

    expect($translation['key'])->not->toBeEmpty();
    expect($translation['value'])->not->toBeEmpty();
    expect($translation['locale']['code'])->not->toBeEmpty();
    expect(array_column($translation['tags'], 'name'))->not->toBeEmpty();
    expect($translation['id'])->not->toBeEmpty();
    expect($translation['namespace'])->toBeNull();

    $response->assertOk();
});

it('edits a translation', function (): void {
    $tags = Tag::pluck('name')->toArray();

    $value = fake()->sentence();
    $tags = array_rand(array_flip($tags), 1);

    $response = $this
        ->withHeaders(['Authorization' => 'Bearer ' . $_SESSION['token']])
        ->putJson('/api/v1/translations/' . $_SESSION['translation_id'], [
            'value' => $value,
            'tags' => [$tags],
        ]);

    $response->assertOk();

    expect($response->json('message'))->toBe('Translation updated successfully');

    $translation = $response->json('translation');

    expect($translation['value'])->toBe($value);
    expect(array_column($translation['tags'], 'name'))->toContain($tags);
    expect($translation['id'])->not->toBeEmpty();
    expect($translation['namespace'])->toBeNull();
});

it('deletes a translation', function (): void {
    $response = $this
        ->withHeaders(['Authorization' => 'Bearer ' . $_SESSION['token']])
        ->deleteJson('/api/v1/translations/' . $_SESSION['translation_id']);

    $response->assertOk();

    expect($response->json('message'))->toBe('Translation deleted successfully');

    unset($_SESSION['translation_id']);
});

it('benchmarks translations five times under 500ms', function (): void {
    for ($i = 1; $i <= 5; $i++) {
        $start = microtime(true);

        $response = $this
            ->withHeaders(['Authorization' => 'Bearer ' . $_SESSION['token']])
            ->getJson('/api/v1/translations');

        $elapsed = (microtime(true) - $start) * 1000;

        $response->assertOk();
        expect($elapsed)->toBeLessThan(500, "Run #{$i} took {$elapsed}ms");
    }
});
