<?php
namespace model;

/**

  $http = new \model\http(5);
  $url = 'http://nj.tiangoutech.com/order/get_price?client_id=1&product_id=172&debug-';

  $ret = $http->get($url);

  try {
      $retarr = \de($ret);
      //拿到price
      $price = $retarr['result']['price'];
      //做你自己的事

  } catch (\Exception $e) {
    //取price失败
    exit('取price失败 client_id:'.$client_id.' product_id:'.$product_id);
  }


*/



class http
{
    public $timeout = 3;

    function http($timeout=3){
        $this->timeout = $timeout;
        return true;
    }

    function execute($method, $url, $fields='', $userAgent='', $httpHeaders='', $username='', $password=''){
        $ch = self::create();
        if(false === $ch){
            return false;
        }
        if(is_string($url) && strlen($url)){
            $ret = curl_setopt($ch, CURLOPT_URL, $url);
        }else{
            return false;
        }
        //是否显示头部信息
        curl_setopt($ch, CURLOPT_HEADER, false);
        //
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if($username != ''){
            curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password);
        }
        $method = strtolower($method);
        if('post' == $method){
            curl_setopt($ch, CURLOPT_POST, true);
            if(is_array($fields)){
                $sets = array();
                foreach ($fields AS $key => $val){
                    $sets[] = $key . '=' . urlencode($val);
                }
                $fields = implode('&',$sets);
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        }else if('put' == $method){
            curl_setopt($ch, CURLOPT_PUT, true);
        } // www.ahlinux.com
        //curl_setopt($ch, CURLOPT_PROGRESS, true);
        //curl_setopt($ch, CURLOPT_VERBOSE, true);
        //curl_setopt($ch, CURLOPT_MUTE, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);//设置curl超时秒数
        if(strlen($userAgent)){
            curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        }
        if(is_array($httpHeaders)){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeaders);
        }
        $ret = curl_exec($ch);
        if(curl_errno($ch)){
            curl_close($ch);
            return array(curl_error($ch), curl_errno($ch));
        }else{
            curl_close($ch);
            if(!is_string($ret) || !strlen($ret)){
                return false;
            }
            return $ret;
        }
    }
     
    function post($url, $fields, $userAgent = '', $httpHeaders = '', $username = '', $password = ''){
        $ret = self::execute('POST', $url, $fields, $userAgent, $httpHeaders, $username, $password);
        if(false === $ret){
            return false;
        }
        if(is_array($ret)){
            return false;
        }
        return $ret;
    }
     
    function get($url, $userAgent = '', $httpHeaders = '', $username = '', $password = ''){
        $ret = self::execute('GET', $url, '', $userAgent, $httpHeaders, $username, $password);
        if(false === $ret){
            return false;
        }
        if(is_array($ret)){
            return false;
        }
        return $ret;
    }
     
    function create(){
        $ch = null;
        if(!function_exists('curl_init')){
            return false;
        }
        $ch = curl_init();
        if(!is_resource($ch)){
            return false;
        }
        return $ch;
    }

}


// 代码示例:
// <?php
// $curl = new Curl();
// $curl->get(‘http://www.ahlinux.com/’);
// 2，POST用法
 

// 代码示例:
// <?php
// $curl = new Curl();
// $curl->get(‘http://www.ahlinux.com/’, ‘p=1&time=0′);
