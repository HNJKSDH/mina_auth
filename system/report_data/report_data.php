<?php

/**
 * Created by PhpStorm.
 * User: ayisun
 * Date: 2016/10/11
 * Time: 10:41
 */
class report_data
{

    public function __construct()
    {
        require_once "system/load_config.php";
        require_once('system/http_util.php');
        require_once('system/log/log.php');
    }

    /**
     * @param $params
     * @return bool|string
     */
    private function set_report_data($ip,$metricName,$value)
    {
        $obj=null;
        $data = array();
        $obj->ip = $ip;
        $data[0]->dimensions = $obj;
        $data[0]->metricName = $metricName;
        $data[0]->value = $value;
        return $data;
    }



    public function report_data($region, $secret_id, $secretKey,$ip,$metricName,$value)
    {
        $data = $this->set_report_data($ip,$metricName,$value);
        if ($this->put_monitor_data($region, $secret_id, $secretKey,$data) == false) {
                log_message("ERROR", "report_data_err");
        }
    }

    /**
     * @param $region
     * @param $secret_id
     * @param $signature
     * @return bool
     * 描述：创建命名空间
     */
    public function create_namespace($region, $secret_id, $secretKey)
    {
        $time = time();
        $nonce = mt_rand(10000, 99999);
        $arr = Array(
            "Action" => "CreateNamespace",
            "Region" => $region,
            "Timestamp" => $time,
            "Nonce" => $nonce,
            "SecretId" => $secret_id,
            "namespace" => "minaauth"
        );
        $signature = $this->get_signature($arr, 'monitor.api.qcloud.com/v2/index.php?', $secretKey);
        $signature = urlencode($signature);
        $url = "https://monitor.api.qcloud.com/v2/index.php?Action=CreateNamespace&Region=$region&Timestamp=$time&Nonce=$nonce&SecretId=$secret_id&Signature=$signature&namespace=minaauth";
        $http_util = new http_util();
        $ret_msg = $http_util->http_get($url);
        if ($ret_msg && $this->is_json($ret_msg)) {
            $json_ret_msg = json_decode($ret_msg, true);
            if ($json_ret_msg['code'] == 0)
                return true;
        }
        return false;
    }


    /**
     * @param $region
     * @param $secret_id
     * @param $signature
     * @param $metric_name
     * @param $metric_cname
     * @return bool
     * 描述：创建指标
     */
    public function create_metric($region, $secret_id, $secretKey, $metric_name, $metric_cname)
    {
        $time = time();
        $nonce = mt_rand(10000, 99999);
        $arr = Array(
            "Action" => "CreateMetric",
            "Region" => $region,
            "Timestamp" => $time,
            "Nonce" => $nonce,
            "SecretId" => $secret_id,
            "namespace" => "minaauth",
            "metricName" => $metric_name,
            "metricCname" => $metric_cname,
            "dimensionNames.0" => "ip",
            "statisticsType.0.period" => 300,
            "statisticsType.0.statistics" => "max"
        );
        $signature = $this->get_signature($arr, 'monitor.api.qcloud.com/v2/index.php?', $secretKey);
        $signature = urlencode($signature);
        $url = "https://monitor.api.qcloud.com/v2/index.php?Action=CreateMetric&Region=$region&Timestamp=$time&Nonce=$nonce&SecretId=$secret_id&Signature=$signature&namespace=minaauth&metricName=$metric_name&metricCname=$metric_cname&dimensionNames.0=ip&statisticsType.0.period=300&statisticsType.0.statistics=max";
        $http_util = new http_util();
        $ret_msg = $http_util->http_get($url);
        if ($ret_msg && $this->is_json($ret_msg)) {
            $json_ret_msg = json_decode($ret_msg, true);
            if ($json_ret_msg['code'] == 0)
                return true;
        }
        return false;
    }

