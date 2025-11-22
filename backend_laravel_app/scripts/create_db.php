<?php
$host = '127.0.0.1';
$port = 3310;
$user = 'root';
$passwordCandidates = ['', '1234'];

$created = false;
foreach ($passwordCandidates as $pwd) {
    try {
        $dsn = sprintf('mysql:host=%s;port=%d', $host, $port);
        $pdo = new PDO($dsn, $user, $pwd);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('CREATE DATABASE IF NOT EXISTS expense_tracker CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        echo "CREATE_DB_OK (password='" . ($pwd === '' ? '<empty>' : $pwd) . "')\n";
        $created = true;
        break;
    } catch (PDOException $e) {
        echo "TRY_PWD='" . ($pwd === '' ? '<empty>' : $pwd) . "' -> ERR: " . $e->getMessage() . "\n";
    }
}

if (! $created) {
    echo "CREATE_DB_FAILED: all password attempts failed\n";
    exit(1);
}
