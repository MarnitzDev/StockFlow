<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        \Log::info('Session ID: ' . $request->session()->getId());
        \Log::info('CSRF Token: ' . $request->session()->token());

        return [
            ...parent::share($request),
            'csrf_token' => $request->session()->token(),
            'auth' => [
                'user' => $request->user(),
            ],
        ];
    }
}
