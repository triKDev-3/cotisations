<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\NeonAuthService;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class NeonAuthMiddleware
{
    protected NeonAuthService $neonAuth;

    public function __construct(NeonAuthService $neonAuth)
    {
        $this->neonAuth = $neonAuth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        
        if (!$token) {
            return response()->json(['message' => 'Unauthorized. No token provided.'], 401);
        }

        $decoded = $this->neonAuth->verifyToken($token);
        
        if (!$decoded) {
            return response()->json(['message' => 'Unauthorized. Invalid token.'], 401);
        }

        // Logic to link token claim to local User model.
        // Assuming 'sub' is the unique identifier and 'email' is also available.
        $userId = $decoded['sub'];
        $email = $decoded['email'] ?? null;

        $user = User::where('email', $email)->first();
        
        if (!$user) {
            // Ideally should create one here or return error if using purely local DB.
            // For now, let's auto-create as part of "preparation".
            $user = User::create([
                'name' => $decoded['name'] ?? 'U' . substr($userId, 0, 8),
                'email' => $email,
                'password' => bcrypt(\Illuminate\Support\Str::random(16)), // Placeholder as we use external auth
                'numero_compte' => User::generateUniqueNumeroCompte(),
                'role' => 'user',
            ]);
        }

        // Standard Laravel login for the request duration
        Auth::login($user);

        return $next($request);
    }
}
