#!/bin/sh
#install_appid.sh ip passwd $appid $secret
./install_appid.sh 10.141.20.104 Murphy.me appid1 secret1 qcloud_appid 192.168.1.1 127.0.0.1 3306 root1 root1
if [ $? -eq 0 ] 
then
    echo "ok"
else
    echo "error"
fi