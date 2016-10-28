<?php
/**
 * Created by PhpStorm.
 * User: ayisun
 * Date: 2016/10/2
 * Time: 10:54
 */
require_once('system/parse_request.php');
$request = file_get_contents("php://input");
$parse_request = new parse_request();
echo($parse_request->parse_json($request));
