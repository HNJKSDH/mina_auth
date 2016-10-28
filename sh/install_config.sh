#!/usr/bin/expect -f
set timeout 10
set IP [lindex $argv 0]
set PASSWORD [lindex $argv 1]
set serverHost [lindex $argv 2]
set authIp [lindex $argv 3]
set tunnelSignatureKey [lindex $argv 4]

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
send "/etc/qcloud/init_config.sh $serverHost $authIp $tunnelSignatureKey \r"
expect {
    "yes\r" {
        #send "exit\r";
        exit 0;
            }
    timeout {
        exit 1;
        }
}