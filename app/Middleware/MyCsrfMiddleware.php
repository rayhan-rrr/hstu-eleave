<?php

namespace App\Middleware;

use Slim\Csrf\Guard;


class MyCsrfMiddleware extends Guard
{
    // This method is processing every request in our application
    public function processRequest($request, $response, $next) {
        $route = $request->getAttribute('route');

        $uri = $request->getUri()->getPath();
        $uriArray = explode('/', $uri);

        if ($uriArray[1] == 'public') {

            // If it is - just pass request-response to the next callable in chain
            return $next($request, $response);

        } else {

            // else apply __invoke method that you've inherited from \Slim\Csrf\Guard
            return $this($request, $response, $next);
        
        }
    }
}