DROP TABLE IF EXISTS `cAppinfo`;
CREATE TABLE `cAppinfo` (
  `appid` varchar(200) COLLATE utf8_unicode_ci NOT NULL COMMENT 'åº”ç”¨çš„å”¯ä¸€æ ‡è¯†',
  `secret` varchar(300) COLLATE utf8_unicode_ci NOT NULL COMMENT 'åº”ç”¨çš„å¯†é’¥',
  `login_duration` int(11) DEFAULT '30' COMMENT 'é»˜è®¤ç™»é™†æœ‰æ•ˆæœŸï¼Œå•ä½å¤©',
  `session_duration` int(11) DEFAULT '3600' COMMENT 'é»˜è®¤sessionæœ‰æ•ˆæœŸ,å•ä½ç§’',
  `qcloud_appid` varchar(300) COLLATE utf8_unicode_ci DEFAULT 'appid_qcloud',
  `ip` varchar(50) COLLATE utf8_unicode_ci DEFAULT '0.0.0.0',
  PRIMARY KEY (`appid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='å…¨å±€ä¿¡æ¯è¡¨ cAppinfo';
DROP TABLE IF EXISTS `cSessioninfo`;
CREATE TABLE `cSessioninfo` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'æœ¬æ¬¡ä¼šè¯åˆ†é…ç»™ç”¨æˆ·çš„id',
  `skey` varchar(200) COLLATE utf8_unicode_ci NOT NULL COMMENT 'æœ¬æ¬¡ä¼šè¯åˆ†é…ç»™ç”¨æˆ·çš„skey',
  `create_time` int(11) NOT NULL COMMENT 'åˆ›å»ºæ—¶é—´',
  `last_visit_time` int(11) NOT NULL COMMENT 'æœ€è¿‘è®¿é—®æ—¶é—´',
  `open_id` varchar(200) COLLATE utf8_unicode_ci NOT NULL COMMENT 'å¾®ä¿¡ç”¨æˆ·æ ‡è¯†',
  `session_key` varchar(200) COLLATE utf8_unicode_ci NOT NULL COMMENT 'å¾®ä¿¡session',
  `user_info` text COLLATE utf8_unicode_ci,
  KEY `auth` (`id`,`skey`),
  KEY `wexin` (`open_id`,`session_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;