<?php
require '../includes/helpers.php';

ob_start();
include '../includes/ua.txt';
$user_agent = ob_get_clean();
$user_agent = explode("\n", $user_agent);
shuffle($user_agent);
$user_agent = str_replace(array("\n", "\r", "\n\r"), '', end($user_agent));

$url = base_url() .'/admin/ip.php';
$process = curl_init();
curl_setopt($process, CURLOPT_URL, $url);
curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($process, CURLOPT_PROXY, "127.0.0.1:9050");
curl_setopt($process, CURLOPT_PROXYTYPE, 7);
curl_setopt($process, CURLOPT_HEADER, 0); 
curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
//curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1); 
curl_setopt($process, CURLOPT_VERBOSE, 1);
curl_setopt($process, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($process, CURLOPT_USERAGENT, $user_agent);
$output = curl_exec($process);
curl_close($process);

echo $output;