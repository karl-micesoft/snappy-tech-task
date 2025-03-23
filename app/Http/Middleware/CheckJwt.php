<?php

namespace App\Http\Middleware;

use Closure;
use DateTimeImmutable;
use Illuminate\Http\Request;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;

class CheckJwt
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $auth = $request->header('Authorization');

        if (!is_string($auth) || !str_starts_with($auth, 'Bearer ')) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $parser = new Parser(new JoseEncoder());
        $token = $parser->parse(substr($auth, 7));

        if ($token->isExpired(new DateTimeImmutable())) {
            return response()->json(['message' => 'Token expired'], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
