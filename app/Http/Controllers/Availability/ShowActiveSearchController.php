<?php

declare(strict_types=1);

namespace App\Http\Controllers\Availability;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final readonly class ShowActiveSearchController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): Response
    {
        return Inertia::render('ActiveSearch/Index', [
            'googleMapsApiKey' => config('services.google.maps.api_key'),
        ]);
    }
}
