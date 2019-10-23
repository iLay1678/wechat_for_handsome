/*
 Navicat Premium Data Transfer

 Source Server         : 47.103.64.44
 Source Server Type    : MySQL
 Source Server Version : 50562
 Source Host           : 47.103.64.44:3306
 Source Schema         : cross

 Target Server Type    : MySQL
 Target Server Version : 50562
 File Encoding         : 65001

 Date: 24/10/2019 01:01:18
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for cross
-- ----------------------------
DROP TABLE IF EXISTS `cross`;
CREATE TABLE `cross`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `timecode` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `cid` int(11) NULL DEFAULT NULL,
  `mid` int(11) NULL DEFAULT NULL,
  `msg_type` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

SET FOREIGN_KEY_CHECKS = 1;
