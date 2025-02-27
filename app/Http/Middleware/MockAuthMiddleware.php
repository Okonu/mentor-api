<?php

namespace App\Http\Middleware;

use App\Repositories\Interfaces\UserRepositoryInterface;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MockAuthMiddleware
{
    public function __construct(protected UserRepositoryInterface $userRepository)
    {
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userId = $request->header('X-User-ID');

        if (!$userId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Authentication required. Please provide X-User-ID header.',
            ], 401);
        }

        $user = $this->userRepository->findById($userId);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid user ID.',
            ], 401);
        }

        $request->attributes->set('user', $user);

        return $next($request);
    }
}
