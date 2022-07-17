<?php

class User{
    private $id;
    private $email;
    private $password;
    private $name;

    public function __construct(array $data)
    {
        $this->hydrate($data);
    }

    private function hydrate(array $data){
        foreach($data as $key => $value) {
            $method = "set".ucfirst($key);
            if(method_exists($this, $method)){
                $this->$method($value);
            }
        }
    }


}