<?php

require_once "./core/JWT.php";

class Auth
{
    private JWT $jwt;

    public function __construct()
    {
        $this->jwt = new JWT();
    }

    public function getToken(): string | bool
    {
        if(isset($_SERVER['Authorization']))
        {
            $token = trim($_SERVER['Authorization']);
        }
        elseif(isset($_SERVER['HTTP_AUTHORIZATION']))
        {
            $token = trim($_SERVER['HTTP_AUTHORIZATION']);
        }elseif(function_exists('apache_request_headers') !== null)
        {
            $requestHeaders = apache_request_headers();
            if (isset($requestHeaders['Authorization']))
            {
                $token = trim($requestHeaders['Authorization']);
            }
        }
        if (!isset($token) || !preg_match('/Bearer\s(\S+)/', $token, $matches))
        {
            return false;
        }else{
            return str_replace("Bearer ",'', $token);
        }
    }

    public function verifToken(): mixed
    {
        if($this->getToken()){
            $token = $this->getToken();
            if (!$this->jwt->isValid($token) ||
                $this->jwt->isExpired($token) ||
                !$this->jwt->check($token)
                ){
                return false;
            }
            return true;
        }else{
            return false;
        }
    }
}
