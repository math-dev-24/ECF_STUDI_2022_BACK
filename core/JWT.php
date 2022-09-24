<?php

class JWT
{
    private string $SECRET = 'H3ll03cf';

    public function generate(array $header, array $payload):string
    {
        $base64Header = base64_encode(json_encode($header));
        $base64Header = str_replace(['+','/','='], ['-','_',''], $base64Header);

        $base64Payload = base64_encode(json_encode($payload));
        $base64Payload = str_replace(['+','/','='], ['-','_',''], $base64Payload);

        $secret = base64_encode($this->SECRET);
        $signature = hash_hmac('sha256', $base64Header.".".$base64Payload, $secret, true);


        return $base64Header.".".$base64Payload;
    }
}