<?php

//function zaddslashes(&$string, $force = 0, $strip = FALSE)
//{
//    if (is_array($string)) {
//        foreach ($string as $key => $val)
//        {
//            $string[$key] = zaddslashes($val, $force, $strip);
//        }
//    }
//    else
//    {
//        //$string = ($strip ? stripslashes($string) : $string);
//        $string = addslashes($string);
//    }
//    return $string;
//}
//
//zaddslashes($_GET);
//zaddslashes($_POST);
/**
 * 数组转关联数组:1->1
 * @param array $array
 * @param string $unique_index
 * @return array|bool
 */
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

/**
     * 删除数组中的数字键,用于mysql数据统计时的冗余数据
     * @param $array array 二维数组,删除第二维中数字key
     * @return mixed
     */
function unsetNumberKey($array)
{
    foreach ($array as $itemKey => $itemValue) {
        foreach ($itemValue as $key => $value) {
            if (is_numeric($key)) {
                unset($array[$itemKey][$key]);
            }
        }
    }
    return $array;
}

/**
 * 组合数组
 * @param $array1 array 目标数组,二维
 * @param $relationKey1 string 目标数组和数据数组的关联key
 * @param $array2 array 数据数组,二维
 * @param $relationKey2 string 数据数组和目标数组的关联key
 * @param $reflectMap array 关联数组,从数据数组取Key1值,添加到目标Key2值, 这里取[Key1=>Key2];
 * @param bool $block 是否阻断
 * @param string $placeholder 不阻断的情况下,为空值提供占位符
 * @return bool|mixed
 */
public static function combineArray($array1, $relationKey1, $array2, $relationKey2, $reflectMap, $block = false, $placeholder = "")
{
    $array2 = self::indexArray($array2, $relationKey2);
    if (!$array2 && $block) {
        return false;
    }

    foreach ($array1 as $key1 => $value1) {
        if (isset($array2[$value1[$relationKey1]])) {
            foreach ($reflectMap as $fromKey => $toKey) {
                $array1[$key1][$toKey] = $array2[$value1[$relationKey1]][$fromKey];
            }
        } elseif (!$block) {
            foreach ($reflectMap as $fromKey => $toKey) {
                $array1[$key1][$toKey] = $placeholder;
            }
        } else {
            return false;
        }
    }

    return $array1;
}

function sortBy($target,$sort_key, $sort_order = SORT_ASC) {
  $order = [];
  foreach ($target as $key => $v) {
    $order[$key] = $v[$sort_key];
  }
  // return array_multisort($target,SORT_ASC,SORT_NUMERIC);
  array_multisort($order, $sort_order, $target);
  \vd($target,'$target');
  return $target;
}

function indexBy($arr,$key)
{
  $r = [];
  foreach ($arr as $v) {
    $r[''.$v[$key]] = $v;
  }
  return $r;
}

function datetime()
{
  return date('Y/m/d H:i:s');
}

function errlog($err)
{
    \error_log("\n" . print_r($err, true) . "\n", 3, __ERRLOG__);
}

function mkarr($v, $k)
{
    if (empty($v[$k])) {
        $v[$k] = [];
    }
}

function err($res)
{
    if ($res && !empty($res['err'])) {
        return true;
    }
    return false;
}

