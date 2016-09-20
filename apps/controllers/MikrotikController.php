<?php
use PEAR2\Net\RouterOS;

class MikrotikController {
    private static $beritagar;

    public function __construct($beritagar)
    {
        self::$beritagar = $beritagar;
    }

    public function index(){
	$client = new RouterOS\Client(self::$beritagar['host'], self::$beritagar['username'], self::$beritagar['password']);
        $request = new RouterOS\Request('/queue simple print');

        $responses = $client->sendSync($request);
        $data = array();

        foreach ($responses as $key => $response) {
            foreach ($response as $name => $value) {
                if($name == 'name' || $name == 'target' || $name == 'parent' || $name == 'bytes' || $name == 'rate' || $name == 'max-limit'){
                    if($name == 'bytes' || $name == 'rate' || $name == 'max-limit'){
                        if($name == 'bytes'){
                            $name = 'traffic';
                        }
                        if($name == 'max-limit'){
                            $name = 'limit';
                        }
                        $data[$key][$name] = self::convert($value);
                    }else{
                        $data[$key][$name] = $value;
                    }

                }
            }
        }

        $data_new = array();
        foreach($data as $k => $v){
            $str = $v['name'];
            preg_match('/(OIX|IX)-(\w+)/',$str,$match);
            if(!isset($match[2])){
                $match[2] = $str;
            }
            if(!isset($match[1])){
                $match[1] = $str;
            }

            $str_parent = $v['parent'];
            preg_match('/(OIX|IX)-(\w+)/',$str_parent,$parent);
            if(!isset($parent[2])){
                $parent[2] = $str_parent;
            }

            $data_new[strtoupper($parent[2])][strtoupper($match[2])][strtoupper($match[1])] = $v;
            
            if( $v['rate']['data']['upload-byte'] == 0){
                $avg_upload = 0;
            }else{
                $avg_upload = ($v['rate']['data']['upload-byte'] / $v['limit']['data']['upload-byte'] * 100);
            }

            if( $v['rate']['data']['download-byte'] == 0){
                $avg_download = 0;
            }else{
                $avg_download = ($v['rate']['data']['download-byte'] / $v['limit']['data']['download-byte'] * 100);    
            }

            $data_new[strtoupper($parent[2])][strtoupper($match[2])][strtoupper($match[1])]['avg']['upload'] = (int) $avg_upload;
            $data_new[strtoupper($parent[2])][strtoupper($match[2])][strtoupper($match[1])]['avg']['download'] = (int) $avg_download;
        }

        return $data_new;
    }
    public function listIp($divisi = null){
	$client = new RouterOS\Client(self::$beritagar['host'], self::$beritagar['username'], self::$beritagar['password']);
      	$request = new RouterOS\Request('/ip dhcp-server lease print');

        $responses = $client->sendSync($request);
        $data = array();
        foreach ($responses as $key => $response) {
            foreach ($response as $name => $value) {
                if($name == 'address' || $name == 'mac-address' || $name == 'comment'){
                    if($name == 'address'){
                        $ip = explode('.', $value);
                    }
                    $value = str_replace(array('=','>','<',':'), array('','','',''), $value);
                    $data[$ip[0].'.'.$ip[1].'.'.$ip[2]][$key][$name] = $value;    
                }
                
            }
        }
        if(!empty($divisi)){
            return array('data' => $data[$divisi]);    
        }else{
            return $data;
        }
        
    }

    public static function convert($data = null){
        $data = explode('/',$data);
        $upload = $data[0];
        $download = $data[1];

        if($upload/1024/1024/1024/1024 > 1){
            $upload = number_format($upload/1024/1024/1024/1024,2) .' TB';
        }elseif($upload/1024/1024/1024 > 1){
            $upload = number_format($upload/1024/1024/1024,2) .' GB';
        }elseif($upload/1024/1024 > 1){
            $upload = number_format($upload/1024/1024,2) .' MB';
        }elseif($upload/1024 > 1){
            $upload = number_format($upload/1024,2) .' KB';
        }elseif($upload > 0){
            $upload = number_format($upload,2).' B';
        }

        if($download/1024/1024/1024/1024 > 1){
            $download = number_format($download/1024/1024/1024/1024,2) .' TB';
        }elseif($download/1024/1024/1024 > 1){
            $download = number_format($download/1024/1024/1024,2) .' GB';
        }elseif($download/1024/1024 > 1){
            $download = number_format($download/1024/1024,2) .' MB';
        }elseif($download/1024 > 1){
            $download = number_format($download/1024,2) .' KB';
        }elseif($download > 1){
            $download = number_format($download,2).' B';
        }
        $result['data'] = [
            'upload'    => $upload,
            'download'  => $download,
            'upload-byte' => (int)$data[0],
            'download-byte' => (int)$data[1],
        ];
        return $result;
    }
}

    public function convertBPS($download = null){

        if($download/1024/1024/1024/1024 > 1){
            $download = number_format($download/1024/1024/1024/1024,2) .'TB';
        }elseif($download/1024/1024/1024 > 1){
            $download = number_format($download/1024/1024/1024,2) .'GB';
        }elseif($download/1024/1024 > 1){
            $download = number_format($download/1024/1024,2) .'MB';
        }elseif($download/1024 > 1){
            $download = number_format($download/1024,2) .'KB';
        }elseif($download > 1){
            $download = number_format($download,2).'B';
        }
        
        return $download;
    }

    public function monitoringInterface(){
        if(self::$beritagar['interface_monitor']){
            $client = new RouterOS\Client(self::$beritagar['host'], self::$beritagar['username'], self::$beritagar['password']);

            $srequest = new RouterOS\Request('/interface monitor-traffic interface='.self::$beritagar['interface'].' once');
            $responses = $client->sendSync($srequest);

            
            foreach ($responses as $key => $response) {
                $data = array();
                foreach ($response as $name => $value) {
                    if(is_numeric($value)){
                        $data[$name] = self::convertBPS($value);    
                    }else{
                        $data[$name] = $value;
                    }
                    
                }
                $simpan[$key] = $data;
            }
            return $simpan;
        }else{
            return false;
        }
        
    }
}
