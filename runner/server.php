<?php
/**
 * Created by PhpStorm.
 * User: kunbudiharta
 * Date: 3/29/16
 * Time: 1:49 PM
 */
require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../apps/config/config.php';
date_default_timezone_set($config['timezone']);
use PEAR2\Net\RouterOS;
use Beritagar\Beritagar;

new Beritagar($config);

function sendMessage($chanel, $message, $encode = true)
{
    $ch = curl_init();
    if ($encode) {
        $message = json_encode($message);
    }
    $data = "ch=" . $chanel . "&msg=" . $message;
    curl_setopt($ch, CURLOPT_URL, 'http://localhost:8081/notify');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $ret = curl_exec($ch);
    curl_close($ch);
    return $ret;
}

function convert($data = null)
{
    $data = explode('/', $data);
    $upload = $data[0];
    $download = $data[1];

    if ($upload / 1024 / 1024 / 1024 / 1024 > 1) {
        $upload = number_format($upload / 1024 / 1024 / 1024 / 1024, 2) . ' TB';
    } elseif ($upload / 1024 / 1024 / 1024 > 1) {
        $upload = number_format($upload / 1024 / 1024 / 1024, 2) . ' GB';
    } elseif ($upload / 1024 / 1024 > 1) {
        $upload = number_format($upload / 1024 / 1024, 2) . ' MB';
    } elseif ($upload / 1024 > 1) {
        $upload = number_format($upload / 1024, 2) . ' KB';
    } elseif ($upload > 0) {
        $upload = number_format($upload, 2) . ' B';
    }

    if ($download / 1024 / 1024 / 1024 / 1024 > 1) {
        $download = number_format($download / 1024 / 1024 / 1024 / 1024, 2) . ' TB';
    } elseif ($download / 1024 / 1024 / 1024 > 1) {
        $download = number_format($download / 1024 / 1024 / 1024, 2) . ' GB';
    } elseif ($download / 1024 / 1024 > 1) {
        $download = number_format($download / 1024 / 1024, 2) . ' MB';
    } elseif ($download / 1024 > 1) {
        $download = number_format($download / 1024, 2) . ' KB';
    } elseif ($download > 1) {
        $download = number_format($download, 2) . ' B';
    }
    $result['data'] = [
        'upload' => $upload,
        'download' => $download,
        'upload-byte' => (int)$data[0],
        'download-byte' => (int)$data[1],
    ];
    return $result;
}


while (true) {

    $client = new RouterOS\Client(Beritagar::$config['host'], Beritagar::$config['username'],
        Beritagar::$config['password']);

    $request = new RouterOS\Request('/queue simple print');

    $responses = $client->sendSync($request);
    $data = array();

    foreach ($responses as $key => $response) {
        foreach ($response as $name => $value) {
            if ($name == 'name' || $name == 'target' || $name == 'parent' || $name == 'bytes' || $name == 'rate' || $name == 'max-limit') {
                if ($name == 'bytes' || $name == 'rate' || $name == 'max-limit') {
                    if ($name == 'bytes') {
                        $name = 'traffic';
                    }
                    if ($name == 'max-limit') {
                        $name = 'limit';
                    }
                    $data[$key][$name] = convert($value);
                } else {
                    $data[$key][$name] = $value;
                }
            }
        }
    }
    
    $data_new = array();
    foreach ($data as $k => $v) {
        $str = $v['name'];
        preg_match('/(OIX|IX)-(\w+)/', $str, $match);
        if (!isset($match[2])) {
            $match[2] = $str;
        }
        if (!isset($match[1])) {
            $match[1] = $str;
        }

        $str_parent = $v['parent'];
        preg_match('/(OIX|IX)-(\w+)/', $str_parent, $parent);
        if (!isset($parent[2])) {
            $parent[2] = $str_parent;
        }

        $data_new[strtoupper($parent[2])][strtoupper($match[2])][strtoupper($match[1])] = $v;


        if ($v['rate']['data']['upload-byte'] == 0) {
            $avg_upload = 0;
        } else {
            $avg_upload = ($v['rate']['data']['upload-byte'] / $v['limit']['data']['upload-byte'] * 100);
        }


        if ($v['rate']['data']['download-byte'] == 0) {
            $avg_download = 0;
        } else {
            $avg_download = ($v['rate']['data']['download-byte'] / $v['limit']['data']['download-byte'] * 100);
        }

        $data_new[strtoupper($parent[2])][strtoupper($match[2])][strtoupper($match[1])]['avg']['upload'] = (int)$avg_upload;
        $data_new[strtoupper($parent[2])][strtoupper($match[2])][strtoupper($match[1])]['avg']['download'] = (int)$avg_download;
    }

    $data_new = json_encode($data_new);
    sendMessage('beritagar_monitoring_mikrotik', $data_new, false);

    usleep(500000);
}