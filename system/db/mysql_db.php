<?php

/**
 * Created by PhpStorm.
 * User: ayisun
 * Date: 2016/10/1
 * Time: 16:47
 */
class mysql_db
{

    private $host= null;
    private $port= null;
    private $user_name= null;
    private $pass_wd = null;
    private $data_base = null;

    public function __construct()
    {
        require_once "system/load_config.php";
        require_once('system/log/log.php');
        $load_config = new load_config();
        $config = $load_config->fc_load_config("system/db/db.ini");
        $this->host = $config['host'];
        $this->port = $config['port'];
        $this->user_name = $config['user_name'];
        $this->pass_wd = $config['pass_wd'];
        $this->data_base = $config['data_base'];
    }

    /**
     * @param $sql
     * @return string
     * 描述:安全过滤sql,防止sql注入
     */
    function safe($sql)
    {
        if (get_magic_quotes_gpc()) {
            $sql = stripslashes($sql);
        }
        $sql = mysql_real_escape_string($sql);
        return $sql;
    }

    /**
     * @param $sql
     * @return bool
     * 描述:执行Mysql增删改操作
     */
    public function query_db($sql)
    {
        $con = mysql_connect($this->host . ':' . $this->port, $this->user_name, $this->pass_wd);
        if ($con) {
            mysql_select_db($this->data_base, $con);
            $mysql_result = mysql_query($sql);
            if ($mysql_result === false) {
                mysql_close($con);
                log_message("ERROR","$sql mysql_err");
                return false;
            }
            mysql_close($con);
            return true;
        } else {
            log_message("ERROR","$sql mysql_connect_err");
            return false;
        }
    }

    /**
     * @param $sql
     * @return bool|resource
     * 描述：执行mysql查询操作
     */
    public function select_db($sql)
    {
        $con = mysql_connect($this->host . ':' . $this->port, $this->user_name, $this->pass_wd);
        if ($con) {
            mysql_select_db($this->data_base, $con);
            $arr_result = mysql_query($sql);
            mysql_close($con);
            return $arr_result;
        } else {
            log_message("ERROR","$sql mysql_connect_err");
            return false;
        }
    }

    public function init_db($sql){
        $con = mysql_connect($this->host . ':' . $this->port, $this->user_name, $this->pass_wd);
        if ($con) {
            $result = mysql_query("$sql",$con);
            if($result===false){
                log_message("ERROR","$sql mysql_err");
                return false;
            }
            return true;
        }else{
            log_message("ERROR","$sql mysql_connect_err");
            return false;
        }
    }
}