function en($arr)
{
    return json_encode($arr, JSON_UNESCAPED_UNICODE);
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

function except($code, $msg = null)
{
    if (null === $msg) $msg = \CODE::$MSG[$code];
    throw new \Exception($msg, $code);
}

function dft(&$arr, $k, $v)
{
    if (empty($arr[$k])) {
        $arr[$k] = $v;
    }
}

function ppp($arr, $text = null)
{
    if (null !== $text) echo '<hr />' . $text . '<br />';
    echo '<pre>' . print_r($arr, true) . '</pre>';
}

function needArgs($arr, $needs, $canBeNull = 'can_not_be_null')
{
    $code = \CODE::PARAMETER_ERROR;
    $err = false;
    foreach ($needs as $k => $v) {
        if (!isset($arr[$v])) {
            $err = true;
        } else if (trim($arr[$v]) == '' && $canBeNull !== 'can_be_null') { // && $canBeNull!=='can_be_null'
            $err = true;
        }
        if ($err) {
            $e = new \Exception(\CODE::$MSG[$code] + ':' + $v, $code);
            // print_r($e);
            throw $e;
        }
    }
    return true;
}

function parseArgs(&$target, $arr, $needs, $canBeNull = 'can_not_be_null')
{
    \vd($needs, 'needs');
    \vd($arr, 'arr');

    $code = \CODE::PARAMETER_ERROR;
    $err = false;
    foreach ($needs as $k => $v) {
        if ($canBeNull !== 'can_be_null') {
            if (!isset($arr[$v])) {
                $err = true;
            } else if (trim($arr[$v]) == '') { // && $canBeNull!=='can_be_null'
                $err = true;
            }
            if ($err) {
                $e = new \Exception(\CODE::$MSG[$code] + ':' + $v, $code);
                // print_r($e);
                throw $e;
            }
        }
        $target[$v] = $arr[$v];
    }
    \vd($target, '$target');
    return $target;
}

function de($str)
{
    try {
        $de = json_decode($str, true);
    } catch (\Exception $e) {
        $de = false;
    }
    return $de;
}

function arrRmEmpty($array)
{
    $array = array_filter($array, create_function('$v', 'return !empty($v);'));
    return $array;
}

function arrRmDup($array)
{
    $arr = [];
    foreach ($array as $k => $v) {
        $arr[$v] = $v;
    }
    return array_values($arr);
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


//unset($_GET['debug']);
if (isset($_GET['debug'])) {
    // echo '--------------------';
    // vd($_GET, 'get');
    // vd($_POST, 'post');
    // vd(\meedo::$SQLS, 'sqls');
    // vd($_SESSION,'session');


    // $sqls=explode("\n",\meedo::$SQLS);
    // foreach($sqls as &$sql){
    //     $sql=substr($sql,32);
    // }
    // $sqlstr=implode("\n",$sqls);
    // if(isset($_GET['debug'])) echo '<pre style="text-align:left;">'. $sqlstr .'</pre>';
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
global $_lasttime;
$_lasttime = 0;
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
        <script src="/lib/jquery.js"></script>
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
    // echo '<pre>'.print_r($trace,true).'</pre>';
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
        if (isset($b['file']) && strrpos($b['file'] . ',', 'index.php,') > 0) {
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

        $b['file'] = empty($b['file']) ? '' : $b['file'];
        $b['line'] = empty($b['line']) ? '' : $b['line'];
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

    if(empty($_lasttime)) $_lasttime = microtime(true);
    $_nowtime = microtime(true);
    $_time = ($_nowtime-$_lasttime)*1000*1000;

    echo '<hr /><pre style="text-align: left;">';
    echo '<p onClick="onoff(\'idx_' . $_idx . '\')">' . substr($filepath, strlen(__BASE_DIR__) + 4) . ':<font color="red">' . $line . '</font> ' . $trace[0]['class'] . '::' . $trace[0]['function'] . ' <b> ' . $str . '</b> /  ' . sprintf("%.2f", $_time) . 'ms</p>';

    $__content_v__ = $v;

    echo '<table id="idx_' . $_idx . '" style="display:none;margin-top:-10px;margin-bottom:20px;"><tr><td>file</td><td>class</td><td>function</td><td>line</td></tr>';
    foreach ($his as $k => $v) {
        $filepath = $v['file'];
        echo '
        <tr>
            <td>' . substr($filepath, strlen(__BASE_DIR__) + 5) . '</td>
            <td>' . $v['class'] . '</td>
            <td>' . $v['function'] . '</td>
            <td>' . $v['line'] . '</td>
        </tr>
        ';
    }
    echo '</table>';

    print_r($__content_v__);

    echo '<br></pre><br>';
}

function vds($v)
{
    foreach ($v as $a => $b) {
        vd($b->stored);
    }
}










