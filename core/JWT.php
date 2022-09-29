<?php

class JWT
{
    private $SECRET = 'testtt2';

    public function generate(array $header, array $payload, int $validity = 600):string
    {
        if($validity > 0){
            $now = new DateTime();
            $exp = $now->getTimestamp() + $validity;
            $payload['iat'] = $now->getTimestamp();
            $payload['exp'] = $exp;
        }

        $base64Header = base64_encode(json_encode($header));
        $base64Header = str_replace(['+','/','='], ['-','_',''], $base64Header);

        $base64Payload = base64_encode(json_encode($payload));
        $base64Payload = str_replace(['+','/','='], ['-','_',''], $base64Payload);

        $secret = base64_encode($this->SECRET);
        $signature = hash_hmac("sha256",$base64Header . "." .$base64Payload, $secret, true);
        $base64Signature = base64_encode($signature);
        $base64Signature = str_replace(['+','/','='], ['-','_',''], $base64Signature);

        return $base64Header.".".$base64Payload.".".$base64Signature;
    }

    public function check(string $token): bool
    {
        $header = $this->getHeader($token);
        $payload = $this->getPayload($token);
        $verifToken = $this->generate($header, $payload, 0);

        return $token === $verifToken;
    }

    public function getHeader(string $token):array
    {
        //démonter le token
        $array = explode('.', $token);
        //on décode le header
        $header = json_decode(base64_decode($array[0]), true);
        return $header;
    }

    public function getPayload(string $token):array
    {
        //démonter le token
        $array = explode('.', $token);
        //on décode le header
        $payload = json_decode(base64_decode($array[1]), true);
        return $payload;
    }
}