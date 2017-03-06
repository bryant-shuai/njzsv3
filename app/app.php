<?php
namespace app;

require __APP_DIR__ . '/lib/meedo.php';
require __APP_DIR__ . '/common/code.php';
require __APP_DIR__ . '/common/utils.php';
require __APP_DIR__ . '/common/tpl.php';
// require_once(__APP_DIR__ . '/lib/Predis/src/Autoloader.php');

use Config\ErrorCode;
// use Predis;

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

zaddslashes($_GET);
zaddslashes($_POST);

///////////////////////////////////////////////////////// 引擎
class engine
{
    public static $instance = null;

    public static function set($k, $v)
    {
        if (!self::$instance) {
            self::$instance = new engine();
        }
        self::$instance->$k = $v;
    }

    public static function get($k = null)
    {
        if (!self::$instance) {
            self::$instance = new engine();
        }
        if (!$k) {
            return self::$instance;
        } else {
            return self::$instance->$k;
        }
    }

    public static function route()
    {
        $uri = $_SERVER['REQUEST_URI'];
        $uris = explode('?', $uri);

        $argsStr = [];
        if (count($uris) > 1) {
            $argsStr = $uris[1];
        }
        $path = $uris[0];
        $cas = explode('/', $path);

        $controller = 'index';
        $action = 'index';
        if (!empty($cas[1])) {
            $controller = $cas[1];
        }
        if (!empty($cas[2])) {
            $action = $cas[2];
        }

        // if(!empty($_POST)){
        //   $action = '__post__'.$action;
        // }

        return [$controller, $action, [$argsStr]];
    }

    public static function run()
    {
        $route = self::route();
        $route[0] = urldecode($route[0]);
        $route[1] = urldecode($route[1]);

        $controller = '\\controller\\' . $route[0];
        $action = $route[1];
        \vd($controller.'->'.$action,'load page -> method:');
        $cA = new $controller();
        $cA->$action();
    }
}

class di implements \arrayaccess
{
    public static $instance = null;
    private $container = [];
    // private $

    public function __construct()
    {
        if (self::$instance) {
            return self::$instance;
        }
        self::$instance = $this;
        return 0;
    }

    public static function get()
    {
        if (self::$instance) {
            return self::$instance;
        }
        self::$instance = new self();

        return self::$instance;
    }

    public function offsetSet($offset, $value = null)
    {
        // echo '<br>offsetSet'.$offset;
        $this->container[$offset] = $value;
    }

    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    public function offsetGet($offset)
    {
        // echo '<br>offset:'.$offset;
        if (isset($this->container[$offset])) {
        } else {
            //从配置里取
            $targetName = $offset;
            if(!empty(\DiConfig::$Conf[$offset])){
              $targetName = \DiConfig::$Conf[$offset];
            }

            $service = '\\service\\' . $targetName;
            // \vd($service,'service');
            $this->container[$offset] = new $service();
        }

        return $this->container[$offset];
    }
}

//////////////////////////////////////////                    controller
class controller
{
    public $code = 0;
    public $data = [];
    public $msg = '';
    public $content = null;

    public function __construct()
    {
        $this->di = di::get();

        // unset($_SESSION['user']);
        // print_r($_SESSION);

        if( strpos('_'.$_SERVER['REQUEST_URI'], '/admin/aj_login')===false && strpos('_'.$_SERVER['REQUEST_URI'], '/remotesms/receive')===false &&  strpos('_'.$_SERVER['REQUEST_URI'], '/syncprice')===false &&     ( empty($_SESSION['user']) || empty($_SESSION['user']['id']) ) && strpos('_'.$_SERVER['REQUEST_URI'], '/admin/login')===false){

          header('Location: /admin/login');
          exit;
        } else if (!empty($_SESSION['user'])) {
          $url = explode('?', $_SERVER['REQUEST_URI']);
          if (!empty($_SESSION['user']['urls'][$url[0]])) {
            if (empty($_SESSION['user']['permissions'][$url[0]])) {
              header('Location: /index');
              exit;
            }
          }
        }


        // Predis\Autoloader::register();

        // $single_server = array(
        //     'host'     => '127.0.0.1',
        //     'port'     => 6379,
        //     'database' => 0,
        // );

        // $this->redis = new Predis\Client($single_server);
    }

    /**
     * 检测Post提交方式
     *
     * @_param_  隐含参数$_POST,从$_POST中取参数,若有则为POST方式
     *
     * @return bool
     */
    protected function verifyPost()
    {
        if (count($_POST) == 0) {
            $this->error(ErrorCode::REQUEST_METHOD);

            return false;
        } else {
            foreach ($_POST as $ele) {
                if (!isset($ele)) {
                    $this->error(ErrorCode::PARAMETER_ERROR);
                }
            }
        }

        return true;
    }

