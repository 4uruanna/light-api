<?php

namespace _4uruanna\LightApi;

use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Server\MiddlewareInterface;

class CorsMiddleware implements MiddlewareInterface
{
    private const DEFAULT_CORS_ORIGIN = '*';
    private const DEFAULT_CORS_HEADERS = '*';
    private const DEFAULT_CORS_METHODS = 'GET, POST, PUT, PATCH, DELETE, OPTIONS';

    private readonly ResponseFactory $factory;

    public function __construct(ResponseFactory $responseFactory)
    {
        $this->factory = $responseFactory;
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        $options = $request->getMethod() === 'OPTIONS';
        $response = ($options ? $this->factory->createResponse() : $handler->handle($request))
            ->withHeader('Access-Control-Allow-Credentials', 'true')
            ->withHeader('Access-Control-Allow-Origin', $_SERVER['CORS_ORIGIN'] ?? self::DEFAULT_CORS_ORIGIN)
            ->withHeader('Access-Control-Allow-Headers', $_SERVER['CORS_HEADERS'] ?? self::DEFAULT_CORS_HEADERS)
            ->withHeader('Access-Control-Allow-Methods', $_SERVER['CORS_METHODS'] ?? self::DEFAULT_CORS_METHODS)
            ->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->withHeader('Pragma', 'no-cache');

        if (ob_get_contents()) {
            ob_clean();
        }

        return $response;
    }
}
