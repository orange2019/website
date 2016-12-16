/*
Navicat MySQL Data Transfer

Source Server         : warm+测试
Source Server Version : 50621
Source Host           : 112.74.83.84:3306
Source Database       : warm_base

Target Server Type    : MYSQL
Target Server Version : 50621
File Encoding         : 65001

Date: 2016-12-16 10:31:13
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for t_auth_component
-- ----------------------------
DROP TABLE IF EXISTS `t_auth_component`;
CREATE TABLE `t_auth_component` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` int(11) NOT NULL DEFAULT '0',
  `price` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `sort` int(11) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of t_auth_component
-- ----------------------------
INSERT INTO `t_auth_component` VALUES ('1', '预约看房', 'book', '', '1', '0', '1', '0', '1480579574', '0', '1');
INSERT INTO `t_auth_component` VALUES ('2', '客户投诉', 'complain', '客户投诉流程，可以前台投诉，后台进行数据管理', '1', '0', '1', '0', '1480911439', '0', '1');
INSERT INTO `t_auth_component` VALUES ('3', '数据统计', 'statistics', '', '2', '100', '1', '1480579634', '1481018388', '1', '1');

-- ----------------------------
-- Table structure for t_auth_group
-- ----------------------------
DROP TABLE IF EXISTS `t_auth_group`;
CREATE TABLE `t_auth_group` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rules` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `sort` int(11) NOT NULL DEFAULT '0',
  `uid` bigint(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of t_auth_group
-- ----------------------------
INSERT INTO `t_auth_group` VALUES ('6', 'administrator', '系统管理员', '14,17,44,40,41,43,45,34,35,36,15,39,18,20,24,22,26,42,19,21,23,25,31,32,33,46,27', '1', '1480492437', '1481618709', '0', '1');
INSERT INTO `t_auth_group` VALUES ('9', 'house_admin', '房源管理', '14,17,44,40,41,43,45,28,29,30,27', '1', '1480907682', '1481080492', '1', '6');
INSERT INTO `t_auth_group` VALUES ('10', 'dg', '电工', '14,17,41,34,35,20,22,24,32', '1', '1481250975', '1481251067', '2', '6');

-- ----------------------------
-- Table structure for t_auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `t_auth_rule`;
CREATE TABLE `t_auth_rule` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `type` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `pid` bigint(20) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `sort` int(11) NOT NULL DEFAULT '0',
  `uid` bigint(20) NOT NULL,
  `display` tinyint(4) NOT NULL DEFAULT '1',
  `component_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of t_auth_rule
-- ----------------------------
INSERT INTO `t_auth_rule` VALUES ('14', 'index/index', '主页', '1', 'pc', '0', '1480497693', '1481018946', '0', '1', '1', '0');
INSERT INTO `t_auth_rule` VALUES ('15', 'system/index', '系统', '1', 'pc', '0', '1480497795', '1480672328', '9', '1', '1', '0');
INSERT INTO `t_auth_rule` VALUES ('17', 'apartment/index', '房源', '1', 'pc', '0', '1480556086', '1480931004', '1', '1', '1', '0');
INSERT INTO `t_auth_rule` VALUES ('18', 'system/user', '成员管理', '1', 'pc', '15', '1480556183', '1480556183', '1', '1', '1', '0');
INSERT INTO `t_auth_rule` VALUES ('19', 'system/group', '角色管理', '1', 'pc', '15', '1480556222', '1480736215', '2', '1', '1', '0');
INSERT INTO `t_auth_rule` VALUES ('20', 'system/userUpdate', '成员添加,编辑', '1', 'pc', '18', '1480556294', '1480556294', '1', '1', '1', '0');
INSERT INTO `t_auth_rule` VALUES ('21', 'system/groupUpdate', '角色添加,编辑', '1', 'pc', '19', '1480556354', '1480556354', '1', '1', '1', '0');
INSERT INTO `t_auth_rule` VALUES ('22', 'system/userStatus', '成员状态改变', '1', 'pc', '18', '1480556423', '1480556423', '2', '1', '1', '0');
INSERT INTO `t_auth_rule` VALUES ('23', 'system/groupStatus', '角色状态改变', '1', 'pc', '19', '1480556546', '1480556546', '2', '1', '1', '0');
INSERT INTO `t_auth_rule` VALUES ('24', 'system/userDelete', '成员删除', '1', 'pc', '18', '1480556580', '1480904255', '2', '1', '1', '0');
INSERT INTO `t_auth_rule` VALUES ('25', 'system/groupRules', '分配权限', '1', 'pc', '19', '1480556625', '1480907880', '3', '1', '1', '0');
INSERT INTO `t_auth_rule` VALUES ('26', 'system/userGroup', '分配角色', '1', 'pc', '18', '1480556671', '1480907843', '4', '1', '0', '0');
INSERT INTO `t_auth_rule` VALUES ('27', 'app/index', 'app首页', '1', 'app', '0', '1480556737', '1480556737', '0', '1', '1', '0');
INSERT INTO `t_auth_rule` VALUES ('28', 'book/index', '所有预约', '1', 'pc', '0', '1480580237', '1480580237', '1', '1', '1', '1');
INSERT INTO `t_auth_rule` VALUES ('29', 'book/deal', '预约处理', '1', 'pc', '28', '1480580307', '1480580307', '1', '1', '1', '1');
INSERT INTO `t_auth_rule` VALUES ('30', 'book/delete', '预约删除', '1', 'pc', '28', '1480580558', '1480580558', '2', '1', '1', '1');
INSERT INTO `t_auth_rule` VALUES ('31', 'system/component', '组件管理', '1', 'pc', '15', '1480589482', '1480589482', '3', '1', '1', '0');
INSERT INTO `t_auth_rule` VALUES ('32', 'system/componentApply', '申请', '1', 'pc', '31', '1480589519', '1480918705', '1', '1', '1', '0');
INSERT INTO `t_auth_rule` VALUES ('33', 'system/componentApplySubmit', '申请提交', '1', 'pc', '31', '1480918689', '1480918689', '3', '1', '0', '0');
INSERT INTO `t_auth_rule` VALUES ('34', 'contract/index', '合同', '1', 'pc', '0', '1480930973', '1480930973', '2', '1', '1', '0');
INSERT INTO `t_auth_rule` VALUES ('35', 'member/index', '租客', '1', 'pc', '0', '1480931044', '1480931044', '3', '1', '1', '0');
INSERT INTO `t_auth_rule` VALUES ('36', 'finance/index', '财务', '1', 'pc', '0', '1480931115', '1480931115', '4', '1', '1', '0');
INSERT INTO `t_auth_rule` VALUES ('39', 'system/projectAdd', '项目添加', '1', 'pc', '15', '1480935978', '1481084400', '0', '1', '1', '0');
INSERT INTO `t_auth_rule` VALUES ('40', 'apartment/project/index', '项目列表', '1', 'pc', '17', '1480936843', '1480995958', '1', '1', '1', '0');
INSERT INTO `t_auth_rule` VALUES ('41', 'apartment/project/update', '房源信息', '1', 'pc', '40', '1480936880', '1480936880', '2', '1', '1', '0');
INSERT INTO `t_auth_rule` VALUES ('42', 'system/userProject', '项目分配', '1', 'pc', '18', '1480991197', '1480991471', '4', '1', '1', '0');
INSERT INTO `t_auth_rule` VALUES ('43', 'apartment/project/houseType', '户型列表', '1', 'pc', '17', '1480996096', '1480996096', '2', '1', '1', '0');
INSERT INTO `t_auth_rule` VALUES ('44', 'apartment/house/index', '房屋列表', '1', 'pc', '17', '1480996174', '1480996184', '0', '1', '1', '0');
INSERT INTO `t_auth_rule` VALUES ('45', 'apartment/project/housekeeper', '管家', '1', 'pc', '17', '1480996266', '1480996291', '4', '1', '1', '0');
INSERT INTO `t_auth_rule` VALUES ('46', 'system/config', '配置', '1', 'pc', '15', '1481618702', '1481618702', '5', '1', '1', '0');

-- ----------------------------
-- Table structure for t_auth_user_group
-- ----------------------------
DROP TABLE IF EXISTS `t_auth_user_group`;
CREATE TABLE `t_auth_user_group` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of t_auth_user_group
-- ----------------------------
INSERT INTO `t_auth_user_group` VALUES ('5', '6', '6');
INSERT INTO `t_auth_user_group` VALUES ('7', '7', '9');
INSERT INTO `t_auth_user_group` VALUES ('8', '8', '9');

-- ----------------------------
-- Table structure for t_project
-- ----------------------------
DROP TABLE IF EXISTS `t_project`;
CREATE TABLE `t_project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of t_project
-- ----------------------------
INSERT INTO `t_project` VALUES ('1', '汇景豪苑', 'huijing', '1', '1480988702', '1480988702', '6');
INSERT INTO `t_project` VALUES ('2', '水木丹华', 'shuimu', '1', '1480988722', '1480988722', '6');
INSERT INTO `t_project` VALUES ('3', '崇文花园', 'chongwen', '0', '1480988842', '1480988842', '6');
INSERT INTO `t_project` VALUES ('4', '海雅缤纷家', 'haiya', '0', '1480989484', '1480989484', '6');
INSERT INTO `t_project` VALUES ('5', '固戍果岭家', 'guolin', '0', '1481538171', '1481538171', '6');
INSERT INTO `t_project` VALUES ('6', '西乡碧海栈', 'bihai', '0', '1481538393', '1481538393', '6');

-- ----------------------------
-- Table structure for t_user
-- ----------------------------
DROP TABLE IF EXISTS `t_user`;
CREATE TABLE `t_user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `pid` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of t_user
-- ----------------------------
INSERT INTO `t_user` VALUES ('1', 'root开发者', 'admin@admin.com', 'e10adc3949ba59abbe56e057f20f883e', '', '1', '0', '1480476943', '0');
INSERT INTO `t_user` VALUES ('4', 'nijkalslu', 'niklaslu@dev.io', 'e10adc3949ba59abbe56e057f20f883e', '', '1', '1480477445', '1480486413', '1');
INSERT INTO `t_user` VALUES ('5', 'johnliu', 'johnliu@dev.io', 'e10adc3949ba59abbe56e057f20f883e', '', '1', '1480477927', '1480477927', '1');
INSERT INTO `t_user` VALUES ('6', 'us', 'us@warmjar.com', 'e10adc3949ba59abbe56e057f20f883e', '123456789', '1', '1480489405', '1480929899', '0');
INSERT INTO `t_user` VALUES ('7', '鲁聪1', 'niklaslu@warmjar.com', 'd7c83f82f269c21cb3636233815fe399', '18676669411', '1', '1480903416', '1481078301', '6');
INSERT INTO `t_user` VALUES ('8', '刘畅', 'johnliu@warmjar.com', 'c8837b23ff8aaa8a2dde915473ce0991', '13715383044', '0', '1481250882', '1481250882', '6');

-- ----------------------------
-- Table structure for t_user_component
-- ----------------------------
DROP TABLE IF EXISTS `t_user_component`;
CREATE TABLE `t_user_component` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `component_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `deadline` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of t_user_component
-- ----------------------------
INSERT INTO `t_user_component` VALUES ('1', '6', '1', '1', '0', '1480920295', '0');

-- ----------------------------
-- Table structure for t_user_config
-- ----------------------------
DROP TABLE IF EXISTS `t_user_config`;
CREATE TABLE `t_user_config` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) NOT NULL,
  `app_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `app_secret` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mch_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `domain` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of t_user_config
-- ----------------------------
INSERT INTO `t_user_config` VALUES ('2', '6', '1', '2', '3', '4');

-- ----------------------------
-- Table structure for t_user_project
-- ----------------------------
DROP TABLE IF EXISTS `t_user_project`;
CREATE TABLE `t_user_project` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `projects` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of t_user_project
-- ----------------------------
INSERT INTO `t_user_project` VALUES ('1', '7', '1,2,3');
INSERT INTO `t_user_project` VALUES ('2', '8', '2');
