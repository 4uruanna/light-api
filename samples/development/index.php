<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use _4uruanna\LightApi\Api;
use Slim\Routing\RouteCollectorProxy;

use function DI\create;

$_SERVER['ENVIRONMENT'] = 'DEVELOPMENT';

class Foo { public function hello() { return 'hello world!'; } }

Api::$definitions[Foo::class] = create()->constructor();

Api::$routes[] = function (RouteCollectorProxy $group) {
    $group->get('/hello', function ($request, $response, Foo $foo) {
        $response->getBody()->write($foo->hello());
        return $response;
    });
};

Api::main();
