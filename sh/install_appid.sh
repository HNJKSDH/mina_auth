#!/usr/bin/expect -f
set timeout 10
set IP [lindex $argv 0]
set PASSWORD [lindex $argv 1]
set APPID [lindex $argv 2]
set SECRET [lindex $argv 3]
set QCLOUD_APPID [lindex $argv 4]
set P_IP [lindex $argv 5]
set CDB_IP [lindex $argv 6]
set CDB_PORT [lindex $argv 7]
set CDB_USER_NAME [lindex $argv 8]
set CDB_PASS_WD [lindex $argv 9]
spawn ssh -l root -p 22 $IP
expect {
    "yes/no" {
         send "yes\r";exp_continue 
            }
    "password:" {
         send "$PASSWORD\r"; 
            }
}
expect "]# "
send "/opt/lampp/htdocs/mina_auth/sh/check_appid.sh $APPID $SECRET $QCLOUD_APPID $P_IP $CDB_IP $CDB_PORT $CDB_USER_NAME $CDB_PASS_WD\r"
expect {
    "yes\r" {
        #send "exit\r";
        exit 0;
            }
    timeout {
        exit 1;
        }
}