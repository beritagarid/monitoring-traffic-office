<?php
/**
 * Created by PhpStorm.
 * User: kunbudiharta
 * Date: 4/4/16
 * Time: 11:38 PM
 */

$app->get('/',function (\Slim\Http\Request $request, \Slim\Http\Response $response, $args) use ($app,$beritagar){
    $data = $beritagar::$config;
    $view = $beritagar->render('/index2.twig', $data);
    return $response->write($view);
});

$app->get('/divisi/{divisi}/{name}',function (\Slim\Http\Request $request, \Slim\Http\Response $response, $args) use ($app,$beritagar){
    $data = $beritagar::$config;
    $args['divisi'] = str_replace('-', '.', $args['divisi']);
    if(!isset($args['divisi'])){
        $view = "divisi not set";
    }else{
        $data['name'] = $args['name'];
        $view = $beritagar->render('/divisi.twig',$data);
    }

    return $response->write($view);
});