    /**
     * @param $region
     * @param $secret_id
     * @param $signature
     * @param $data
     * @return bool
     * 描述：创建指标上报数据
     */
    public function put_monitor_data($region, $secret_id, $secretKey, $data)
    {
        $time = time();
        $nonce = mt_rand(10000, 99999);
        $arr = Array(
            "Action" => "PutMonitorData",
            "Region" => $region,
            "Timestamp" => $time,
            "Nonce" => $nonce,
            "SecretId" => $secret_id
        );
        $signature = $this->post_signature($arr, 'receiver.monitor.tencentyun.com/v2/index.php?', $secretKey);

        $params = array(
            "Action" => "PutMonitorData",
            "Region" => $region,
            "Timestamp" => $time,
            "Nonce" => $nonce,
            "SecretId" => $secret_id,
            "Signature"=>$signature,
            "Namespace"=>"minaauth",
            "Data"=>$data
        );
        $params_json = json_encode($params);
        $http_util = new http_util();
        $ret_msg = $http_util->http_post('http://receiver.monitor.tencentyun.com:8080/v2/index.php',$params_json);
        if ($ret_msg && $this->is_json($ret_msg)) {
            $json_ret_msg = json_decode($ret_msg, true);
            if ($json_ret_msg['code'] == 0)
                return true;
        }
        return false;

    }

    /**
     * @return bool
     * 描述：获取用户组ID
     */
    public function describe_user_group($region, $secret_id, $secretKey)
    {
        $time = time();
        $nonce = mt_rand(10000, 99999);
        $arr = Array(
            "Action" => "DescribeUserGroup",
            "Region" => $region,
            "Timestamp" => $time,
            "Nonce" => $nonce,
            "SecretId" => $secret_id,
        );
        $signature = $this->get_signature($arr, 'account.api.qcloud.com/v2/index.php?', $secretKey);

        $url = "https://account.api.qcloud.com/v2/index.php?Action=DescribeUserGroup&Region=$region&Timestamp=$time&Nonce=$nonce&SecretId=$secret_id&Signature=$signature";
        $http_util = new http_util();
        $ret_msg = $http_util->http_get($url);
        if ($ret_msg && $this->is_json($ret_msg)) {
            $json_ret_msg = json_decode($ret_msg, true);
            if (isset($json_ret_msg['data']['groupSet'][0]['groupId']))
                return $json_ret_msg['data']['groupSet'][0]['groupId'];
        }
        return false;
    }

    /**
     * @param $region
     * @param $secret_id
     * @param $signature
     * @param $metric_name
     * @param $metric_cname
     * @return bool
     * 描述：创建告警规则
     */
    public function create_alarm_rule($region, $secret_id, $secretKey, $metric_name)
    {
        $time = time();
        $nonce = mt_rand(10000, 99999);
        $receivers_id = $this->describe_user_group($region, $secret_id, $secretKey);
        $arr = Array(
            "Action" => "CreateAlarmRule",
            "Region" => $region,
            "Timestamp" => $time,
            "Nonce" => $nonce,
            "SecretId" => $secret_id,
            "namespace"=>"minaauth",
            "metricName"=>$metric_name,
            "dimensionNames.0"=>"ip",
            "operatorType"=>"<",
            "threshold"=>95,
            "period"=>300,
            "statistics"=>"max",
            "constancy"=>2,
            "receiversId"=>$receivers_id
        );
        $signature = $this->get_signature($arr, 'monitor.api.qcloud.com/v2/index.php?', $secretKey);
        if ($receivers_id) {
            $url = "https://monitor.api.qcloud.com/v2/index.php?Action=CreateAlarmRule&Region=$region&Timestamp=$time&Nonce=$nonce&SecretId=$secret_id&Signature=$signature&namespace=minaauth&metricName=$metric_name&dimensionNames.0=ip&operatorType=<&threshold=95&period=300&statistics=max&constancy=2&receiversId=$receivers_id";
            $http_util = new http_util();
            $ret_msg = $http_util->http_get($url);
            if ($ret_msg && $this->is_json($ret_msg)) {
                $json_ret_msg = json_decode($ret_msg, true);
                if (isset($json_ret_msg['data']['alarmRuleId']))
                    return $json_ret_msg['data']['alarmRuleId'];
            }
        }

        return false;
    }

