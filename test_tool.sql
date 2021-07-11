/*
 Navicat Premium Data Transfer

 Source Server         : 56565656
 Source Server Type    : MySQL
 Source Server Version : 50650
 Source Host           : 47.242.44.105:3306
 Source Schema         : test_tool

 Target Server Type    : MySQL
 Target Server Version : 50650
 File Encoding         : 65001

 Date: 09/07/2021 13:51:11
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for admin
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` int(11) NULL DEFAULT NULL,
  `status` int(1) NULL DEFAULT 0,
  `send_message` int(1) NULL DEFAULT NULL COMMENT '1发送信息 2 不发信息',
  `version` int(11) NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for config
-- ----------------------------
DROP TABLE IF EXISTS `config`;
CREATE TABLE `config`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nickname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '手机别名',
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '手机的唯一标识',
  `api_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '百度的 API Key',
  `secret_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '百度的 Secret Key',
  `attention_num` int(11) NULL DEFAULT 0 COMMENT '关注个数 0不限制个数 其他 ',
  `status` int(11) NULL DEFAULT 2 COMMENT '0 未使用 1正在运行 2被封  //运行状态',
  `created_at` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `sendMessage_time` int(11) NULL DEFAULT 3 COMMENT '每隔几个小时去私聊',
  `impower` int(255) NULL DEFAULT 0 COMMENT '0 没授权 1授权',
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '机型',
  `if_get_follow` int(1) NULL DEFAULT 1 COMMENT '0 没有 获取    1获取了',
  `update_at` int(11) NULL DEFAULT 0 COMMENT '更新时间 控制发信息的时间',
  `permissions_for_message` int(1) NULL DEFAULT 0 COMMENT '私聊权限 0不可以 1可以',
  `attentionTheLastTime` int(11) NULL DEFAULT NULL COMMENT '关注粉丝最后一次更新时间',
  `if_banned` int(1) NULL DEFAULT 0 COMMENT '0 正常 1被封  //',
  `false_fan` int(1) NULL DEFAULT 0 COMMENT '0正常  1被封',
  `model_for_fans` int(10) NULL DEFAULT 0 COMMENT '//加粉模式 0  无限 其他有限',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id`(`id`) USING BTREE,
  FULLTEXT INDEX `nickname`(`nickname`),
  FULLTEXT INDEX `uuid`(`uuid`)
) ENGINE = InnoDB AUTO_INCREMENT = 1093 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for contact
-- ----------------------------
DROP TABLE IF EXISTS `contact`;
CREATE TABLE `contact`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contact` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '// 联系方式',
  `created_at` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 35 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for douyin_name
-- ----------------------------
DROP TABLE IF EXISTS `douyin_name`;
CREATE TABLE `douyin_name`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `name`(`name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2741134 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for dy_url
-- ----------------------------
DROP TABLE IF EXISTS `dy_url`;
CREATE TABLE `dy_url`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Dy_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Dy_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int(1) NULL DEFAULT NULL COMMENT '//0 没有使用 1正在使用 2使用过了',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NULL DEFAULT NULL COMMENT '// 链接取走时间',
  `nickname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '//取走设备的昵称',
  `use_time` int(11) NULL DEFAULT NULL COMMENT '//这个 肯定是已经链接已经使用完的 一个时间统计',
  `comments` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '这里是 脚本上传的 这条链接的评论人数',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 18210 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for follow
-- ----------------------------
DROP TABLE IF EXISTS `follow`;
CREATE TABLE `follow`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `phone_id` int(1) NULL DEFAULT NULL,
  `following` int(11) NOT NULL COMMENT '我关注的',
  `followers` int(11) NULL DEFAULT NULL COMMENT '粉丝数',
  `created_at` int(11) NOT NULL,
  `nickname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '别名',
  `add_following` int(11) NULL DEFAULT 0 COMMENT '新增关注',
  `add_followers` int(11) NULL DEFAULT 0 COMMENT '新增粉丝数',
  `status` int(1) NULL DEFAULT 1 COMMENT '1正常 2被封',
  `date` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '日期 更新日期,',
  `updated_at` int(11) NULL DEFAULT NULL COMMENT '更新时间',
  `real_concerns` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '实际手动关注',
  `sixin` int(11) NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1637 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for total
-- ----------------------------
DROP TABLE IF EXISTS `total`;
CREATE TABLE `total`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `all_following` int(11) NOT NULL,
  `all_sixin` int(11) NOT NULL COMMENT '//私信数量',
  `date` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '//日期',
  `all_real_following` int(11) NULL DEFAULT NULL COMMENT '// 脚本实际关注的数量',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for two_douyin
-- ----------------------------
DROP TABLE IF EXISTS `two_douyin`;
CREATE TABLE `two_douyin`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `douyi_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `updated_at` int(10) NOT NULL,
  `created_at` int(10) NOT NULL,
  `status` int(1) NOT NULL DEFAULT 0 COMMENT '0 正常 1 已经被取走了 2 使用完毕',
  `nickname` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '//收集者',
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '//使用者',
  `remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '//备注',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7143 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

SET FOREIGN_KEY_CHECKS = 1;
