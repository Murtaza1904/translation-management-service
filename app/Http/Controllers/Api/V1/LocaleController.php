<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\Locale;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LocaleRequest;
use App\Http\Resources\Api\V1\LocaleResource;

final class LocaleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $locales = Locale::query()
                    ->search($request->input('search'))
                    ->latest()
                    ->paginate($request->input('per_page', 10));

        return response()->json([
            'locales' => LocaleResource::collection($locales),
            'meta' => [
                'current_page' => $locales->currentPage(),
                'last_page' => $locales->lastPage(),
                'per_page' => $locales->perPage(),
                'total' => $locales->total(),
            ],
        ]);
    }

    /**
     * Store a new locale.
     *
     * @param LocaleRequest $request
     * @return JsonResponse
     */
    public function store(LocaleRequest $request): JsonResponse
    {
        $locale = Locale::create($request->validated());

        return response()->json([
            'message' => 'Locale created successfully',
            'locale' => new LocaleResource($locale),
        ], 201);
    }

    /**
     * Show a specific locale.
     *
     * @param Locale $locale
     * @return JsonResponse
     */
    public function show(Locale $locale): JsonResponse
    {
        return response()->json([
            'locale' => new LocaleResource($locale),
        ]);
    }

    /**
     * Update a specific locale.
     *
     * @param LocaleRequest $request
     * @param Locale $locale
     * @return JsonResponse
     */
    public function update(LocaleRequest $request, Locale $locale): JsonResponse
    {
        $locale->update($request->validated());

        return response()->json([
            'message' => 'Locale updated successfully',
            'locale' => new LocaleResource($locale->refresh()),
        ]);
    }

    /**
     * Delete a specific locale.
     *
     * @param Locale $locale
     * @return JsonResponse
     */
    public function destroy(Locale $locale): JsonResponse
    {
        $locale->delete();

        return response()->json([
            'message' => 'Locale deleted successfully',
        ]);
    }
}
