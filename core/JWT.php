<?php

class JWT
{
    private $SECRET = 'testtt2';


    /**
     * this function generate token JWT. default validity 10min
     * @param array $header
     * @param array $payload
     * @param int $validity
     * @return string
     */
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

    /**
     * this function return true if token is OK
     * @param string $token
     * @return bool
     */
    public function check(string $token): bool
    {
        $header = $this->getHeader($token);
        $payload = $this->getPayload($token);
        $verifToken = $this->generate($header, $payload, 0);
        return ($token === $verifToken);
    }

    /**
     * this function return true if token is expired
     * @param string $token
     * @return bool
     */
    public function isExpired(string $token):bool
    {
        $payload = $this->getPayload($token);
        $now = new DateTime();
        return $payload['exp'] < $now->getTimestamp();
    }

    /**
     * this function verify validity token ex : qzaedqd.dzaqedzqa.zqffqf
     * @param string $token
     * @return bool
     */
    public function isValid(string $token):bool
    {
        return preg_match('/^[a-zA-Z0-9\-\_]+\.[a-zA-Z0-9\-\_]+\.[a-zA-Z0-9\-\_]+$/',$token) === 1;
    }

    /**
     * this function return Header decode
     * @param string $token
     * @return array
     */
    public function getHeader(string $token):array
    {
        //démonter le token
        $array = explode('.', $token);
        //on décode le header
        $header = json_decode(base64_decode($array[0]), true);
        return $header;
    }

    /**
     * this function return Payload decode
     * @param string $token
     * @return array
     */
    public function getPayload(string $token):array
    {
        //démonter le token
        $array = explode('.', $token);
        //on décode le header
        $payload = json_decode(base64_decode($array[1]), true);
        return $payload;
    }

}