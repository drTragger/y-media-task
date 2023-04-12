<?php

namespace App\Http\Middleware;

use App\Services\UserInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Closure;
use Symfony\Component\HttpFoundation\Response;

class JWTMiddleware
{
    public function __construct(
        private readonly UserInterface $userService
    ) {
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (!$request->hasHeader('Authorization')) {
            return response()->json([
                'status' => false,
                'message' => 'JWT token not found.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $decodedToken = JWT::decode(
            $request->header('Authorization'),
            new Key(config('security.token.secret'), config('security.token.algorithm'))
        );

        if (empty($decodedToken->sub)) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $user = $this->userService->getUserById($decodedToken->sub);

        if (empty($user)) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $request->merge(['user' => $user]);

        return $next($request);
    }
}
