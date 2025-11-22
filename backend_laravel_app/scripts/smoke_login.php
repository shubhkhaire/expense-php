<?php
$login = [
    'email' => 'demo@example.com',
    'password' => 'password',
];
$ch = curl_init('http://127.0.0.1:8000/auth/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($login));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$res = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);
if ($err) {
    echo "CURL_ERR:" . $err . "\n";
    exit(1);
}
echo $res . "\n";
