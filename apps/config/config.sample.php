<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT');
$config = array(
    'host' => '127.0.0.1',
    'username' => 'api_username',
    'password' => 'api_password',
    'logo'     => 'https://beritagar.id/images/logo-beritagar.svg',
    'template' => 'desktop',
    'server'   => 'Beritagar',
    'interface' => 'ether1_WAN,ether2_LAN',
    'interface_monitor' => true, // Change false to unmonitor interface
    'socket_host' => 'http://localhost:8081', // Change with your socket listen address
    'localhost' => 'http://localhost:4000'
);
