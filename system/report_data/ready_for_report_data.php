<?php
/**
 * Created by PhpStorm.
 * User: ayisun
 * Date: 2016/10/12
 * Time: 10:11
 */

class ready_for_report_data {

    private $report_data_path = 'reportdata/';

    public function __construct()
    {
        require_once "system/load_config.php";
        require_once('system/log/log.php');
        $load_config = new load_config();
        $config = $load_config->fc_load_config("system/conf/config.ini");
        $this->report_data_path =  $config['data_path'];
    }

    /**
     * @param $report_data
     */
    public function  write_report_data($report_data){
        file_exists($this->report_data_path) OR mkdir($this->report_data_path, 0755, TRUE);
        $data_path = $this->report_data_path."data";
        $handle = fopen($data_path,'w');
        flock($handle, LOCK_EX);
        $write_result = fwrite($handle,$report_data);
        if ($write_result === false){
            log_message("ERROR","$report_data write_report_data_wrong");
        }
        flock($handle, LOCK_UN);
        fclose($handle);
        return $write_result;
    }

    /**
     * @return string
     */
    public function read_report_data(){
        $data_path = $this->report_data_path."data";
        if(!file_exists($data_path)){
            log_message("ERROR","report_data_not_exit");
            return false;
        }
        $handle = fopen($data_path, "r");
        $contents = fread($handle, filesize($data_path));
        fclose($handle);
        if($contents===false){
            log_message("ERROR","read_report_data_wrong");
        }
        return $contents;
    }


    public function check_data(){
        $contents = $this->read_report_data();
        if($contents){
            if($this->is_json($contents)){
                $json_contents = json_decode($contents, true);
                if(isset($json_contents['ip']) && isset($json_contents['appid']) && isset($json_contents['login_count']) && isset($json_contents['login_sucess']) && isset($json_contents['auth_count']) && isset($json_contents['auth_sucess'])){
                    return $contents;
                }
                return false;
            }
            return false;
        }else{
            return false;
        }
    }

    public function ready_data($type){
        $content = $this->check_data();
        if($content != false){
            $arr_content = json_decode($content,true);
            $arr_content[$type]++;
            $json_content = json_encode($arr_content);
            return $this->write_report_data($json_content);
        }
        return false;
    }

    public function is_json($str)
    {
        json_decode($str);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    public function deletfile(){
        $data_path = $this->report_data_path."data";
        if(is_file($data_path)){
            if(!unlink($data_path)){
                chmod($data_path,0777);
                unlink($data_path);
            }
        }
    }
}