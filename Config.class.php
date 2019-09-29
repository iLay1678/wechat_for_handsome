<?php  
/** 
 * PHP 无数据库配置文件增删查改模块 
 * ！注：本模块未对高并发进行优化兼容，如果数据量或并发过大，还是用数据库比较好 :) 
 *  
 * 使用方法： 
 *  
 * 一、引用本模块 
 *  
 *  require_once 'Config.class.php'; 
 *  
 *  
 * 二、初始化 
 *  
 *  $C = new Config('配置文件名');  // * 如果是在二级目录下，请确保该目录存在 
 *  
 *  
 * 三、内置方法 
 *  
 *  - 存储（如果已存在则是修改）单条数据 
 *  
 *      $C->set('sitename', '孟坤博客'); 
 *  
 *  
 *  - 存储（如果已存在则是修改）一个数组 
 *       
 *      $C->set('user', array('name'=>'peter', 'age'=>12)); 
 *  
 *  
 *  - 读取一条数据 
 *  
 *      $C->get('user', '默认值'); 
 *  
 *  
 *  - 删除一条数据 
 *  
 *      $C->delete('user'); 
 *  
 *  
 *  - 保存对数据的修改 
 *  
 *      $C->save();     // 保存成功返回 true，否则返回失败原因 
 *  
 *   
 * * 注：为了避免频繁地写文件，以上所有对数据的操作都必须调用一次 $C->save(); 才会真正被保存到配置文件中 
 *       建议将所有的数据操作都执行完后再进行存储操作。 
 *  
 *  
 * * 附：精简写法 
 *  
 *      $C->set('sitename', '孟坤博客')->save(); 
 */  
  
define('CONFIG_EXIT', '<?php exit;?>');  
class Config {  
    private $data;  
    private $file;  
      
    /** 
     * 构造函数 
     * @param $file 存储数据文件 
     * @return  
     */  
    function __construct($file) {  
        $file = $file.'.php';  
        $this->file = $file;  
        $this->data= self::read($file);  
    }  
      
    /** 
     * 读取配置文件 
     * @param $file 要读取的数据文件 
     * @return 读取到的全部数据信息 
     */  
    public function read($file) {  
        if(!file_exists($file)) return array();  
          
        $str = file_get_contents($file);  
        $str = substr($str, strlen(CONFIG_EXIT));  
        $data = json_decode($str, true);  
        if (is_null($data)) return array();  
        return $data;  
    }  
      
    /** 
     * 获取指定项的值 
     * @param $key 要获取的项名 
     * @param $default 默认值 
     * @return data 
     */  
    public function get($key = null, $default = '') {  
        if (is_null($key)) return $this->data;  // 取全部数据  
          
        if(isset($this->data[$key])) return $this->data[$key];  
        return $default;  
    }  
      
    /** 
     * 设置指定项的值 
     * @param $key 要设置的项名 
     * @param $value 值 
     * @return null 
     */  
    public function set($key, $value) {  
        if(is_string($key)) {   // 更新单条数据  
            $this->data[$key] = $value;  
        } else if(is_array($key)) {   // 更新多条数据                 
            foreach ($this->data as $k => $v) {  
                if ($v[$key[0]] == $key[1]) {  
                    $this->data[$k][$value[0]] = $value[1];  
                }  
            }  
        }  
          
        return $this;  
    }  
      
    /** 
     * 删除并清空指定项 
     * @param $key 删除项名 
     * @return null 
     */  
    public function delete($key) {  
        unset($this->data[$key]);  
          
        return $this;  
    }  
      
    /** 
     * 保存配置文件 
     * @param $file 要保存的数据文件 
     * @return true-成功 其它-保存失败原因 
     */  
    public function save() {  
        if(defined('JSON_PRETTY_PRINT')) {  
            $jsonStr = json_encode($this->data, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);  
        } else {  
            $jsonStr = json_encode($this->data);  
        }  
          
        // 含有二进制或非utf8字符串对应检测  
        if(is_null($jsonStr)) return '数据文件有误';  
        $buffer = CONFIG_EXIT.$jsonStr;  
          
        $file_strm = fopen($this->file, 'w');  
        if(!$file_strm) return '写入文件失败，请赋予 '.$file.' 文件写权限！';  
        fwrite($file_strm, $buffer);  
        fclose($file_strm);  
        return true;  
    }  
}  