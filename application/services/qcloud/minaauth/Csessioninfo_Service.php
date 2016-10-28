<?php

/**
 * Created by PhpStorm.
 * User: ayisun
 * Date: 2016/10/1
 * Time: 15:15
 */
class Csessioninfo_Service
{

    public function __construct()
    {
        require_once('system/db/mysql_db.php');
    }

    /**
     * @param $skey
     * @param $create_time
     * @param $last_visit_time
     * @param $open_id
     * @param $session_key
     * @return bool
     */
    public function insert_csessioninfo($params)
    {
        $insert_sql = 'insert into cSessioninfo SET skey = "' . $params['skey'] . '",create_time = ' . $params['create_time'] . ',last_visit_time = ' . $params['last_visit_time'] . ',open_id = "' . $params['openid'] . '",session_key="' . $params['session_key'] . '",user_info=\''.$params['user_info'].'\'';
        $mysql_insert = new mysql_db();
        return $mysql_insert->query_db($insert_sql);
    }

    /**
     * @param $id
     * @param $skey
     * @param $last_visit_time
     * @return bool
     */
    public function update_csessioninfo_time($params)
    {
        $update_sql = 'update cSessioninfo set last_visit_time = ' . $params['last_visit_time'] . ' where id = ' . $params['id'];
        $mysql_update = new mysql_db();
        return $mysql_update->query_db($update_sql);
    }

    public function update_csessioninfo($params)
    {
        $update_sql = 'update cSessioninfo set last_visit_time = ' . $params['last_visit_time'] . ',skey = "' . $params['skey'] .'",user_info=\''.$params['user_info'].'\' where id = ' . $params['id'];
        $mysql_update = new mysql_db();
        return $mysql_update->query_db($update_sql);
    }

    /**
     * @param $id
     * @param $skey
     * @return bool
     */
    public function delete_csessioninfo($open_id)
    {
        $delete_sql = 'delete from cSessioninfo where open_id = "' . $open_id . '"';
        $mysql_delete = new mysql_db();
        return $mysql_delete->query_db($delete_sql);
    }

    public function delete_csessioninfo_by_id_skey($params)
    {
        $delete_sql = 'delete from cSessioninfo where id = ' . $params['id'];
        $mysql_delete = new mysql_db();
        return $mysql_delete->query_db($delete_sql);
    }

    /**
     * @param $id
     * @param $skey
     * @return array|bool
     */
    public function select_csessioninfo($params)
    {
        $select_sql = 'select * from cSessioninfo where id = ' . $params['id'] . ' and skey = "' . $params['skey'] . '"';
        $mysql_select = new mysql_db();
        $result = $mysql_select->select_db($select_sql);
        if ($result !== false && !empty($result)) {
            $arr_result = array();
            while ($row = mysql_fetch_array($result)) {
                $arr_result['id'] = $row['id'];
                $arr_result['skey'] = $row['skey'];
                $arr_result['create_time'] = $row['create_time'];
                $arr_result['last_visit_time'] = $row['last_visit_time'];
                $arr_result['open_id'] = $row['open_id'];
                $arr_result['session_key'] = $row['session_key'];
                $arr_result['user_info'] = $row['user_info'];
            }
            return $arr_result;
        } else {
            return false;
        }
    }

    /**
     * @param $open_id
     * @return bool
     */
    public function get_id_csessioninfo($open_id)
    {
        $select_sql = 'select id from cSessioninfo where open_id = "' . $open_id . '"';
        $mysql_select = new mysql_db();
        $result = $mysql_select->select_db($select_sql);
        if ($result !== false && !empty($result)) {
            $id = false;
            while ($row = mysql_fetch_array($result)) {
                $id = $row['id'];
            }
            return $id;
        } else {
            return false;
        }
    }

    public function check_session_for_login($params){
        $select_sql = 'select *_time from cSessioninfo where open_id = "' . $params['openid'] . '"';
        $mysql_select = new mysql_db();
        $result = $mysql_select->select_db($select_sql);
        if ($result !== false && !empty($result)) {
            $create_time = false;
            while ($row = mysql_fetch_array($result)) {
                $create_time = $row['create_time'];
            }
            if($create_time == false){
                return false;
            }else{
                $now_time = time();
                if(($now_time-$create_time)/86400>$params['login_duration']){
                     $this->delete_csessioninfo($params['openid']);
                    return true;
                }else{
                    return true;
                }
            }
        } else {
            return true;
        }
    }


    public function check_session_for_auth($params){
        $result = $this->select_csessioninfo($params);
        if(!empty($result) && $result !== false && count($result) != 0){
            $now_time = time();
            $create_time = $result['create_time'];
            $last_visit_time = $result['last_visit_time'];
            if(($now_time-$create_time)/86400>$params['login_duration']) {
                $this->delete_csessioninfo_by_id_skey($params);
                return false;
            }else if(($now_time-$last_visit_time)>$params['session_duration']){
                return false;
            }else{
                $params['last_visit_time'] = $now_time;
                $this->update_csessioninfo_time($params);
                return $result['user_info'];
            }
        }else{
            return false;
        }
    }

    /**
     * @param $skey
     * @param $create_time
     * @param $last_visit_time
     * @param $open_id
     * @param $session_key
     * @return bool
     */
    public function change_csessioninfo($params)
    {
        if($this->check_session_for_login($params)){
            $id = $this->get_id_csessioninfo($params['openid']);
            if ($id != false) {
                $params['id'] = $id;
                if ($this->update_csessioninfo($params))
                    return $id;
                else
                    return false;
            } else {
                return $this->insert_csessioninfo($params);
            }
        }else{
            return false;
        }
    }
}