    /**
     * 检测并取得指定的数据项.
     *
     * @method fetchData
     *
     * @param string $type POST或者GET
     * @param array $keys 键
     * @param bool $block 当所取数据项不存在时是否报错
     *
     * @return array 结果
     */
    protected function fetchData($type, $keys, $block)
    {
        $type = strtoupper($type);
        if ($type == 'POST') {
            $type = $_POST;
        } elseif ($type == 'GET') {
            $type = $_GET;
        }

        $data = [];
        foreach ($keys as $key) {
            if (isset($type[$key]) && (!empty($type[$key]) || $type[$key] === '0')) {
                $data[$key] = $type[$key];
            } elseif ($block) {
                // 只有在开发模式中才进行错误提示
                if (constant("__MODE__") == "dev") {
                    $this->error(ErrorCode::PARAMETER_ERROR, "missing key:".$key);
                } else {
                    $this->error(ErrorCode::PARAMETER_ERROR);
                }
            }
        }

        return $data;
    }

    /**
     * 关联数组的换key方法,纯方法;会修改原原始数组
     *
     * @param array $arr 换key的原始数组
     * @param array $keyMap 旧key到新key的关联数组
     * @return array 修改过的原有的数组
     */
    protected function replaceKey($arr = [], $keyMap = [])
    {
        if (!$arr || !$keyMap) {
            return [];
        }
        foreach ($keyMap as $key => $newKey) {
            if (isset($arr[$key])) {
                $arr[$newKey] = $arr[$key];
                unset($arr[$key]);
            }
        }
        return $arr;
    }

    public function echoJson()
    {
        $this->content['code'] = $this->code;
        if ($this->msg) {
            $this->content['msg'] = $this->msg;
        } elseif ($this->code) {
            $this->content['msg'] = \Code::$MSG['' . $this->code];
        }
        echo json_encode($this->content, JSON_UNESCAPED_UNICODE);
    }

    public function error($code = -1, $msg = null)
    {
        if ($code) {
            $this->code = $code;
        }
        if ($msg) {
            $this->msg = $msg;
        }
        // $this->echoJson();
        \except($this->code, $this->msg);
    }

    function err($code = -1, $msg = null){
      return $this->error($code, $msg);
    }

    public function data($data = '')
    {
        $this->content['data'] = $data;
        $this->echoJson();
    }
}

//////////////////////////////////////////                    model
class model
{
    public static $table = '';
    public static $db = null;
    public static $cache = [];
    public static $key = 'id';
    public $id = null;
    public $di = null;
    public $data = [];
    public $column_key = null;
    public $column_value = null;
    public static $test = 'test in model';


    public function __construct()
    {
        $this->di = di::get();
    }

    //数据库连接
    public static function connect()
    {
        if (!self::$db) {
            self::$db = new \medoo(\DbConfig::$mysql);
        }
        return self::$db;
    }

    public function __set($name, $value)
    {
        // echo "Setting '$name'\n";
        $this->callMethod('parse');
        $this->callMethod('_set_' . $name);
        if (!isset($this->$name)) {
            $this->$name = $value;
        }
        return $this;
    }

    public function __get($name)
    {
        // echo "Getting '$name'\n";
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        // echo "Setting '$name'\n";

        $this->callMethod('parse');
        $this->callMethod('_set_' . $name);

        if (isset($this->$name)) return $this->$name;
        return null;
    }


    public function callMethod($method)
    {
        if (is_callable(array($this, $method))) {
            // echo "Call '$method'\n";
            $this->$method();
        }
    }


    public static function loadObjByData($data = [], $column_key = null, $column_value = null)
    {
        $obj = new static();
        $obj->data = $data;

        if (!$column_key) $column_key = 'id';
        $obj->column_key = $column_key;

        if (!$column_value) $column_value = $obj->data['id'];
        $obj->column_value = $column_value;

        return $obj;
    }


    public static function findByIds($ids = [],$columns='*',$indexBy=null, $order="")
    {
      $res = self::finds('where id in ('.implode(',', $ids).') '.$order, $columns);
      if($indexBy){
        $res = \indexBy($res,$indexBy);
      }
      return $res;
    }


    public static function loadObj($id = 0, $key = null)
    {
        if (!$key) $key = static::$key;

        if ($key == 'id') {
            $keystr = static::$table . '_' . $key . '_' . $id;
            if (!empty(self::$cache[$keystr])) {
                return self::$cache[$keystr];
            }
        }

        self::connect();
        $r = self::$db
            ->get(static::$table, '*', [
                '' . $key => $id,
            ]);
        \vd($r, 'r');
        $obj = new static;
        $obj->column_key = $key;
        $obj->column_value = $id;
        // foreach ($r as $k => $v) {
        //     $obj->data[$k] = $v;
        // }

        if ($r) {
            $obj->data = $r;
            $obj->column_value = $id;
            if ($key == 'id'){
              self::$cache[$keystr] = $obj;
              $obj->id = $id;
            }
            return $obj;
        }
        return null;
    }

