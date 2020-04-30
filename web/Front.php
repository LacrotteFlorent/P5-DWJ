<?php

require_once __DIR__.'/vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\ErrorHandler\ErrorHandler;
use Symfony\Component\ErrorHandler\DebugClassLoader;
use Symfony\Component\Routing;

/**
 * @source https://symfony.com/doc/current/components/error_handler.html
 * @note Error Handler
 */
if ($_ENV['DEV'] === 'On') {
    Debug::enable();
    ErrorHandler::register();
    DebugClassLoader::enable();
}

$request = Request::createFromGlobals();
$routes = new Routing\RouteCollection();

$context = new Routing\RequestContext();
$context->fromRequest($request);
$matcher = new Routing\Matcher\UrlMatcher($routes, $context);

//$routes->add('hello', new Routing\Route('/hello/{name}', [
//    'name' => 'World',
//    '_controller' => function ($request) {
//        // $foo will be available in the template
//        $request->attributes->set('foo', 'bar');
//
//        $response = render_template($request);
//
//        // change some header
//        $response->headers->set('Content-Type', 'text/plain');
//
//        return $response;
//    }
//]));

try {
    $request->attributes->add($matcher->match($request->getPathInfo()));
    $response = call_user_func($request->attributes->get('_controller'), $request);
} catch (Routing\Exception\ResourceNotFoundException $exception) {
    $response = new Response('Not Found', 404);
} catch (Exception $exception) {
    $response = new Response('An error occurred', 500);
}

$response->send();

function render_template($request)
{
    extract($request->attributes->all(), EXTR_SKIP);
    ob_start();
    include sprintf(__DIR__.'/%s.php', $_route);

    return new Response(ob_get_clean());
}