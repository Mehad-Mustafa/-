<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateSignedVideoUrl
{
    /**
     * التحقق من توقيع رابط الفيديو
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->hasValidSignature()) {
            abort(403, 'انتهت صلاحية رابط الفيديو، يرجى تحديث الصفحة');
        }

        return $next($request);
    }
}
