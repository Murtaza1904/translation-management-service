<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\Locale;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ExportController extends Controller
{
    public function export(Request $request, string $localeCode)
    {
        $locale = Cache::rememberForever("locale.$localeCode", fn () => Locale::where('code', $localeCode)->firstOrFail());

        $etag = Cache::tags(['translations'])->remember("etag.$locale->id", 60, function () use ($locale) {
            $max = $locale->translations()->max('updated_at');
            return sha1($locale->id.'|'.$max);
        });

        if ($request->headers->get('If-None-Match') === $etag) {
            return response()->noContent(304)->setEtag($etag);
        }

        $response = new StreamedResponse(function () use ($locale) {
            echo '{"locale":"'.$locale->code.'","data":{';
            $first = true;
            $locale->translations()->select(['key', 'value'])->orderBy('key')->chunk(2000, function ($chunk) use (&$first) {
                foreach ($chunk as $row) {
                    if (!$first) {
                        echo ',';
                    }
                    echo json_encode($row->key).':'.json_encode($row->value, JSON_UNESCAPED_UNICODE);
                    $first = false;
                }
            });
            echo '}}';
        });

        $response->headers->set('Content-Type', 'application/json');
        $response->setEtag($etag);
        $response->headers->set('Cache-Control', 'public, max-age=0, must-revalidate, s-maxage=0');
        $response->headers->set('X-Accel-Buffering', 'no');
        return $response;
    }
}
