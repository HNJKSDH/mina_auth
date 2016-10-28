<?php
/**
 * Created by PhpStorm.
 * User: ayisun
 * Date: 2016/10/12
 * Time: 16:15
 */

class init_db {

    public function __construct()
    {
        require_once "mysql_db.php";
    }

    public function init_db_config($params){
        if(isset($params['cdb_ip']) && isset($params['cdb_port']) && isset($params['cdb_user_name']) && isset($params['cdb_pass_wd'])){
            $host = $params['cdb_ip'];
            $port = $params['cdb_port'];
            $user_name = $params['cdb_user_name'];
            $pass_wd = $params['cdb_pass_wd'];
            $data_base = "cAuth";
            $data = "[db]\nhost = $host\nport = $port\nuser_name = $user_name\npass_wd = $pass_wd\ndata_base= $data_base";
            $data_path = "system/db/db.ini";
            $handle = fopen($data_path,'w');
            flock($handle, LOCK_EX);
            if (fwrite($handle,$data) === false){
                log_message("ERROR","init_db_config_wrong");
                flock($handle, LOCK_UN);
                fclose($handle);
                return false;
            }else{
                flock($handle, LOCK_UN);
                fclose($handle);
                return true;
            }
        }else{
            return false;
        }
    }

    public function init_db_table(){
        $sql = file_get_contents("system/db/db.sql");
        if($sql === false){
            return false;
        }
        $_arr = explode(';', $sql);
        $mysql_db = new mysql_db();
        if($mysql_db->init_db("DROP DATABASE IF EXISTS `cAuth`")){
            if($mysql_db->init_db("CREATE DATABASE `cAuth`")){
                foreach ($_arr as $_value) {
                    if(!empty($_value)){
                        if(!$mysql_db->query_db($_value.';'))
                            return false;
                    }
                }
            }
            else{
                return false;
            }
        }else{
            return false;
        }
        return true;
    }
}