    public function save($data = [], $param = ['key' => null, 'value' => null])
    {
        // \vd($data, 'data');
        self::connect();

        $newdata = [];
        if(empty($data)){
            $newdata = $this->data;
        }else{
            $newdata = $data;
        }
        // foreach ($data as $key => $value) {
        //     $this->data[$key] = $value;
        // }


        // \vd($this->column_key, '$this->column_key');
        // \vd($this->column_value, '$this->column_value');

        if (empty($this->column_key) && empty($this->column_value)) {
            $id = static::insert($newdata);
            \vd($id, '$id');
            if ($id !== false) {
                $this->id = $id;
                $this->data['id'] = $id;
                // if(!empty($param['key'])){
                //   $this->column_key = $param['key'];
                //   $this->column_value = $param['value'];
                // }else{
                //   $this->column_key = 'id';
                //   $this->column_value = $id; 
                // }

                $this->column_key = 'id';
                $this->column_value = $id; 
                // $keystr = static::$table . '_' . $this->column_key . '_' . $id;

                foreach ($newdata as $k => $v) {
                  $this->data[$k] = $v;
                }
                return $this;
            } else {
                // return new ErrorObject(ErrorCode::LOG_QUERY_FAIL);
            }
            return $this;
        }

        \vd($newdata, 'newdata to save');
        $r = self::$db->update(static::$table, $newdata, [
            '' . $this->column_key . '' => $this->column_value
        ]);

        if (!empty($data['deleted']) && $data['deleted'] == '1') {
            $this->clearcache();
        }
        return $this;
    }


    public function save_bk($data = [], $param = ['key' => null, 'value' => null])
    {
        // \vd($data, 'data');
        self::connect();
        foreach ($data as $key => $value) {
            $this->data[$key] = $value;
        }

        \vd($this->data, '$this->data');

        // \vd($this->column_key, '$this->column_key');
        // \vd($this->column_value, '$this->column_value');

        if (empty($this->column_key) && empty($this->column_value)) {
            $id = static::insert($this->data);
            \vd($id, '$id');
            if ($id !== false) {
                $this->id = $id;
                $this->data['id'] = $id;
                $this->column_key = 'id';
                $this->column_value = $id;
                // $keystr = static::$table . '_' . $this->column_key . '_' . $id;

                return $this;
            } else {
                // return new ErrorObject(ErrorCode::LOG_QUERY_FAIL);

            }
            $this->column_key = $param['key'];
            $this->column_value = $param['value'];
            return $this;
        }

        \vd($this->data, '$this->data');
        $r = self::$db->update(static::$table, $this->data, [
            '' . $this->column_key . '' => $this->column_value
        ]);

        if (!empty($data['deleted']) && $data['deleted'] == '1') {
            $this->clearcache();
        }
        return $this;
    }

    public static function clearCacheById($id,$key='id')
    {
      $keystr = static::$table . '_' . $key . '_' . $id;
      if (isset(self::$cache[$keystr])) {
          self::$cache[$keystr] = null;
          unset(self::$cache[$keystr]);
      }
    }

    public static function deleteById($id)
    {
        self::connect();
        return static::delete(["id" => $id]);
    }

    public static function load($v = 0, $key = 'id')
    {
        // $keystr = static::$table . '_' . $key . '_' . $v;
        // if (!empty(self::$cache[$keystr])) {
        //     return self::$cache[$keystr];
        // }
        self::connect();
        $r = self::$db
            ->get(static::$table, '*', [
                $key => $v,
            ]);
        \vd($r, 'r');

        return $r;
    }

    public function clearcache()
    {
        if (!empty($this->data['id'])) {
            $id = $this->data['id'];
            $keystr = static::$table . '_' . $this->column_key . '_' . $id;
            if (isset(self::$cache[$keystr])) {
                self::$cache[$keystr] = null;
                unset(self::$cache[$keystr]);
            }
        }
    }

    public function rm($force = 0)
    {
        if ($force === 'forcedelete') {
            if ($this->column_value && $this->column_key) {
                self::$db->delete(static::$table, [$this->column_key => $this->column_value]);
                $this->data = null;
            }
        } else {
            $this->data['deleted'] = 1;
            $this->save();
        }
        $this->clearcache();
    }


    public static function count($where = [])
    {
        self::connect();

        return self::$db->count(static::$table, $where);
    }

