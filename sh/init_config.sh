#!/bin/sh
echo "{\"serverHost\": \"$1\",\n\"authServerUrl\": \"$2\",\n\"tunnelServerUrl\": \"https://ws.qcloud.com\",\n\"tunnelSignatureKey\": \"$3\",\n\"networkProxy\": \"127.0.0.1:8888\"}" > /etc/qcloud/sdk.config