    /**
     * @param $region
     * @param $secret_id
     * @param $signature
     * @param $metric_name
     * @param $ip
     * @param $metric_name_value
     * @return bool
     * 描述：绑定告警规则和对象
     */
    public function bind_alarm_rule_objects($region, $secret_id, $secretKey, $metric_name, $ip)
    {
        $time = time();
        $nonce = mt_rand(10000, 99999);
        $alarmRule_id = $this->create_alarm_rule($region, $secret_id, $secretKey, $metric_name);
        $arr = Array(
            "Action" => "BindAlarmRuleObjects",
            "Region" => $region,
            "Timestamp" => $time,
            "Nonce" => $nonce,
            "SecretId" => $secret_id,
            "alarmRuleId" => $alarmRule_id,
            "dimensions.0.name"=>"ip",
            "dimensions.0.value"=>$ip
        );
        $signature = $this->get_signature($arr, 'monitor.api.qcloud.com/v2/index.php?', $secretKey);

        if ($alarmRule_id) {
            $url = "https://monitor.api.qcloud.com/v2/index.php?Action=BindAlarmRuleObjects&Region=$region&Timestamp=$time&Nonce=$nonce&SecretId=$secret_id&Signature=$signature&alarmRuleId=$alarmRule_id&dimensions.0.name=ip&dimensions.0.value=$ip";
            $http_util = new http_util();
            $ret_msg = $http_util->http_get($url);
            if ($ret_msg && $this->is_json($ret_msg)) {
                $json_ret_msg = json_decode($ret_msg, true);
                if ($json_ret_msg['code'] == 0)
                    return true;
            }
        }
        return false;
    }

    public function is_json($str)
    {
        json_decode($str);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * @param $arr
     * @param $get_url
     * @param $secretKey
     * @return bool|string
     * 描述：生成签名
     */
    public function get_signature($arr, $get_url, $secretKey)
    {
        $sort_arr = ksort($arr);
        if ($sort_arr) {
            $sort_json = json_encode($arr);
            $sort_json = $this->wipe_illegal_char($sort_json);
            $str_sig = 'GET' . $get_url . $sort_json;
            $sign_str = base64_encode(hash_hmac('sha1', $str_sig, $secretKey, true));
            return $sign_str;
        }
        return false;
    }

    public function post_signature($arr, $get_url, $secretKey)
    {
        $sort_arr = ksort($arr);
        if ($sort_arr) {
            $sort_json = json_encode($arr);
            $sort_json = $this->wipe_illegal_char($sort_json);
            $str_sig = 'POST' . $get_url . $sort_json;
            $sign_str = base64_encode(hash_hmac('sha1', $str_sig, $secretKey, true));
            return $sign_str;
        }
        return false;
    }

    private function wipe_illegal_char($str)
    {
        $tmp_begin = -1;
        $tmp_end = -1;
        $str_tmp = "";
        for ($i = 0; $i < strlen($str); $i++) {
            if ($str[$i] == "[") {
                if ($tmp_begin == -1)
                    $tmp_begin = $i;
            }
            if ($str[$i] == "]") {
                if ($tmp_end == -1)
                    $tmp_end = $i;
            }
        }
        for ($j = 0; $j < strlen($str); $j++) {
            $str_tmp[$j] = $str[$j];
            if ($tmp_begin != -1 && $tmp_end != -1) {
                if ($j < $tmp_begin || $j > $tmp_end) {
                    if ($str_tmp[$j] == ",")
                        $str_tmp[$j] = "&";
                    if ($str_tmp[$j] == "_")
                        $str_tmp[$j] = ".";
                    if ($str_tmp[$j] == "\"")
                        $str_tmp[$j] = "";
                    if ($str_tmp[$j] == "{")
                        $str_tmp[$j] = "";
                    if ($str_tmp[$j] == "}")
                        $str_tmp[$j] = "";
                    if ($str_tmp[$j] == ":")
                        $str_tmp[$j] = "=";
                }
            } else {
                if ($str_tmp[$j] == ",")
                    $str_tmp[$j] = "&";
                if ($str_tmp[$j] == "_")
                    $str_tmp[$j] = ".";
                if ($str_tmp[$j] == "\"")
                    $str_tmp[$j] = "";
                if ($str_tmp[$j] == "{")
                    $str_tmp[$j] = "";
                if ($str_tmp[$j] == "}")
                    $str_tmp[$j] = "";
                if ($str_tmp[$j] == ":")
                    $str_tmp[$j] = "=";
            }
        }
        return implode($str_tmp);
    }
}