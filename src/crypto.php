<?php
/**
* XOR stream encrypt/decrypt (symmetric)
* هذه الدوال مناسبة للتعلّم فقط.
*/
function xor_stream_encrypt(string $data, string $key): string {
if ($key === '') return $data;
$out = '';
$klen = strlen($key);
for ($i = 0, $n = strlen($data); $i < $n; $i++) {
$out .= chr(ord($data[$i]) ^ ord($key[$i % $klen]));
}
return $out;
}


function encrypt_for_db(string $plaintext, string $key): string {
$cipher = xor_stream_encrypt($plaintext, $key);
return base64_encode($cipher);
}


function decrypt_from_db(string $b64cipher, string $key): string {
$cipher = base64_decode($b64cipher, true);
if ($cipher === false) return '';
return xor_stream_encrypt($cipher, $key);
}
?>