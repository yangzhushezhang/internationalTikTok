/*
 Navicat Premium Data Transfer

 Source Server         : 國際抖音
 Source Server Type    : MySQL
 Source Server Version : 80024
 Source Host           : 47.241.62.70:3306
 Source Schema         : test_tool

 Target Server Type    : MySQL
 Target Server Version : 80024
 File Encoding         : 65001

 Date: 26/03/2022 16:51:29
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for admin
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` int DEFAULT NULL,
  `status` int DEFAULT '0',
  `send_message` int DEFAULT NULL COMMENT '1发送信息 2 不发信息',
  `version` int DEFAULT '0',
  `version_forWs` int DEFAULT '0',
  `cookies` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for collection_fans
-- ----------------------------
DROP TABLE IF EXISTS `collection_fans`;
CREATE TABLE `collection_fans` (
  `id` int NOT NULL AUTO_INCREMENT,
  `image` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `sex` varchar(255) DEFAULT NULL,
  `signature` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `created` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3;

-- ----------------------------
-- Table structure for config
-- ----------------------------
DROP TABLE IF EXISTS `config`;
CREATE TABLE `config` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nickname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '手机别名',
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '手机的唯一标识',
  `api_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '百度的 API Key',
  `secret_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '百度的 Secret Key',
  `attention_num` int DEFAULT '0' COMMENT '关注个数 0不限制个数 其他 ',
  `status` int DEFAULT '2' COMMENT '0 未使用 1正在运行 2被封  //运行状态',
  `created_at` int DEFAULT NULL COMMENT '创建时间',
  `sendMessage_time` int DEFAULT '3' COMMENT '每隔几个小时去私聊',
  `impower` int DEFAULT '0' COMMENT '0 没授权 1授权',
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '机型',
  `if_get_follow` int DEFAULT '1' COMMENT '0 没有 获取    1获取了',
  `update_at` int DEFAULT '0' COMMENT '更新时间 控制发信息的时间',
  `permissions_for_message` int DEFAULT '0' COMMENT '私聊权限 0不可以 1可以',
  `attentionTheLastTime` int DEFAULT NULL COMMENT '关注粉丝最后一次更新时间',
  `if_banned` int DEFAULT '0' COMMENT '0 正常 1被封  //',
  `false_fan` int DEFAULT '0' COMMENT '0正常  1被封',
  `model_for_fans` int DEFAULT '0' COMMENT '//加粉模式 0  无限 其他有限',
  `running_model` int DEFAULT '1' COMMENT '1 加粉 2私聊',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `id` (`id`) USING BTREE,
  FULLTEXT KEY `nickname` (`nickname`),
  FULLTEXT KEY `uuid` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=1099 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for contact
-- ----------------------------
DROP TABLE IF EXISTS `contact`;
CREATE TABLE `contact` (
  `id` int NOT NULL AUTO_INCREMENT,
  `contact` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '// 联系方式',
  `created_at` int DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for cookies
-- ----------------------------
DROP TABLE IF EXISTS `cookies`;
CREATE TABLE `cookies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cookies` text COLLATE utf8mb4_general_ci,
  `status` int DEFAULT NULL COMMENT '1 可以用 2不可以用',
  `created` int DEFAULT NULL,
  `username` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mail` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `updated` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for cw_address
-- ----------------------------
DROP TABLE IF EXISTS `cw_address`;
CREATE TABLE `cw_address` (
  `id` int NOT NULL AUTO_INCREMENT,
  `axie_id` int NOT NULL,
  `old_address` varchar(255) DEFAULT NULL,
  `new_address` varchar(255) DEFAULT NULL,
  `status` int DEFAULT NULL COMMENT '1 查询中 2查询失败  3查询成功',
  `created_at` int DEFAULT NULL,
  `updated_at` int DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `eth` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2202 DEFAULT CHARSET=utf8mb3;

-- ----------------------------
-- Table structure for cw_collect
-- ----------------------------
DROP TABLE IF EXISTS `cw_collect`;
CREATE TABLE `cw_collect` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cw_id` varchar(255) NOT NULL COMMENT '宝宝id',
  `cw_class` varchar(255) NOT NULL COMMENT '种类',
  `cw_stage` int DEFAULT NULL COMMENT '宝宝阶段等级',
  `cw_breedCount` int DEFAULT NULL COMMENT '宝宝品种计数',
  `cw_level` int DEFAULT NULL COMMENT '等级',
  `cw_currentPriceUSD` decimal(11,2) DEFAULT '0.00',
  `cw_Eyes_name` varchar(255) DEFAULT NULL,
  `cw_Eyes_stage` varchar(255) DEFAULT NULL,
  `cw_Eyes_abilities_name_0` varchar(255) DEFAULT NULL,
  `cw_Ears_name` varchar(255) DEFAULT NULL,
  `cw_Ears_stage` varchar(255) DEFAULT NULL,
  `cw_Ears_abilities_name_0` varchar(255) DEFAULT NULL,
  `cw_Back_name` varchar(255) DEFAULT NULL,
  `cw_Back_stage` varchar(255) DEFAULT NULL,
  `cw_Back_abilities_name_0` varchar(255) DEFAULT NULL,
  `cw_Mouth_name` varchar(255) DEFAULT NULL,
  `cw_Mouth_stage` varchar(255) DEFAULT NULL,
  `cw_Mouth_abilities_name_0` varchar(255) DEFAULT NULL,
  `cw_Horn_name` varchar(255) DEFAULT NULL,
  `cw_Horn_stage` varchar(255) DEFAULT NULL,
  `cw_Horn_abilities_name_0` varchar(255) DEFAULT NULL,
  `cw_Tail_name` varchar(255) DEFAULT NULL,
  `cw_Tail_stage` varchar(255) DEFAULT NULL,
  `cw_Tail_abilities_name_0` varchar(255) DEFAULT NULL,
  `created_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- ----------------------------
-- Table structure for cw_collect_copy1
-- ----------------------------
DROP TABLE IF EXISTS `cw_collect_copy1`;
CREATE TABLE `cw_collect_copy1` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cw_id` varchar(255) NOT NULL COMMENT '宝宝id',
  `cw_class` varchar(255) NOT NULL COMMENT '种类',
  `cw_stage` int DEFAULT NULL COMMENT '宝宝阶段等级',
  `cw_breedCount` int DEFAULT NULL COMMENT '宝宝品种计数',
  `cw_level` int DEFAULT NULL COMMENT '等级',
  `cw_currentPriceUSD` decimal(11,2) DEFAULT '0.00',
  `cw_Eyes_name` varchar(255) DEFAULT NULL,
  `cw_Eyes_stage` varchar(255) DEFAULT NULL,
  `cw_Eyes_abilities_name_0` varchar(255) DEFAULT NULL,
  `cw_Ears_name` varchar(255) DEFAULT NULL,
  `cw_Ears_stage` varchar(255) DEFAULT NULL,
  `cw_Ears_abilities_name_0` varchar(255) DEFAULT NULL,
  `cw_Back_name` varchar(255) DEFAULT NULL,
  `cw_Back_stage` varchar(255) DEFAULT NULL,
  `cw_Back_abilities_name_0` varchar(255) DEFAULT NULL,
  `cw_Mouth_name` varchar(255) DEFAULT NULL,
  `cw_Mouth_stage` varchar(255) DEFAULT NULL,
  `cw_Mouth_abilities_name_0` varchar(255) DEFAULT NULL,
  `cw_Horn_name` varchar(255) DEFAULT NULL,
  `cw_Horn_stage` varchar(255) DEFAULT NULL,
  `cw_Horn_abilities_name_0` varchar(255) DEFAULT NULL,
  `cw_Tail_name` varchar(255) DEFAULT NULL,
  `cw_Tail_stage` varchar(255) DEFAULT NULL,
  `cw_Tail_abilities_name_0` varchar(255) DEFAULT NULL,
  `created_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39141 DEFAULT CHARSET=utf8mb3;

-- ----------------------------
-- Table structure for cw_model
-- ----------------------------
DROP TABLE IF EXISTS `cw_model`;
CREATE TABLE `cw_model` (
  `id` int NOT NULL AUTO_INCREMENT,
  `md5` varchar(255) NOT NULL,
  `remark` varchar(255) NOT NULL,
  `eyes` varchar(255) DEFAULT '1',
  `ears` varchar(255) DEFAULT '1',
  `horn` varchar(255) DEFAULT '1',
  `back` varchar(255) DEFAULT '1',
  `mouth` varchar(255) DEFAULT '1',
  `tail` varchar(255) DEFAULT '1',
  `created_at` int DEFAULT NULL,
  `kinds` varchar(255) DEFAULT '1',
  `price` decimal(10,2) DEFAULT NULL,
  `eth_price` decimal(10,6) DEFAULT NULL,
  `times` int DEFAULT '0',
  `switch` int DEFAULT '0',
  `updated_at` int DEFAULT NULL,
  `count_nums` int DEFAULT '0' COMMENT '想死匹配的共个数',
  `low_five_price` varchar(255) DEFAULT NULL,
  `profit` decimal(11,2) DEFAULT '0.00',
  `avg_difference` float(11,2) DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9070 DEFAULT CHARSET=utf8mb3;

-- ----------------------------
-- Table structure for cw_model_copy1
-- ----------------------------
DROP TABLE IF EXISTS `cw_model_copy1`;
CREATE TABLE `cw_model_copy1` (
  `id` int NOT NULL AUTO_INCREMENT,
  `md5` varchar(255) NOT NULL,
  `remark` varchar(255) NOT NULL,
  `eyes` varchar(255) DEFAULT '1',
  `ears` varchar(255) DEFAULT '1',
  `horn` varchar(255) DEFAULT '1',
  `back` varchar(255) DEFAULT '1',
  `mouth` varchar(255) DEFAULT '1',
  `tail` varchar(255) DEFAULT '1',
  `created_at` int DEFAULT NULL,
  `kinds` varchar(255) DEFAULT '1',
  `price` decimal(10,2) DEFAULT NULL,
  `eth_price` decimal(10,6) DEFAULT NULL,
  `times` int DEFAULT '0',
  `switch` int DEFAULT '0',
  `updated_at` int DEFAULT NULL,
  `count_nums` int DEFAULT '0' COMMENT '想死匹配的共个数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8734 DEFAULT CHARSET=utf8mb3;

-- ----------------------------
-- Table structure for cw_model_copy2
-- ----------------------------
DROP TABLE IF EXISTS `cw_model_copy2`;
CREATE TABLE `cw_model_copy2` (
  `id` int NOT NULL AUTO_INCREMENT,
  `md5` varchar(255) NOT NULL,
  `remark` varchar(255) NOT NULL,
  `eyes` varchar(255) DEFAULT '1',
  `ears` varchar(255) DEFAULT '1',
  `horn` varchar(255) DEFAULT '1',
  `back` varchar(255) DEFAULT '1',
  `mouth` varchar(255) DEFAULT '1',
  `tail` varchar(255) DEFAULT '1',
  `created_at` int DEFAULT NULL,
  `kinds` varchar(255) DEFAULT '1',
  `price` decimal(10,2) DEFAULT NULL,
  `eth_price` decimal(10,6) DEFAULT NULL,
  `times` int DEFAULT '0',
  `switch` int DEFAULT '0',
  `updated_at` int DEFAULT NULL,
  `count_nums` int DEFAULT '0' COMMENT '想死匹配的共个数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9070 DEFAULT CHARSET=utf8mb3;

-- ----------------------------
-- Table structure for cw_model_copy3
-- ----------------------------
DROP TABLE IF EXISTS `cw_model_copy3`;
CREATE TABLE `cw_model_copy3` (
  `id` int NOT NULL AUTO_INCREMENT,
  `md5` varchar(255) NOT NULL,
  `remark` varchar(255) NOT NULL,
  `eyes` varchar(255) DEFAULT '1',
  `ears` varchar(255) DEFAULT '1',
  `horn` varchar(255) DEFAULT '1',
  `back` varchar(255) DEFAULT '1',
  `mouth` varchar(255) DEFAULT '1',
  `tail` varchar(255) DEFAULT '1',
  `created_at` int DEFAULT NULL,
  `kinds` varchar(255) DEFAULT '1',
  `price` decimal(10,2) DEFAULT NULL,
  `eth_price` decimal(10,6) DEFAULT NULL,
  `times` int DEFAULT '0',
  `switch` int DEFAULT '0',
  `updated_at` int DEFAULT NULL,
  `count_nums` int DEFAULT '0' COMMENT '想死匹配的共个数',
  `low_five_price` varchar(255) DEFAULT NULL,
  `profit` decimal(11,2) DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9070 DEFAULT CHARSET=utf8mb3;

-- ----------------------------
-- Table structure for douyin_name
-- ----------------------------
DROP TABLE IF EXISTS `douyin_name`;
CREATE TABLE `douyin_name` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3859997 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for dy_url
-- ----------------------------
DROP TABLE IF EXISTS `dy_url`;
CREATE TABLE `dy_url` (
  `id` int NOT NULL AUTO_INCREMENT,
  `Dy_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Dy_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int DEFAULT NULL COMMENT '//0 没有使用 1正在使用 2使用过了',
  `created_at` int DEFAULT NULL,
  `updated_at` int DEFAULT NULL COMMENT '// 链接取走时间',
  `nickname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '//取走设备的昵称',
  `use_time` int DEFAULT NULL COMMENT '//这个 肯定是已经链接已经使用完的 一个时间统计',
  `comments` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '这里是 脚本上传的 这条链接的评论人数',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=47773 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for fans_t
-- ----------------------------
DROP TABLE IF EXISTS `fans_t`;
CREATE TABLE `fans_t` (
  `id` int NOT NULL AUTO_INCREMENT,
  `uid` varchar(255) NOT NULL,
  `nickname` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL,
  `created_at` varchar(255) NOT NULL,
  `last_time_of_vido` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- ----------------------------
-- Table structure for follow
-- ----------------------------
DROP TABLE IF EXISTS `follow`;
CREATE TABLE `follow` (
  `id` int NOT NULL AUTO_INCREMENT,
  `phone_id` int DEFAULT NULL,
  `following` int NOT NULL COMMENT '我关注的',
  `followers` int DEFAULT NULL COMMENT '粉丝数',
  `created_at` int NOT NULL,
  `nickname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '别名',
  `add_following` int DEFAULT '0' COMMENT '新增关注',
  `add_followers` int DEFAULT '0' COMMENT '新增粉丝数',
  `status` int DEFAULT '1' COMMENT '1正常 2被封',
  `date` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '日期 更新日期,',
  `updated_at` int DEFAULT NULL COMMENT '更新时间',
  `real_concerns` int unsigned DEFAULT NULL COMMENT '实际手动关注',
  `sixin` int DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2639 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for matching
-- ----------------------------
DROP TABLE IF EXISTS `matching`;
CREATE TABLE `matching` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cw_id` int NOT NULL,
  `created_at` int NOT NULL,
  `price` decimal(10,4) DEFAULT NULL COMMENT '// 匹配时候 宠物的价格',
  `mode_price` decimal(10,4) DEFAULT NULL,
  `low_price` decimal(10,4) DEFAULT NULL,
  `status` int DEFAULT NULL,
  `model_id` int NOT NULL,
  `delay_index` int DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=29460 DEFAULT CHARSET=utf8mb3 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for monitor_fans
-- ----------------------------
DROP TABLE IF EXISTS `monitor_fans`;
CREATE TABLE `monitor_fans` (
  `id` int NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '抖音的用户名',
  `uid` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `send_text` text COLLATE utf8mb4_general_ci,
  `image_url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created` int DEFAULT NULL,
  `sex` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `signature` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `video_id` int DEFAULT NULL COMMENT '视频id',
  `country` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2834 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for monitor_video
-- ----------------------------
DROP TABLE IF EXISTS `monitor_video`;
CREATE TABLE `monitor_video` (
  `id` int NOT NULL AUTO_INCREMENT,
  `vID` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `up_id` int DEFAULT NULL COMMENT 'up主的id',
  `created` int DEFAULT NULL,
  `release_time` int DEFAULT NULL,
  `status` int DEFAULT '1' COMMENT '1 没有被使用过 2被使用过',
  `end` int DEFAULT NULL,
  `updated` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vid` (`vID`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for monitortiktokupname
-- ----------------------------
DROP TABLE IF EXISTS `monitortiktokupname`;
CREATE TABLE `monitortiktokupname` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` int DEFAULT '1' COMMENT '1正常 2 不监听了',
  `created` int DEFAULT NULL,
  `uid` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for recently_sold
-- ----------------------------
DROP TABLE IF EXISTS `recently_sold`;
CREATE TABLE `recently_sold` (
  `id` int NOT NULL AUTO_INCREMENT,
  `axie_id` varchar(255) NOT NULL DEFAULT '-2',
  `status` int NOT NULL COMMENT '在售情况 -1 查询失败 -2正常在查询 ',
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL,
  `kinds` varchar(255) DEFAULT NULL,
  `eyes` varchar(255) DEFAULT NULL,
  `ears` varchar(255) DEFAULT NULL,
  `back` varchar(255) DEFAULT NULL,
  `mouth` varchar(255) DEFAULT NULL,
  `horn` varchar(255) DEFAULT NULL,
  `tail` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1005323 DEFAULT CHARSET=utf8mb3;

-- ----------------------------
-- Table structure for recently_sold_copy1
-- ----------------------------
DROP TABLE IF EXISTS `recently_sold_copy1`;
CREATE TABLE `recently_sold_copy1` (
  `id` int NOT NULL AUTO_INCREMENT,
  `axie_id` varchar(255) NOT NULL DEFAULT '-2',
  `status` int NOT NULL COMMENT '在售情况 -1 查询失败 -2正常在查询 ',
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL,
  `kinds` varchar(255) DEFAULT NULL,
  `eyes` varchar(255) DEFAULT NULL,
  `ears` varchar(255) DEFAULT NULL,
  `back` varchar(255) DEFAULT NULL,
  `mouth` varchar(255) DEFAULT NULL,
  `horn` varchar(255) DEFAULT NULL,
  `tail` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=82118 DEFAULT CHARSET=utf8mb3;

-- ----------------------------
-- Table structure for telegram
-- ----------------------------
DROP TABLE IF EXISTS `telegram`;
CREATE TABLE `telegram` (
  `id` int NOT NULL AUTO_INCREMENT,
  `group_id` varchar(255) NOT NULL COMMENT '//群id',
  `update_id` int NOT NULL,
  `from_id` int NOT NULL,
  `from_first_name` varchar(255) NOT NULL,
  `from_last_name` varchar(255) DEFAULT NULL,
  `from_username` varchar(255) NOT NULL,
  `message_id` int NOT NULL,
  `text` varchar(255) NOT NULL,
  `date` int NOT NULL,
  `status` int DEFAULT '0' COMMENT '//0 没有 发送过 1已结发送了',
  `created_at` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb3;

-- ----------------------------
-- Table structure for total
-- ----------------------------
DROP TABLE IF EXISTS `total`;
CREATE TABLE `total` (
  `id` int NOT NULL AUTO_INCREMENT,
  `all_following` int NOT NULL,
  `all_sixin` int NOT NULL COMMENT '//私信数量',
  `date` varchar(255) NOT NULL COMMENT '//日期',
  `all_real_following` int DEFAULT NULL COMMENT '// 脚本实际关注的数量',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- ----------------------------
-- Table structure for two_douyin
-- ----------------------------
DROP TABLE IF EXISTS `two_douyin`;
CREATE TABLE `two_douyin` (
  `id` int NOT NULL AUTO_INCREMENT,
  `douyi_id` varchar(255) NOT NULL,
  `updated_at` int NOT NULL,
  `created_at` int NOT NULL,
  `status` int NOT NULL DEFAULT '0' COMMENT '0 正常 1 已经被取走了 2 使用完毕',
  `nickname` varchar(255) DEFAULT NULL COMMENT '//收集者',
  `username` varchar(255) DEFAULT NULL COMMENT '//使用者',
  `remark` varchar(255) DEFAULT NULL COMMENT '//备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7184 DEFAULT CHARSET=utf8mb3;

-- ----------------------------
-- Table structure for uid_t
-- ----------------------------
DROP TABLE IF EXISTS `uid_t`;
CREATE TABLE `uid_t` (
  `id` int NOT NULL AUTO_INCREMENT,
  `uid` varchar(255) NOT NULL,
  `nickname` varchar(255) NOT NULL,
  `status` int NOT NULL COMMENT '1 未使用  2使用',
  `created_at` varchar(255) NOT NULL COMMENT '创建时间',
  `username` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- ----------------------------
-- Table structure for userid
-- ----------------------------
DROP TABLE IF EXISTS `userid`;
CREATE TABLE `userid` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` varchar(255) NOT NULL,
  `status` int NOT NULL COMMENT '1 未使用  2使用',
  `updated_at` int NOT NULL,
  `nickname` varchar(255) NOT NULL COMMENT '使用的手机',
  `location` int NOT NULL DEFAULT '1' COMMENT '1 越南 2日本 3韩国',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6428 DEFAULT CHARSET=utf8mb3;

-- ----------------------------
-- Table structure for video_link
-- ----------------------------
DROP TABLE IF EXISTS `video_link`;
CREATE TABLE `video_link` (
  `id` int NOT NULL AUTO_INCREMENT,
  `link` varchar(255) NOT NULL,
  `status` int DEFAULT '1',
  `created_at` int NOT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb3;

-- ----------------------------
-- Table structure for weishi_name
-- ----------------------------
DROP TABLE IF EXISTS `weishi_name`;
CREATE TABLE `weishi_name` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=276 DEFAULT CHARSET=utf8mb3;

SET FOREIGN_KEY_CHECKS = 1;
