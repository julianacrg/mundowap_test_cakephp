<?php

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

return static function (RouteBuilder $routes) {
    
    $routes->setRouteClass(DashedRoute::class);

    Router::scope('/', function ($routes) {
        $routes->resources('Stores');
    });  
};
