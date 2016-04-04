<?php
namespace Beritagar;

class Beritagar{

    public static $config;

    public function __construct($config = array())
    {
        self::$config = $config;
        date_default_timezone_set('Asia/Jakarta');
    }

    public function template(){
        return self::$config['template'];
    }

    public function render($view_file = '',$data = array()){
        $loader = new \Twig_Loader_Filesystem('../apps/views');
        $twig = new \Twig_Environment($loader, array());

        return $twig->render($this->template().$view_file,$data);
    }

}