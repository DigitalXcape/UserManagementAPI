<?php
require_once '../model/UserModel.php';
define('JWT_SECRET_KEY', '435dgh47n349sn783hf839db3');

//function to validate the JWT
function validateJWT($jwt, $secret = JWT_SECRET_KEY) {
    $parts = explode('.', $jwt);
    if (count($parts) !== 3) return null;

    list($base64UrlHeader, $base64UrlPayload, $base64UrlSignature) = $parts;
    $header = json_decode(base64_decode($base64UrlHeader), true);
    $payload = json_decode(base64_decode($base64UrlPayload), true);
    $signature = base64_decode(str_replace(['-', '_'], ['+', '/'], $base64UrlSignature));

    $expectedSignature = hash_hmac('sha256', "$base64UrlHeader.$base64UrlPayload", $secret, true);

    if (hash_equals($expectedSignature, $signature) && $payload['exp'] > time()) {
        return $payload;
    }

    return null;
}
?>