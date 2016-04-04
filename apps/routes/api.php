<?php

$app->get('/home',function (\Slim\Http\Request $request, \Slim\Http\Response $response, $args) use ($app,$beritagar){
    $controller = new \HomeController($beritagar);
    $response = $response->withHeader('Content-type', 'application/json');

    $key_cache = 'api-home-json';
    if(@$_GET['clear'] == 'clear'){
        $beritagar->cache()->clear($key_cache);
    }

    if($beritagar->cache()->has($key_cache)){
        $return = $beritagar->cache()->get($key_cache);
        $response->withStatus(200);
        $return['cache'] = true;
        return json_encode($return);
    }else{
        $return = $controller->home();
        $beritagar->cache()->set($key_cache,$return,600);
        $response->withStatus(200);
        $return['cache'] = false;
        return json_encode($return);
    }
});

$app->get('/mikrotik/listip',function (\Slim\Http\Request $request, \Slim\Http\Response $response, $args) use ($app,$beritagar){
    $controller = new \MikrotikController($beritagar);
    $response = $response->withHeader('Content-type', 'application/json');

    $return = $controller->listIp();
    $view = json_encode($return);
    return $response->write($view);
});

$app->get('/lihat',function (\Slim\Http\Request $request, \Slim\Http\Response $response, $args) use ($app,$beritagar){
    $view = $beritagar->render('/hasil.twig');
    return $response->write($view);
});

$app->get('/mikrotik',function (\Slim\Http\Request $request, \Slim\Http\Response $response, $args) use ($app,$beritagar){
    $controller = new \MikrotikController($beritagar);
    $response = $response->withHeader('Content-type', 'application/json');

    $return = $controller->index();
    $view =  json_encode($return);
    return $response->write($view);
});