    public static function sum($where = [], $column)
    {
        self::connect();

        return self::$db->sum(static::$table, $column, $where);
    }


    public static function sqlQuery($sql)
    {
        self::connect();

        $data = self::$db->query($sql);
        if (is_numeric($data) || is_bool($data)) {
            return $data;
        } else {
            return $data->fetchAll(\PDO::FETCH_ASSOC);
        }
    }

    public static function execSql($sql1, $sql2)
    {
        $prefix = \DbConfig::$mysql['prefix'];
        $sql = $sql1 . ' ' . $prefix . static::$table . ' ' . $sql2;
        return static::sqlQuery($sql);
    }

    public static function execCount($sql)
    {
        $prefix = \DbConfig::$mysql['prefix'];
        $count = static::sqlExec("SELECT count(*) as count from " . $prefix . static::$table . "  $sql ");
        if ($count && is_array($count) && count($count) > 0 && isset($count[0]['count'])) {
            $count = $count[0]['count'];
            return (int)$count;
        }
        return 0;
    }

    public static function log()
    {
        return self::$db->log();
    }


    public static function find($where = '', $column = '*')
    {
        $data = self::finds($where, $column);

        return count($data) <= 0 ? [] : $data[0];
    }

    public static function finds($where = '', $columns = '*', &$count=null, $param=[
          'length'=>0,
          'page'=>1,
        ])
    {
        self::connect();

        $sqlLimit = '';
        if($param['length']>0){
          $length = $param['length'];
          $limit = $param['length']*($param['page']-1);
          $sqlLimit = " LIMIT $limit,$length ";
        }

        $prefix = \DbConfig::$mysql['prefix'];
        $sqlwhere = ' FROM ' . $prefix . static::$table . ' ' . $where;
        $sql = 'SELECT ' . $columns . $sqlwhere . $sqlLimit;
        $tmp = self::$db->query($sql);
        if ($tmp) {
            $data = $tmp->fetchAll();
        } else {
            $data = [];
        }

        if(null!==$count){
          $count = static::execCount($where);
        }

        return count($data) <= 0 ? [] : $data;
    }


    /**
     * 数据库查询
     * @param $column
     * @param array $where
     * @return mixed
     */
    public static function query($column, $where = [])
    {
        self::connect();
        return self::$db->select(static::$table, $column, $where);
    }

    /**
     * 数据库join查询
     * @param array $where
     * @param array $column
     * @param array $join
     * @return mixed
     */
    public static function queryJoin($where = [], $column = [], $join = [])
    {
        self::connect();
        return self::$db->get(static::$table, $join, $column, $where);
    }

    /**
     * 数据库插入
     * @param array $data
     * @return mixed
     */
    public static function insert($data = [])
    {
        self::connect();
        return self::$db->insert(static::$table, $data);
    }

    /**
     * 数据库更新
     * @param array $where
     * @param array $data
     * @return mixed
     */
    public static function update($where = [], $data = [])
    {
        self::connect();
        \vd(static::$table);
        \vd($data);
        $result = self::$db->update(static::$table, $data, $where);
        return $result;
    }

    /**
     * 数据库删除,不建议使用,建议使用假删除
     * @param array $where
     * @return mixed
     */
    public static function delete($where = [])
    {
        self::connect();
        return self::$db->delete(static::$table, $where);
    }

    /**
     * sql语句执行
     * @param $sql
     * @return mixed
     */
    public static function sqlExec($sql)
    {
        self::connect();
        $data = self::$db->query($sql);
        if ($data) {
            return $data->fetchAll();
        } else {
            return false;
        }
    }
}

//////////////////////////////////////////                    service
class service
{
    public function __construct()
    {
        $this->di = di::get();
        // Predis\Autoloader::register();

        // $single_server = array(
        //     'host'     => '127.0.0.1',
        //     'port'     => 6379,
        //     'database' => 0,
        // );

        // $this->redis = new Predis\Client($single_server);
    }
}

/////////////////////////////////////////////////////////     设置loader
class Loader
{
    public static function __autoloader($class)
    {
        $splits = explode('\\', $class);
        $class = implode('/', $splits);
        $filename = __APP_DIR__ . '/' . $class . '.php';
        if (file_exists($filename)) {
            require_once $filename;

            return true;
        }

        return false;
    }

    public static function __libsloader($class)
    {
        $splits = explode('\\', $class);
        $class = implode('/', $splits);
        $filename = __APP_DIR__ . '/libs/' . $class . '.php';
        if (file_exists($filename)) {
            require_once $filename;

            return true;
        }

        return false;
    }
}

spl_autoload_register(array('\app\Loader', '__autoloader'));
spl_autoload_register(array('\app\Loader', '__libsloader'));








