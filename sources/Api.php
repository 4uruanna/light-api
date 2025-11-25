<?php

namespace _4uruanna\LightApi;

use DI\Bridge\Slim\Bridge;
use DI\Container;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\App;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Routing\RouteCollectorProxy;

final class Api
{
    private const string ROUTES_FILENAME = 'routes.php';
    private const string DI_DIRECTORY = 'di';
    private const string DI_PROXIES_DIRECTORY = 'proxies';

    public static array $definitions = [];
    public static array $middlewares = [];
    public static array $routes = [];

    private static string $environment;
    private static App $application;
    private static bool $cache;
    private static string $cacheDirectory;

    public static function main(): App
    {
        self::$environment = $_SERVER['ENVIRONMENT'] ?? 'DEVELOPMENT';
        self::$cache = self::$environment === 'PRODUCTION' && isset($_SERVER['CACHE_DIRECTORY']);
        self::$cacheDirectory = self::$cache ? $_SERVER['CACHE_DIRECTORY'] . DIRECTORY_SEPARATOR : '';
        
        self::timezone();

        $container = self::container();

        self::$application = Bridge::create($container);
        
        self::middlewares();

        self::routes();

        self::$application->run();

        return self::$application;
    }

    protected static function timezone(): void
    {
        if(isset($_SERVER['DATE_TIMEZONE'])) {
            date_default_timezone_set($_SERVER['DATE_TIMEZONE']);
        }
    }

    protected static function container(): Container
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions(self::$definitions);
        $builder->addDefinitions([ResponseFactoryInterface::class => fn (ContainerInterface $container) => $container->get(ResponseFactory::class)]);

        if(self::$cache) {
            $builder->enableCompilation(self::$cacheDirectory . self::DI_DIRECTORY);
            $builder->writeProxiesToFile(true, self::$cacheDirectory . self::DI_PROXIES_DIRECTORY);
        }

        return $builder->build();
    }

    protected static function middlewares(): void
    {
        self::$application->addBodyParsingMiddleware();
        self::$application->addRoutingMiddleware();

        for($i = 0; $i < count(self::$middlewares); $i++) {
            self::$middlewares[$i](self::$application);
        }

        if(isset($_SERVER['ALLOW_CORS']) && $_SERVER['ALLOW_CORS'] === true) {
            self::$application->add(CorsMiddleware::class);
        }
    }

    protected static function routes(): void
    {
        self::$application->group('/api', function (RouteCollectorProxy $group) {
            for($i = 0; $i < count(self::$routes); $i++) {
                self::$routes[$i]($group);
            }
        });

        if(self::$cache) {
            $collector = self::$application->getRouteCollector();
            $collector->setCacheFile(self::$cacheDirectory . self::ROUTES_FILENAME);
        }
    }
}