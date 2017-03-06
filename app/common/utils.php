<?php

function zaddslashes(&$string, $force = 0, $strip = FALSE)
{
   if (is_array($string)) {
       foreach ($string as $key => $val)
       {
           $string[$key] = zaddslashes($val, $force, $strip);
       }
   }
   else
   {
       //$string = ($strip ? stripslashes($string) : $string);
       $string = addslashes($string);
   }
   return $string;
}

if(get_magic_quotes_gpc()){
  zaddslashes($_GET);
  zaddslashes($_POST);
}



//////////////////////////////////////////////////////

function createRandomStr($length=6)
{  
  // $randpwd = ”;  
  // for ($i = 0; $i < $pw_length; $i++)  
  // {  
  //   $randpwd .= chr(mt_rand(33, 126));  
  // }  
  // return $randpwd;  

  // 密码字符集，可任意添加你需要的字符  
  $chars = array('A', 'B', 'C', 'D',  
  'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L','M', 'N',  
  'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y','Z', 
  '1', '2', '3', '4', '5', '6', '7', '8', '9');  
   
  // 在 $chars 中随机取 $length 个数组元素键名  
  $keys = array_rand($chars, $length);  
  $password = '';  
  for($i = 0; $i < $length; $i++)  
  {  
    // 将 $length 个数组元素连接成字符串  
    $password .= $chars[$keys[$i]];  
  }  
  return $password;  
}  

function gourl($url){
  echo '<script type="text/javascript">
          window.location.href="'.$url.'";
        </script>
  ';
}  

function mtime(){
  $_time = microtime(true);
  $inttime = (int)$_time;
  $time = $_time*1000;
  $microsecs = (int) ($time%1000);
  \vd($time,'$time');
  \vd($inttime,'$inttime');
  \vd($microsecs,'$microsecs');
  return (float) $inttime.'.'.$microsecs;
}

function datetime()
{
  return date('Y/m/d H:i:s');
}

function safequery(){
  foreach ($_GET as $key => $v) {
    $_GET[$key] = \safesql($v);
  }
  foreach ($_POST as $key => $v) {
    $_POST[$key] = \safesql($v);
  }
}

function indexBy($arr,$by='id'){
  $r = [];
  foreach ($arr as $key => $v) {
    $r[$v[$by].''] = $v;
  }
  return $r;
}

function indexWith($arr,$with){
  $r = [];
  foreach ($arr as $key => $v) {
    $key = $with.'_'.$v[$with];
    if(empty($r[$key])){
      $r[$key] = [];
    }
    $r[$key]['id_'.$v['id']] = $v;
  }
  return $r;
}

function initArr(&$target,$key){
  if( !isset($target[$key]) ){
    $target[$key] = [];
  }
}

function multiPickBy($arr,$by=[]){
  $r = [];
  foreach ($by as $byk => $byv) {
    $r[$byv] = [];
  }
  foreach ($arr as $key => $v) {
    foreach ($by as $byk => $byv) {
      if (!empty($v[$byv])) {
        $r[$byv][$v[$byv].''] = $v[$byv];
      }
    }
  }
  return $r;
}

function pickBy($arr,$by){
  $r = [];
  foreach ($arr as $key => $v) {
    $r[$v[$by].''] = $v[$by];
  }

  return array_keys($r);
}

function copyArr(&$to,$from,$keystr){
  $s = explode(',', $keystr);
  foreach ($s as $key) {
    $to[$key] = $from[$key];
  }
}

function safesql($str){
    // $str = str_replace([';',"'"],['；',"\'"],$str);
    $str = str_replace([';'],['；'],$str);
    $str = preg_replace('/(\s(and|or)\s)/i','&nbsp;$2&nbsp;',$str);
    return $str;
}


function totime($length) {
  // $str = '';
  // $h = floor($length /(60*60)) 
  // $h = floor($length /(60)) % (60)
  return gmstrftime('%H:%M:%S',$length);
}

function view($filepath) {
  $path = __BASE_DIR__."/view/$filepath.php";
  \vd($path);
  // include($path);
  return $path;
}

function user($filepath) {
    $path = __BASE_DIR__."/view/user/$filepath.php";
    \vd($path);
    // include($path);
    return $path;
}


function errlog($err)
{
    \error_log("\n".print_r($err, true)."\n", 3, __ERRLOG__);
}

function mkarr($v, $k)
{
    if (empty($v[$k])) {
        $v[$k] = [];
    }
}

function err($res)
{
    if( $res && !empty($res['err'])){
        return true;
    }
    return false;
}

function en($arr)
{
    return json_encode($arr, JSON_UNESCAPED_UNICODE);
}

function de($str)
{
    try{
        $de = json_decode($str, true);
    }catch(\Exception $e){
        $de = false;
    }
    return $de;
}

