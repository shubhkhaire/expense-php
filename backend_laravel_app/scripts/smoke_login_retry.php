<?php
$hosts = ['127.0.0.1','localhost','::1'];
$port = 8000;
$login = ['email'=>'demo@example.com','password'=>'password'];
foreach ($hosts as $h) {
    $url = "http://$h:$port/auth/login";
    echo "Trying $url\n";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($login));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $res = curl_exec($ch);
    $err = curl_error($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    echo "HTTP_CODE: " . ($info['http_code'] ?? 'N/A') . "\n";
    if ($err) echo "CURL_ERR: $err\n";
    echo "RESP: " . ($res ?: '<empty>') . "\n\n";
}
