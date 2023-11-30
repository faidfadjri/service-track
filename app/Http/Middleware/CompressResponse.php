<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CompressResponse
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($response instanceof Response) {
            $content = $response->getContent();
            $gzippedContent = gzencode($content, 9);
            $response->setContent($gzippedContent);

            $response->header('Content-Encoding', 'gzip');
            $response->header('Content-Length', strlen($gzippedContent));
        }

        return $response;
    }
}
