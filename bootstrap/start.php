<?php
$container = new \Slim\Container();
require __DIR__ . '/../apps/config/handler.php';
$app = new \Slim\App($container);
$app->add(new \Slim\HttpCache\Cache('public', 86400));
require __DIR__ . '/../apps/config/routes.php';