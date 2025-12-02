<?php
require __DIR__ . '/src/db.php';
require __DIR__ . '/src/crypto.php';
$config = require __DIR__ . '/src/config.php';

$key = $config['xor_key'];

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=users.xls");

$rows = $pdo->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);

echo "ID\tName\tEmail\tPassword\n";

foreach ($rows as $r) {
    echo $r['id'] . "\t" .
        decrypt_from_db($r['name_enc'], $key) . "\t" .
        decrypt_from_db($r['email_enc'], $key) . "\t" .
        decrypt_from_db($r['password_enc'], $key) . "\n";
}
