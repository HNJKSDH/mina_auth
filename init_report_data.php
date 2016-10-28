<?php
/**
 * Created by PhpStorm.
 * User: ayisun
 * Date: 2016/10/17
 * Time: 10:18
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

$report_data = new report_data();
var_dump($report_data->create_namespace($region,$secret_id,$secretKey));
var_dump($report_data->create_metric($region,$secret_id,$secretKey,"authsucessrate","authsucessrate"));
var_dump($report_data->create_metric($region,$secret_id,$secretKey,"loginsucessrate","loginsucessrate"));
var_dump($report_data->bind_alarm_rule_objects($region,$secret_id,$secretKey,"authsucessrate",$ip));
var_dump($report_data->bind_alarm_rule_objects($region,$secret_id,$secretKey,"loginsucessrate",$ip));
