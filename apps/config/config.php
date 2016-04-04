<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT');
$config = array(
    'host' => '103.52.2.6',
    'username' => 'monitoring',
    'password' => 'beritagar',
    'logo'     => 'https://beritagar.id/images/logo-beritagar.svg',
    'template' => 'desktop',
    'server'   => 'Beritagar'
);
