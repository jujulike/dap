<?php
/**
 * Created by PhpStorm.
 * User: BikeVR
 * Date: 2018/10/8
 * Time: 11:29
 */

namespace org\qqlogin;


class Recorder
{
    private static $data;
    private $inc;

    public function write($name,$value){
        self::$data[$name] = $value;
    }

    public function read($name){
        if(empty(self::$data[$name])){
            return null;
        }else{
            return self::$data[$name];
        }
    }

    public function readInc($name){
        if(empty($this->inc->$name)){
            return null;
        }else{
            return $this->inc->$name;
        }
    }

    public function delete($name){
        unset(self::$data[$name]);
    }

    function __destruct(){
        $_SESSION['QC_userData'] = self::$data;
    }
}