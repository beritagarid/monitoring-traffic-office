<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT');
$config = array(
    'host' => 'localhost',
    'username' => 'username',
    'password' => 'password',
    'logo'     => 'https://beritagar.id/images/logo-beritagar.svg',
    'template' => 'desktop',
    'server'   => 'Beritagar'
);