function _in_data($data)
{
    $data = str_replace(array(';', ' and ', ' or '), array('[;]', ' [and] ', ' [or] '), $data);

    return $data;
}

function _out_data($data)
{
    $data = str_replace(array('[;]', '[and]', '[or]'), array(';', 'and', 'or'), $data);

    return $data;
}

function addCacheKey($key)
{
    global $cache_keys;
    if (isset($cache_keys[$key])) {
        exit('添加了重复的key');
    }
    $cache_keys[$key] = $key;
}

function parseCacheKey($key, $args)
{
    global $cache_keys;
}

function init(&$target=[], $key, $value = null) {
    if( empty($target[$key]) ){
        $target[$key] = $value;
    }
}












































function toMap($objs)
{
  $r = [];
  foreach ($objs as $k => $v) {
    $r[$k] = $v->data;
  }
  return $r;
}

function toList($objs)
{
  $r = [];
  foreach ($objs as $k => $v) {
    $r[] = $v->data;
  }
  return $r;
}

function except($code,$msg=null)
{
  if(null===$msg && !empty(\CODE::$MSG[$code]) ) $msg = \CODE::$MSG[$code];
  if(!$msg) $msg = \CODE::$MSG[self::NO_ERROR];
  throw new \Exception($msg, $code);
}

function dft(&$arr,$k,$v)
{
  if(empty($arr[$k])){
    $arr[$k] = $v;
  }
}

function ppp($arr,$text=null)
{
  if(null!==$text) echo '<hr />'.$text.'<br />';
  echo '<pre>'.print_r($arr,true).'</pre>';
}

function needArgs($arr,$needs,$canBeNull='can_not_be_null') 
{
  $code = \CODE::PARAMETER_ERROR;
  $err = false;
  foreach ($needs as $k=>$v) {
    if( !isset($arr[$v]) ){
      $err = true;
    }else if( trim($arr[$v])=='' && $canBeNull!=='can_be_null' ){ // && $canBeNull!=='can_be_null'
      $err = true;
    }
    if($err){
      $e = new \Exception(\CODE::$MSG[$code] + ':'+$v, $code);
      // print_r($e);
      throw $e;
    }
  }
  return true;
}

function parseArgs(&$target, $arr,$needs,$canBeNull='can_not_be_null') 
{
  $code = \CODE::PARAMETER_ERROR;
  $err = false;
  foreach ($needs as $k=>$v) {
    if( !isset($arr[$v]) ){
      $err = true;
    }else if( trim($arr[$v])=='' && $canBeNull!=='can_be_null' ){ // && $canBeNull!=='can_be_null'
      $err = true;
    }
    if($err){
      $e = new \Exception(\CODE::$MSG[$code] + ':'+$v, $code);
      // print_r($e);
      throw $e;
    }
    $target[$v] = $arr[$v];
  }
  return $target;
}


function arrRmEmpty($array)
{
    $array=array_filter($array,create_function('$v','return !empty($v);'));
    return $array;
}

function arrRmDup($array)
{
  $arr = [];
  foreach ($array as $k=>$v) {
    $arr[$v] = $v;
  }
  return array_values($arr);
}





function my_get_browser(){
    if(empty($_SERVER['HTTP_USER_AGENT'])){
        return '命令行，机器人来了！';
    }
    if(false!==strpos($_SERVER['HTTP_USER_AGENT'],'MSIE 9.0')){
        return 'Internet Explorer 9.0';
    }
    if(false!==strpos($_SERVER['HTTP_USER_AGENT'],'MSIE 8.0')){
        return 'Internet Explorer 8.0';
    }
    if(false!==strpos($_SERVER['HTTP_USER_AGENT'],'MSIE 7.0')){
        return 'Internet Explorer 7.0';
    }
    if(false!==strpos($_SERVER['HTTP_USER_AGENT'],'MSIE 6.0')){
        return 'Internet Explorer 6.0';
    }
    if(false!==strpos($_SERVER['HTTP_USER_AGENT'],'Firefox')){
        return 'Firefox';
    }
    if(false!==strpos($_SERVER['HTTP_USER_AGENT'],'Chrome')){
        return 'Chrome';
    }
    if(false!==strpos($_SERVER['HTTP_USER_AGENT'],'Safari')){
        return 'Safari';
    }
    if(false!==strpos($_SERVER['HTTP_USER_AGENT'],'Opera')){
        return 'Opera';
    }
    if(false!==strpos($_SERVER['HTTP_USER_AGENT'],'360SE')){
        return '360SE';
    }
}
















///////////////////////////////////////////////////////// 打印调试堆栈用

/**
 * 打印调试，能显示相关位置，避免打多了不知道去哪删.
 */
function vdx($v, $str = '', $printobject = false)
{
    vd($v, $str, $printobject);
    exit('end');
}

