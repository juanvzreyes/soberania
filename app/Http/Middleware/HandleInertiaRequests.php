<?php

namespace App\Http\Middleware;

use App\Http\Resources\UserResource;
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
        return [
            ...parent::share($request),
            'cartCount' => function () {
                return count(session()->get('cart', []));
            },
            'auth' => [
                'user'  => $request->user() ? new UserResource($request->user()) : null,
                'roles' => $request->user() ? $request->user()->getRolesArray() : [],
                'can' => $request->user() ? $request->user()->getPermissionArray() : [],
            ],
            'flash' => function () use ($request) {
                foreach (['success', 'error', 'danger', 'warning', 'info', 'isBanned'] as $key) {
                    if ($message = $request->session()->get($key)) {
                        return [
                            'type' => $key,
                            'message' => $message,
                        ];
                    }
                }
                return null;
            },
        ];
    }
}
