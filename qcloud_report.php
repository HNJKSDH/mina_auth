<?php
/**
 * Created by PhpStorm.
 * User: ayisun
 * Date: 2016/10/13
 * Time: 14:24
 */

require_once('system/report_data/report_data.php');
require_once('system/report_data/ready_for_report_data.php');
require_once "system/load_config.php";
$load_config = new load_config();
$config = $load_config->fc_load_config("system/conf/config.ini");
$region = $config['region'];//用户配置
$secret_id = $config['secret_id'];//用户配置
$secretKey = $config['secretKey'];//用户配置
$ip = $config['ip'];//用户配置

$ready_for_report_data = new ready_for_report_data();
$report_data = new report_data();
$contents = $ready_for_report_data->check_data();
if($contents){
    $arr_contents = json_decode($contents,true);
    $arr_report_data['ip'] = $arr_contents['ip'];
    $arr_report_data['login_count'] = "login_count";
    $arr_report_data['login_count_value'] = $arr_contents['login_count'];
    $arr_report_data['login_sucess_rate'] = "login_sucess_rate";
    if($arr_contents['login_count']===0){
        $arr_report_data['login_sucess_value'] = 0 ;
    }else{
        $arr_report_data['login_sucess_value'] = ($arr_contents['login_sucess']/$arr_contents['login_count'])*100;
    }
    $arr_report_data['auth_count'] = "auth_count";
    $arr_report_data['auth_count_value'] = $arr_contents['login_count'];

    $arr_report_data['auth_sucess_rate']="auth_sucess_rate";
    if($arr_report_data['auth_count']===0){
        $arr_report_data['auth_sucess_value']=0;
    }else{

    }
    $arr_report_data['auth_sucess_value'] = ($arr_contents['auth_sucess']/$arr_contents['auth_count'])*100;

    $report_data->report_data($region, $secret_id, $secretKey,$ip,"authsucessrate",$arr_report_data['auth_sucess_value']);
    $report_data->report_data($region, $secret_id, $secretKey,$ip,"loginsucessrate",$arr_report_data['login_sucess_value']);

    $ready_for_report_data->deletfile();
}