global $_idx;
$_idx = 0;
function vd($v, $str = '', $printobject = false)
{
    global $_idx;
    // if (__MODE__ !== 'dev') {
    //     return;
    // }
    if (!isset($_GET['debug'])) {
        return;
    }

    ++$_idx;
    if ($_idx < 2) {
        echo '<style>
        *{
            // font-size:36px;
        }
        xmp, pre, plaintext {
          display: block;
          font-family: -moz-fixed;
          white-space: pre;
          margin: 1em 0;

          white-space: pre-wrap;
          word-wrap: break-word;

        }
        table
        {
            border-color: #600;
            border-width: 0 0 1px 1px;
            border-style: solid;
        }

        td
        {
            border-color: #600;
            border-width: 1px 1px 0 0;
            border-style: solid;
            margin: 0;
            padding: 4px;
            background-color: #FFC;
        }

        </style>';
        echo '
        <script src="/assets/jquery.min.js"></script>
        <script>
        var onoff = function(idx){
            // alert(idx)
            $("#"+idx).toggle()
        }
        </script>
        ';
    }

    // if( empty($_GET['debug']) ) return;
//    if(!isset($_GET['debug'])) return ;
    global $application_folder;
    $trace = $backtrace = debug_backtrace();
    $line = 0;
    $file = '';
    $filepath = null;

   // echo '<pre>';print_r($trace);echo '</pre>';
    $his = [];
    $tmp_file = null;
    $tmp_line = null;
    foreach ($trace as $a => $b) {
        if (!empty($b['file'])) {
            $file = $b['file'];
            if (!$filepath) {
                $filepath = $file;
            }
        }
            // if( isset($b['line']) && !$line ) $line = $b['line'];

        $trace[$a]['file'] = $file;
        if (isset($b['file']) && strrpos($b['file'].',', 'index.php,') > 0) {
            // $file = $b['file'];
            array_shift($trace);
        } else {
            if (isset($b['line']) && !$line) {
                $line = $b['line'];
            }
        }
        unset($trace[$a]['args']);
        if (!$printobject) {
            unset($trace[$a]['object']);
        }
        unset($b['args']);
        unset($b['object']);
        // echo '<br>'.print_r($b,true).'<br>';

        if ($tmp_file) {
            $his[] = [
                'file' => $tmp_file,
                'line' => $tmp_line,
                'class' => $b['class'],
                'function' => $b['function'],
            ];
        }

        $tmp_file = $b['file'];
        $tmp_line = $b['line'];
    }

    if (empty($trace[0]['class'])) {
        $trace[0]['class'] = '';
    }
    if (empty($trace[1]['class'])) {
        $trace[1]['class'] = '';
    }
    if (empty($trace[0]['function'])) {
        $trace[0]['function'] = '';
    }
    if (empty($trace[2]['class'])) {
        $trace[2]['class'] = '';
    }

    echo '<pre style="text-align: left;clear:both;width:100%;">';
    echo '<p onClick="onoff(\'idx_'.$_idx.'\')">'.substr($filepath, strlen(__BASE_DIR__) + 4).':<font color="red">'.$line.'</font> '.$trace[0]['class'].'::'.$trace[0]['function'].' <b style="font-size:bold;color:blue;"> '.$str.'</b></p>';

    $__content_v__ = $v;

    echo '<table id="idx_'.$_idx.'" style="display:none;margin-top:2px;margin-bottom:10px;"><tr><td>file</td><td>class</td><td>function</td><td>line</td></tr>';
    foreach ($his as $k => $v) {
        $filepath = $v['file'];
        echo '
        <tr>
            <td>'.substr($filepath, strlen(__BASE_DIR__) + 5).'</td>
            <td>'.$v['class'].'</td>
            <td>'.$v['function'].'</td>
            <td>'.$v['line'].'</td>
        </tr>
        ';
    }
    echo '</table>';

    print_r($__content_v__);

    echo '<br></pre><hr />';
}

function vds($v)
{
    foreach ($v as $a => $b) {
        vd($b->stored);
    }
}

   function indexArray($array, $unique_index)
  {
      if (!is_array($array) || !$unique_index) {
          return false;
      }
      $result = [];
      foreach ($array as $key => $value) {
          if (!isset($value["$unique_index"])) {
              return false;
          }
          $result[$value["$unique_index"]] = $value;
      }
      return $result;
  }

  /**
   * 数组转关联数组,其中每个value又是一个list数组:1->n
   * @param array $array
   * @param string $index
   * @return array|bool
   */
   function indexSet($array, $index)
  {
      if (!is_array($array) || !$index) {
          return false;
      }
      $result = [];
      foreach ($array as $key => $value) {
          if (!isset($value["$index"])) {
              return false;
          }
          if (!isset($result[$value["$index"]])) {
              $result[$value["$index"]] = [];
          }
          $result[$value["$index"]][] = $value;
      }
      return $result;
  }




