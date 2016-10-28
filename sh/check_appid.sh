#!/bin/sh

appid_check=`curl -i -d  "{\"version\":1,\"componentName\":\"MA\",\"interface\":{\"interfaceName\" : \"qcloud.cam.initdata\",\"para\" : {\"appid\":\"$1\",\"secret\":\"$2\",\"qcloud_appid\":\"$3\",\"ip\":\"$4\",\"cdb_ip\":\"$5\",\"cdb_port\":\"$6\",\"cdb_user_name\":\"$7\",\"cdb_pass_wd\":\"$8\"}}}" http://127.0.0.1/mina_auth/ 2>/dev/null`
echo $appid_check | grep "\"returnCode\":0"
test $? -eq 0 && echo "yes" || echo "no"