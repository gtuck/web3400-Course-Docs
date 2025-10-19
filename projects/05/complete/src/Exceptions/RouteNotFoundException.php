<?php
/**
 * RouteNotFoundException
 *
 * PURPOSE:
 * Custom exception thrown when a requested route is not registered in the router.
 * Provides better error handling and debugging compared to generic exceptions.
 *
 * USAGE:
 * ```php
 * throw new RouteNotFoundException('GET', '/non-existent-page');
 * ```
 *
 * HTTP STATUS CODE: 404 Not Found
 */

namespace App\Exceptions;

class RouteNotFoundException extends \Exception
{
    /**
     * Create a new RouteNotFoundException
     *
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $uri The requested URI
     * @param int $code HTTP status code (default: 404)
     * @param \Throwable|null $previous Previous exception for exception chaining
     */
    public function __construct(
        string $method,
        string $uri,
        int $code = 404,
        ?\Throwable $previous = null
    ) {
        $message = "No route found for {$method} {$uri}";
        parent::__construct($message, $code, $previous);
    }
}
