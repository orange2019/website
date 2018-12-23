/*
 Navicat Premium Data Transfer

 Source Server         : 聚仁
 Source Server Type    : MySQL
 Source Server Version : 50628
 Source Host           : 59939c0a9a983.gz.cdb.myqcloud.com:5579
 Source Schema         : 2018_juren

 Target Server Type    : MySQL
 Target Server Version : 50628
 File Encoding         : 65001

 Date: 20/12/2018 22:32:08
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for t_album
-- ----------------------------
DROP TABLE IF EXISTS `t_album`;
CREATE TABLE `t_album` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `content` text NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `type` varchar(12) NOT NULL,
  `relation_type` varchar(12) NOT NULL,
  `relation_id` bigint(11) NOT NULL DEFAULT '0',
  `sort` int(11) NOT NULL DEFAULT '0',
  `link` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_album
-- ----------------------------
BEGIN;
INSERT INTO `t_album` VALUES (3, '2', '/uploads/image/20161219/20161219232434_26162.jpg', '3', '4', 0, 1482412231, 1482412349, 'banner', 'category', 6, 1, '');
INSERT INTO `t_album` VALUES (4, '', '/static/kindeditor/php/../../../uploads/image/20181031/20181031003730_80549.jpg', '', '', 1, 1540917455, 1540917455, '', 'category', 2, 0, '');
COMMIT;

-- ----------------------------
-- Table structure for t_app
-- ----------------------------
DROP TABLE IF EXISTS `t_app`;
CREATE TABLE `t_app` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `app_id` varchar(255) NOT NULL,
  `app_secret` varchar(255) NOT NULL,
  `public_key` varchar(255) NOT NULL DEFAULT '',
  `access_token` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_app
-- ----------------------------
BEGIN;
INSERT INTO `t_app` VALUES (4, 'APP开发', '1fdbbe847be4d2aa6f39a5350329106d', 'MWZkYmJlODQ3YmU0ZDJhYTZmMzlhNTM1MDMyOTEwNmQ=', 'lc19890512', '', 1, 1492057637, 1492057637);
COMMIT;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of t_auth_group
-- ----------------------------
BEGIN;
INSERT INTO `t_auth_group` VALUES (6, 'administrator', '系统管理员', '14,52,17,56,54,55,58,59,53,47,63,64,57,60,69,70,71,61,65,67,66,68,15,39,48,49,50,51,18,20,24,22,42,26,19,21,23,25,27', 1, 1480492437, 1540914062, 0, 1);
INSERT INTO `t_auth_group` VALUES (9, 'house_admin', '房源管理', '14,17,44,40,41,43,45,28,29,30,27', 1, 1480907682, 1481080492, 1, 6);
INSERT INTO `t_auth_group` VALUES (10, 'dg', '电工', '14,17,41,34,35,20,22,24,32', 1, 1481250975, 1481251067, 2, 6);
INSERT INTO `t_auth_group` VALUES (11, 'web', '网站管理员', '14,52,17,56,54,55,58,59,53,47,63,64,57,60,69,70,71,61,65,67,66,68', 1, 1490456390, 1490459010, 1, 2);
COMMIT;

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
  `icon` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=118 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of t_auth_rule
-- ----------------------------
BEGIN;
INSERT INTO `t_auth_rule` VALUES (14, 'index/index', '面板导航', 1, 'pc', 0, 1480497693, 1490970719, 0, 1, 1, 0, 'home');
INSERT INTO `t_auth_rule` VALUES (15, 'system/index', '系统设置', 1, 'pc', 0, 1480497795, 1490970825, 6, 1, 1, 0, 'cog');
INSERT INTO `t_auth_rule` VALUES (17, 'category/index', '内容管理', 1, 'pc', 0, 1480556086, 1490970727, 1, 1, 1, 0, 'list');
INSERT INTO `t_auth_rule` VALUES (18, 'system/user', '成员管理', 1, 'pc', 15, 1480556183, 1480556183, 1, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (19, 'system/group', '角色管理', 1, 'pc', 15, 1480556222, 1480736215, 2, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (20, 'system/userUpdate', '成员添加,编辑', 1, 'pc', 18, 1480556294, 1480556294, 1, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (21, 'system/groupUpdate', '角色添加,编辑', 1, 'pc', 19, 1480556354, 1480556354, 1, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (22, 'system/userStatus', '成员状态改变', 1, 'pc', 18, 1480556423, 1480556423, 2, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (23, 'system/groupStatus', '角色状态改变', 1, 'pc', 19, 1480556546, 1480556546, 2, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (24, 'system/userDelete', '成员删除', 1, 'pc', 18, 1480556580, 1480904255, 2, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (25, 'system/groupRules', '分配权限', 1, 'pc', 19, 1480556625, 1480907880, 3, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (26, 'system/userGroup', '分配角色', 1, 'pc', 18, 1480556671, 1480907843, 4, 1, 0, 0, '');
INSERT INTO `t_auth_rule` VALUES (27, 'app/index', 'app首页', 1, 'app', 0, 1480556737, 1480556737, 0, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (28, 'book/index', '所有预约', 1, 'pc', 0, 1480580237, 1480580237, 1, 1, 1, 1, '');
INSERT INTO `t_auth_rule` VALUES (29, 'book/deal', '预约处理', 1, 'pc', 28, 1480580307, 1480580307, 1, 1, 1, 1, '');
INSERT INTO `t_auth_rule` VALUES (30, 'book/delete', '预约删除', 1, 'pc', 28, 1480580558, 1480580558, 2, 1, 1, 1, '');
INSERT INTO `t_auth_rule` VALUES (31, 'system/component', '组件管理', 1, 'pc', 15, 1480589482, 1482130210, 3, 1, 0, 0, '');
INSERT INTO `t_auth_rule` VALUES (32, 'system/componentApply', '申请', 1, 'pc', 31, 1480589519, 1480918705, 1, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (33, 'system/componentApplySubmit', '申请提交', 1, 'pc', 31, 1480918689, 1480918689, 3, 1, 0, 0, '');
INSERT INTO `t_auth_rule` VALUES (35, 'member/index', '用户数据', 1, 'pc', 0, 1480931044, 1490970805, 3, 1, 1, 0, 'users');
INSERT INTO `t_auth_rule` VALUES (39, 'project/index', '项目管理', 1, 'pc', 15, 1480935978, 1482136143, 0, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (42, 'system/userProject', '项目分配', 1, 'pc', 18, 1480991197, 1480991471, 4, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (47, 'posts/index', '文档列表', 1, 'pc', 17, 1482130464, 1482304067, 3, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (48, 'project/add', '项目添加', 1, 'pc', 39, 1482134342, 1482134342, 1, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (49, 'project/update', '项目更新', 1, 'pc', 39, 1482134388, 1482134416, 2, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (50, 'project/status', '项目状态', 1, 'pc', 39, 1482134462, 1482134462, 3, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (51, 'project/delete', '项目删除', 1, 'pc', 39, 1482134486, 1482134486, 4, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (52, 'index/current', '设置当前项目', 1, 'pc', 14, 1482134624, 1482137123, 1, 1, 0, 0, '');
INSERT INTO `t_auth_rule` VALUES (53, 'category/add', '页面添加', 1, 'pc', 17, 1482289669, 1482304363, 2, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (54, 'category/edit', '页面编辑', 1, 'pc', 56, 1482289696, 1482304025, 2, 1, 0, 0, '');
INSERT INTO `t_auth_rule` VALUES (55, 'category/status', '页面状态', 1, 'pc', 56, 1482289746, 1482329732, 3, 1, 0, 0, '');
INSERT INTO `t_auth_rule` VALUES (56, 'category/index', '页面栏目', 1, 'pc', 17, 1482303999, 1482304039, 1, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (57, 'posts/update', '文档添加', 1, 'pc', 17, 1482304411, 1482376358, 4, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (58, 'category/content', '页面内容', 1, 'pc', 56, 1482329759, 1482329767, 3, 1, 0, 0, '');
INSERT INTO `t_auth_rule` VALUES (59, 'category/delete', '页面删除', 1, 'pc', 56, 1482331002, 1482331002, 4, 1, 0, 0, '');
INSERT INTO `t_auth_rule` VALUES (60, 'data/index', '数据模型', 1, 'pc', 17, 1482331456, 1483631356, 5, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (61, 'album/index', '图片画册', 1, 'pc', 17, 1482331499, 1483631992, 6, 1, 0, 0, '');
INSERT INTO `t_auth_rule` VALUES (62, 'component/index', '组件', 1, 'pc', 0, 1482331618, 1489933033, 9, 1, 0, 0, '');
INSERT INTO `t_auth_rule` VALUES (63, 'posts/status', '文档状态', 1, 'pc', 47, 1482393935, 1482393935, 1, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (64, 'posts/delete', '文档删除', 1, 'pc', 47, 1482393958, 1482393958, 2, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (65, 'album/lists', '列表', 1, 'pc', 61, 1482405667, 1482405693, 1, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (66, 'album/status', '状态改变', 1, 'pc', 61, 1482406679, 1482406679, 3, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (67, 'album/update', '添加修改', 1, 'pc', 61, 1482406703, 1482406720, 2, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (68, 'album/delete', '删除', 1, 'pc', 61, 1482406743, 1482406743, 4, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (69, 'data/lists', '数据列表', 1, 'pc', 60, 1483632960, 1483632960, 1, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (70, 'data/update', '数据更新', 1, 'pc', 60, 1483632985, 1483633820, 2, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (71, 'data/delete', '数据删除', 1, 'pc', 60, 1483633007, 1483633007, 3, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (72, 'extend/index', '扩展配置', 1, 'pc', 0, 1490322063, 1490970848, 7, 1, 1, 0, 'flash');
INSERT INTO `t_auth_rule` VALUES (73, 'wechat/index', '微信', 1, 'pc', 72, 1490322137, 1490341959, 1, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (74, 'sms/index', '短信', 1, 'pc', 72, 1490322214, 1490341969, 2, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (75, 'email/index', '邮件', 1, 'pc', 72, 1490322274, 1490341981, 3, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (76, 'wechat/config', '公众号配置', 1, 'pc', 73, 1490344304, 1490344304, 1, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (77, 'sms/alidayu', '阿里大鱼配置', 1, 'pc', 74, 1490539191, 1490539191, 1, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (78, 'email/config', 'smtp配置', 1, 'pc', 75, 1490540808, 1490540847, 1, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (79, 'sms/templateUpdate', '短信模板更新', 1, 'pc', 74, 1490630102, 1490630102, 1, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (80, 'sms/lists', '短信发送记录', 1, 'pc', 74, 1490630138, 1490630138, 3, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (81, 'sms/sent', '发送短信', 1, 'pc', 74, 1490630162, 1490630162, 4, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (82, 'sms/templateStatus', '模板状态设置', 1, 'pc', 74, 1490632650, 1490632650, 5, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (83, 'sms/templateDelete', '模板删除', 1, 'pc', 74, 1490632676, 1490632676, 7, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (84, 'email/templateUpdate', '邮件模板更新', 1, 'pc', 75, 1490878695, 1490878745, 3, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (85, 'email/templateStatus', '邮件模板状态', 1, 'pc', 75, 1490878725, 1490878725, 4, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (86, 'email/templateDelete', '邮件模板删除', 1, 'pc', 75, 1490878795, 1490878795, 5, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (87, 'email/lists', '发送记录', 1, 'pc', 75, 1490878841, 1490878841, 6, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (89, 'email/sent', '发送测试', 1, 'pc', 75, 1490881287, 1490881287, 7, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (90, 'func/index', '功能开发', 1, 'pc', 0, 1490970914, 1490971545, 4, 1, 1, 0, 'code');
INSERT INTO `t_auth_rule` VALUES (91, 'p2p/index', '丰驰信贷', 1, 'pc', 90, 1490971119, 1492505095, 1, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (92, 'crowdfunding/index', '丰驰众筹', 1, 'pc', 90, 1490971232, 1492505679, 3, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (93, 'mall/index', '丰驰商城', 1, 'pc', 90, 1490971276, 1492505659, 2, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (94, 'auction/index', '拍卖商城', 1, 'pc', 90, 1490971332, 1492505561, 4, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (95, 'p2p/loan', '借款列表', 1, 'pc', 91, 1490975664, 1491029076, 1, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (96, 'p2p/loanCancel', '借款取消受理', 1, 'pc', 91, 1491029066, 1491359856, 2, 1, 0, 0, '');
INSERT INTO `t_auth_rule` VALUES (97, 'p2p/loanStep1', '贷款受理', 1, 'pc', 91, 1491029156, 1491029156, 3, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (98, 'p2p/loanStep2', '资料验收', 1, 'pc', 91, 1491029184, 1491029184, 4, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (99, 'p2p/loanStep3', '评估审批', 1, 'pc', 91, 1491029227, 1491029227, 5, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (100, 'p2p/loanStep4', '签订协议', 1, 'pc', 91, 1491029256, 1491294780, 6, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (101, 'p2p/loanStep5', '贷款筹集', 1, 'pc', 91, 1491029910, 1491294760, 7, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (102, 'p2p/loanStep6', '贷款发放', 1, 'pc', 91, 1491029929, 1491294745, 8, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (103, 'p2p/loanStep7', '贷后管理', 1, 'pc', 91, 1491029953, 1491294715, 9, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (104, 'p2p/product', '产品配置', 1, 'pc', 91, 1491195333, 1491195400, 11, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (105, 'p2p/product', '产品配置更新', 1, 'pc', 91, 1491195379, 1491195379, 12, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (106, 'p2p/productStatus', '产品配置状态', 1, 'pc', 91, 1491195435, 1491195435, 13, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (107, 'p2p/productDelete', '产品配置删除', 1, 'pc', 91, 1491195470, 1491195470, 14, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (108, 'p2p/finance', '项目列表', 1, 'pc', 91, 1491195558, 1491195558, 21, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (109, 'p2p/financeDelete', '项目取消', 1, 'pc', 91, 1491195594, 1491195594, 22, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (110, 'p2p/financeDetail', '项目详情', 1, 'pc', 91, 1491195632, 1491195632, 23, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (111, 'p2p/raise', '筹款数据', 1, 'pc', 91, 1491196948, 1491196948, 24, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (112, 'p2p/loanStep8', '贷款回收', 1, 'pc', 91, 1491294686, 1491294686, 10, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (113, 'p2p/order', '还款账单', 1, 'pc', 91, 1491401182, 1491401197, 31, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (114, 'p2p/lateFeeCreate', '生成滞纳金', 1, 'pc', 91, 1491401249, 1491401249, 32, 1, 0, 0, '');
INSERT INTO `t_auth_rule` VALUES (115, 'p2p/raiseShare', '投资分享收益结算', 1, 'pc', 91, 1491447654, 1491447670, 25, 1, 0, 0, '');
INSERT INTO `t_auth_rule` VALUES (116, 'api/index', 'api接口', 0, 'pc', 72, 1492053361, 1492053463, 4, 1, 1, 0, '');
INSERT INTO `t_auth_rule` VALUES (117, 'score/index', '积分商城', 1, 'pc', 90, 1492505642, 1492505642, 5, 1, 1, 0, '');
COMMIT;

-- ----------------------------
-- Table structure for t_auth_user_group
-- ----------------------------
DROP TABLE IF EXISTS `t_auth_user_group`;
CREATE TABLE `t_auth_user_group` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of t_auth_user_group
-- ----------------------------
BEGIN;
INSERT INTO `t_auth_user_group` VALUES (11, 2, 6);
COMMIT;

-- ----------------------------
-- Table structure for t_category
-- ----------------------------
DROP TABLE IF EXISTS `t_category`;
CREATE TABLE `t_category` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `pid` bigint(20) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `type` varchar(12) NOT NULL,
  `template` bigint(20) NOT NULL DEFAULT '0',
  `template_sub` bigint(20) NOT NULL DEFAULT '0',
  `seo` text NOT NULL,
  `info` text NOT NULL,
  `content` text NOT NULL,
  `sort` int(11) NOT NULL DEFAULT '0',
  `display` tinyint(1) NOT NULL DEFAULT '1',
  `config` text NOT NULL,
  `list_count` int(11) NOT NULL DEFAULT '0',
  `jump` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_category
-- ----------------------------
BEGIN;
INSERT INTO `t_category` VALUES (2, 8, 'index', '首页', '/', 0, 1, 1482293475, 1490947420, 'index', 2, 0, '{\"title\":\"\",\"keywords\":\"\",\"description\":\"\"}', '', '', 0, 1, '', 0, 0);
INSERT INTO `t_category` VALUES (3, 8, 'about', '聚仁概况', '/about/', 0, 1, 1482293789, 1540954932, 'page', 3, 0, '{\"title\":\"\",\"keywords\":\"\",\"description\":\"\"}', '{\"title_sub\":\"\",\"cover\":\"\\/static\\/kindeditor\\/php\\/..\\/..\\/..\\/uploads\\/image\\/20181031\\/20181031110210_89808.jpg\"}', '', 2, 1, '', 0, 1);
INSERT INTO `t_category` VALUES (4, 8, 'investment', '招商加盟', '/investment/', 0, 1, 1482293870, 1540949082, 'page', 3, 0, '{\"title\":\"\",\"keywords\":\"\",\"description\":\"\"}', '', '', 6, 1, '', 0, 0);
INSERT INTO `t_category` VALUES (5, 8, 'news', '新闻资讯', '/news/', 0, 1, 1482294137, 1540950526, 'page', 3, 0, '{\"title\":\"\",\"keywords\":\"\",\"description\":\"\"}', '', '', 5, 1, '', 0, 1);
INSERT INTO `t_category` VALUES (6, 8, 'industry', '行业新闻', '/news/industry/', 5, 1, 1482294220, 1540948700, 'list', 4, 5, '{\"title\":\"1\",\"keywords\":\"2\",\"description\":\"3\"}', '{\"banner\":\"\\/uploads\\/image\\/20161219\\/20161219235720_61331.jpg\",\"desc\":\"12\"}', '123', 3, 1, '', 10, 0);
INSERT INTO `t_category` VALUES (7, 8, 'enterprise', '企业新闻', '/news/enterprise/', 5, 1, 1482294350, 1540967835, 'list', 4, 5, '{\"title\":\"\",\"keywords\":\"\",\"description\":\"\"}', '', '', 2, 1, 'banner|横幅|img\r\ndes|简述|text', 1, 0);
INSERT INTO `t_category` VALUES (12, 9, 'index', '首页', '/', 0, 1, 1540914915, 1540915914, 'index', 0, 0, '{\"title\":\"\",\"keywords\":\"\",\"description\":\"\"}', '', '', 0, 1, '', 0, 0);
INSERT INTO `t_category` VALUES (13, 9, 'about', '关于', '/about/', 0, 1, 1540916008, 1540916008, 'page', 0, 0, '', '', '', 0, 1, '', 0, 0);
INSERT INTO `t_category` VALUES (14, 8, 'service', '服务项目', '/service/', 0, 1, 1540948576, 1540950503, 'page', 3, 0, '{\"title\":\"\",\"keywords\":\"\",\"description\":\"\"}', '', '', 3, 1, '', 0, 1);
INSERT INTO `t_category` VALUES (15, 8, 'cooperation', '合作优势', '/cooperation/', 0, 1, 1540948618, 1540950516, 'page', 3, 0, '{\"title\":\"\",\"keywords\":\"\",\"description\":\"\"}', '', '', 4, 1, '', 0, 1);
INSERT INTO `t_category` VALUES (16, 8, 'medi', '媒体评论', '/news/medi/', 5, 1, 1540948740, 1540948842, 'list', 4, 5, '{\"title\":\"\",\"keywords\":\"\",\"description\":\"\"}', '', '', 3, 1, '', 10, 0);
INSERT INTO `t_category` VALUES (17, 8, 'customer', '客服中心', '/customer/', 0, 1, 1540949005, 1540950537, 'page', 3, 0, '{\"title\":\"\",\"keywords\":\"\",\"description\":\"\"}', '', '', 7, 1, '', 0, 1);
INSERT INTO `t_category` VALUES (18, 8, 'company', '公司介绍', '/about/company/', 3, 1, 1540949499, 1540956000, 'page', 3, 0, '{\"title\":\"\",\"keywords\":\"\",\"description\":\"\"}', '{\"title_sub\":\"\",\"cover\":\"\"}', '内容详情', 1, 1, '', 0, 0);
INSERT INTO `t_category` VALUES (19, 8, 'honor', '资质荣誉', '/about/honor/', 3, 1, 1540949626, 1540949637, 'page', 3, 0, '{\"title\":\"\",\"keywords\":\"\",\"description\":\"\"}', '', '', 2, 1, '', 0, 0);
INSERT INTO `t_category` VALUES (20, 8, 'framework', '组织架构', '/about/framework/', 3, 1, 1540949666, 1540949675, 'page', 3, 0, '{\"title\":\"\",\"keywords\":\"\",\"description\":\"\"}', '', '', 3, 1, '', 0, 0);
INSERT INTO `t_category` VALUES (21, 8, 'culture', '企业文化', '/about/culture/', 3, 1, 1540949695, 1540949791, 'page', 3, 0, '{\"title\":\"\",\"keywords\":\"\",\"description\":\"\"}', '', '', 4, 1, '', 0, 0);
INSERT INTO `t_category` VALUES (22, 8, 'personnel', '人才战略', '/personnel/', 3, 1, 1540949751, 1540949799, 'page', 3, 0, '{\"title\":\"\",\"keywords\":\"\",\"description\":\"\"}', '', '', 5, 1, '', 0, 0);
INSERT INTO `t_category` VALUES (23, 8, 'team', '团队风采', '/about/team/', 3, 1, 1540949783, 1540949807, 'page', 3, 0, '{\"title\":\"\",\"keywords\":\"\",\"description\":\"\"}', '', '', 6, 1, '', 0, 0);
INSERT INTO `t_category` VALUES (24, 8, 'ceshi', '发现焦点', '/service/ceshi/', 14, 1, 1540949855, 1541135304, 'page', 13, 0, '{\"title\":\"\",\"keywords\":\"\",\"description\":\"\"}', '{\"title_sub\":\"\",\"cover\":\"\\/static\\/kindeditor\\/php\\/..\\/..\\/..\\/uploads\\/image\\/20181031\\/20181031155521_96165.jpeg\"}', '发现焦点——每个人都是焦点', 1, 1, '', 0, 0);
INSERT INTO `t_category` VALUES (25, 8, 'case', '合作案例', '/service/case/', 14, 1, 1540949919, 1540971974, 'list', 13, 5, '{\"title\":\"\",\"keywords\":\"\",\"description\":\"\"}', '{\"title_sub\":\"\",\"cover\":\"\"}', '', 9, 1, '', 0, 0);
INSERT INTO `t_category` VALUES (26, 8, 'concept', '合作理念', '/cooperation/concept/', 15, 1, 1540949990, 1540949999, 'page', 3, 0, '{\"title\":\"\",\"keywords\":\"\",\"description\":\"\"}', '', '', 1, 1, '', 0, 0);
INSERT INTO `t_category` VALUES (27, 8, 'partner', '合作伙伴', '/cooperation/partner/', 15, 1, 1540950051, 1540950065, 'page', 3, 0, '{\"title\":\"\",\"keywords\":\"\",\"description\":\"\"}', '', '', 3, 1, '', 0, 0);
INSERT INTO `t_category` VALUES (28, 8, 'process', '合作流程', '/cooperation/process/', 15, 1, 1540950101, 1540950112, 'page', 3, 0, '{\"title\":\"\",\"keywords\":\"\",\"description\":\"\"}', '', '', 4, 1, '', 0, 0);
INSERT INTO `t_category` VALUES (29, 8, 'advantage', '合作优势', '/cooperation/advantage/', 15, 1, 1540950149, 1540950159, 'page', 3, 0, '{\"title\":\"\",\"keywords\":\"\",\"description\":\"\"}', '', '', 2, 1, '', 0, 0);
INSERT INTO `t_category` VALUES (30, 8, 'features', '服务特色', '/customer/features/', 17, 1, 1540950414, 1540950424, 'page', 3, 0, '{\"title\":\"\",\"keywords\":\"\",\"description\":\"\"}', '', '', 1, 1, '', 0, 0);
INSERT INTO `t_category` VALUES (31, 8, 'contact', '联系我们', '/customer/contact/', 17, 1, 1540950446, 1540950454, 'page', 3, 0, '{\"title\":\"\",\"keywords\":\"\",\"description\":\"\"}', '', '', 4, 1, '', 0, 0);
COMMIT;

-- ----------------------------
-- Table structure for t_email
-- ----------------------------
DROP TABLE IF EXISTS `t_email`;
CREATE TABLE `t_email` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `template_id` bigint(20) NOT NULL,
  `template_name` varchar(24) NOT NULL,
  `params` varchar(1000) NOT NULL,
  `code` varchar(12) NOT NULL DEFAULT '',
  `deadline` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL,
  `email` varchar(24) NOT NULL,
  `uid` bigint(20) NOT NULL DEFAULT '0' COMMENT '操作人',
  `subject` varchar(255) NOT NULL DEFAULT '' COMMENT '邮件标题',
  `extend` text COMMENT '备注信息',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_email
-- ----------------------------
BEGIN;
INSERT INTO `t_email` VALUES (1, 1, 'emial_check_code', '{\"code\":\"1234\",\"ps\":\"ceshi\"}', '1234', 0, 0, 1490883714, 1490883714, '332553882@qq.com', 2, '邮件验证码', '');
COMMIT;

-- ----------------------------
-- Table structure for t_email_template
-- ----------------------------
DROP TABLE IF EXISTS `t_email_template`;
CREATE TABLE `t_email_template` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `name` varchar(64) NOT NULL,
  `type` varchar(24) NOT NULL DEFAULT '1' COMMENT '1:text',
  `content` varchar(1000) NOT NULL,
  `extra` text,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL,
  `uid` bigint(20) NOT NULL DEFAULT '0',
  `is_code` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否验证码 0:不是1:是',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_email_template
-- ----------------------------
BEGIN;
INSERT INTO `t_email_template` VALUES (1, '邮件验证码', 'emial_check_code', '1', '您好，您收到了验证码为{$code},请妥善保管，5分钟有效\r\n{$ps}', 'code:验证码|ps:备注', 1, 1490879445, 1490881438, 2, 1);
COMMIT;

-- ----------------------------
-- Table structure for t_fanslink
-- ----------------------------
DROP TABLE IF EXISTS `t_fanslink`;
CREATE TABLE `t_fanslink` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `cover` varchar(255) NOT NULL,
  `status` varchar(12) NOT NULL,
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_fanslink
-- ----------------------------
BEGIN;
INSERT INTO `t_fanslink` VALUES (1, '友情链接1', 'http://www.baidu.com', 'ceshi', '1', '2017-01-05 23:16:45', '2017-01-05 23:16:45');
COMMIT;

-- ----------------------------
-- Table structure for t_member
-- ----------------------------
DROP TABLE IF EXISTS `t_member`;
CREATE TABLE `t_member` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `phone` varchar(32) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL,
  `sex` tinyint(1) NOT NULL DEFAULT '0',
  `info` text,
  `avatar` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `wechat` text,
  `openid` varchar(255) NOT NULL DEFAULT '',
  `unionid` varchar(255) NOT NULL DEFAULT '',
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1:个人用户2:企业用户',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_member
-- ----------------------------
BEGIN;
INSERT INTO `t_member` VALUES (1, 'L@C ‍', '18676669410', '', 'e10adc3949ba59abbe56e057f20f883e', 1, NULL, 'http://wx.qlogo.cn/mmopen/Wr6mtQnJMVFq7R3AJldfPTia7IicEU45ZsShc4FMCNnIQ3WHenH06OUqhAgkuxHJSJlaZSIsFZetUwE5lMQaMjmHjE2310Bt0f/0', 1490465088, 1491445052, 0, '{\"openid\":\"oWw5tszKjCj_6-Aq8UWhXxpa3MYU\",\"nickname\":\"L@C \\u200d\",\"sex\":1,\"language\":\"zh_CN\",\"city\":\"\\u6df1\\u5733\",\"province\":\"\\u5e7f\\u4e1c\",\"country\":\"\\u4e2d\\u56fd\",\"headimgurl\":\"http:\\/\\/wx.qlogo.cn\\/mmopen\\/Wr6mtQnJMVFq7R3AJldfPTia7IicEU45ZsShc4FMCNnIQ3WHenH06OUqhAgkuxHJSJlaZSIsFZetUwE5lMQaMjmHjE2310Bt0f\\/0\",\"privilege\":[],\"unionid\":\"ouoDUsi7x4JmrW9LdCsyiUIHxBP8\"}', 'oWw5tszKjCj_6-Aq8UWhXxpa3MYU', 'ouoDUsi7x4JmrW9LdCsyiUIHxBP8', 2);
INSERT INTO `t_member` VALUES (2, 'L@C ‍', '1867666941', '', 'e10adc3949ba59abbe56e057f20f883e', 1, NULL, 'http://wx.qlogo.cn/mmopen/Wr6mtQnJMVFq7R3AJldfPTia7IicEU45ZsShc4FMCNnIQ3WHenH06OUqhAgkuxHJSJlaZSIsFZetUwE5lMQaMjmHjE2310Bt0f/0', 1490527204, 1490715805, 0, '', '', '', 1);
INSERT INTO `t_member` VALUES (3, '张三', '1867666942', '', 'e10adc3949ba59abbe56e057f20f883e', 1, NULL, '', 1491293765, 1491293765, 0, NULL, '', '', 1);
COMMIT;

-- ----------------------------
-- Table structure for t_member_auth
-- ----------------------------
DROP TABLE IF EXISTS `t_member_auth`;
CREATE TABLE `t_member_auth` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `member_id` bigint(20) NOT NULL DEFAULT '0',
  `token` varchar(255) NOT NULL,
  `type` varchar(12) NOT NULL,
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `login_ip` varchar(32) NOT NULL DEFAULT '0',
  `login_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_member_auth
-- ----------------------------
BEGIN;
INSERT INTO `t_member_auth` VALUES (1, 1, '239048b17b12f8d5a256fc2500501233', 'wx', 1490465088, 1491445052, 0, '127.0.0.1', 1491445052);
INSERT INTO `t_member_auth` VALUES (3, 1, '021eadedb1036aa572f8f07bd56a6761', 'pc', 1490527076, 1492141081, 0, '127.0.0.1', 1492141081);
INSERT INTO `t_member_auth` VALUES (4, 3, '7cbadfa199c0fb84aa00f9c88db0160b', 'wx', 1490527204, 1490715133, 0, '0.0.0.0', 1490715134);
INSERT INTO `t_member_auth` VALUES (5, 3, 'ed9757cec4290d0a65e2b2051b5649f0', 'wx', 1490527233, 1490527643, 0, '0.0.0.0', 1490527644);
INSERT INTO `t_member_auth` VALUES (6, 4, 'a5bfee5a97c8620335eb7cfdb8366b3f', 'pc', 1490878179, 1490878179, 0, '127.0.0.1', 1490878179);
INSERT INTO `t_member_auth` VALUES (7, 2, '553594fa6ed310f237722cb7d8cb5698', 'pc', 1491205504, 1491448900, 0, '127.0.0.1', 1491448900);
INSERT INTO `t_member_auth` VALUES (8, 3, 'e3a8e85e99bbf8963fa56d71a47a3469', 'pc', 1491293765, 1491465516, 0, '127.0.0.1', 1491465516);
COMMIT;

-- ----------------------------
-- Table structure for t_member_info
-- ----------------------------
DROP TABLE IF EXISTS `t_member_info`;
CREATE TABLE `t_member_info` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `member_id` bigint(20) NOT NULL DEFAULT '0',
  `realname` varchar(255) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `identify_no` varchar(32) NOT NULL DEFAULT '' COMMENT '身份证号',
  `bank_name` varchar(64) NOT NULL DEFAULT '' COMMENT '开户银行',
  `bank_branch` varchar(255) NOT NULL DEFAULT '' COMMENT '开户银行支行名称',
  `bank_card` varchar(255) NOT NULL DEFAULT '' COMMENT '银行卡号',
  `level_value` tinyint(4) NOT NULL DEFAULT '0' COMMENT '用户评级',
  `assets` varchar(255) NOT NULL DEFAULT '' COMMENT '资产信息',
  `company` varchar(255) NOT NULL DEFAULT '' COMMENT '公司名称',
  `contract_name` varchar(64) NOT NULL DEFAULT '' COMMENT '联系人姓名',
  `contract_phone` varchar(24) NOT NULL DEFAULT '' COMMENT '联系人电话',
  `income_year` bigint(20) NOT NULL DEFAULT '0' COMMENT '年收入',
  `address` varchar(1000) NOT NULL DEFAULT '' COMMENT '家庭住址',
  `car_num` varchar(24) NOT NULL DEFAULT '' COMMENT '车牌号码',
  `status` tinyint(1) NOT NULL,
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_member_info
-- ----------------------------
BEGIN;
INSERT INTO `t_member_info` VALUES (1, 1, '鲁聪', '4208221989', '招商银行', '东门支行', '1234567887654321', 2, '无房无车无贷款', '我们家', '无', '1000100', 10000000, '广东深圳', '', 0, 1491136275, 1491465651);
INSERT INTO `t_member_info` VALUES (2, 2, '鲁聪', '42082219890512', '建行', '城建支行', '12345678', 0, '无', '橙子网络', '鲁', '18676669410', 12000000, '广东深圳南山', '', 0, 1491205660, 1491205660);
COMMIT;

-- ----------------------------
-- Table structure for t_member_value
-- ----------------------------
DROP TABLE IF EXISTS `t_member_value`;
CREATE TABLE `t_member_value` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `member_id` bigint(20) NOT NULL DEFAULT '0',
  `money` bigint(20) NOT NULL DEFAULT '0' COMMENT '钱',
  `score` int(20) NOT NULL COMMENT '积分',
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_member_value
-- ----------------------------
BEGIN;
INSERT INTO `t_member_value` VALUES (11, 2, 1006000, 0, 1, 1491209511, 1491469666);
INSERT INTO `t_member_value` VALUES (12, 3, 1010000, 0, 1, 1491293982, 1491469666);
INSERT INTO `t_member_value` VALUES (13, 1, 245275, 0, 1, 1491359661, 1491468852);
COMMIT;

-- ----------------------------
-- Table structure for t_member_value_log
-- ----------------------------
DROP TABLE IF EXISTS `t_member_value_log`;
CREATE TABLE `t_member_value_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `member_id` bigint(20) NOT NULL,
  `type` varchar(24) NOT NULL COMMENT '类型',
  `log_type` varchar(12) NOT NULL DEFAULT '' COMMENT '记录类型',
  `num` bigint(20) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_member_value_log
-- ----------------------------
BEGIN;
INSERT INTO `t_member_value_log` VALUES (41, 3, 'in', 'money', 1000000, 1, 1491465541, 1491465541);
INSERT INTO `t_member_value_log` VALUES (42, 2, 'in', 'money', 1000000, 1, 1491465624, 1491465624);
INSERT INTO `t_member_value_log` VALUES (43, 3, 'raise', 'money', -500000, 1, 1491466405, 1491466405);
INSERT INTO `t_member_value_log` VALUES (44, 2, 'raise', 'money', -300000, 1, 1491466424, 1491466424);
INSERT INTO `t_member_value_log` VALUES (45, 1, 'loan', 'money', 800000, 1, 1491466474, 1491466474);
INSERT INTO `t_member_value_log` VALUES (46, 1, 'loan', 'money', 800000, 1, 1491466599, 1491466599);
INSERT INTO `t_member_value_log` VALUES (47, 1, 'pay_p2p', 'money', -270945, 1, 1491467009, 1491467009);
INSERT INTO `t_member_value_log` VALUES (48, 1, 'pay_p2p', 'money', -270945, 1, 1491467013, 1491467013);
INSERT INTO `t_member_value_log` VALUES (49, 1, 'pay_p2p', 'money', -270945, 1, 1491467017, 1491467017);
INSERT INTO `t_member_value_log` VALUES (50, 1, 'pay_p2p_auto', 'money', -270945, 1, 1491468851, 1491468851);
INSERT INTO `t_member_value_log` VALUES (51, 1, 'pay_p2p_auto', 'money', -270945, 1, 1491468852, 1491468852);
INSERT INTO `t_member_value_log` VALUES (52, 3, 'raise_share', 'money', 510000, 1, 1491469666, 1491469666);
INSERT INTO `t_member_value_log` VALUES (53, 2, 'raise_share', 'money', 306000, 1, 1491469666, 1491469666);
COMMIT;

-- ----------------------------
-- Table structure for t_models
-- ----------------------------
DROP TABLE IF EXISTS `t_models`;
CREATE TABLE `t_models` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `name` varchar(32) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `project_id` int(11) NOT NULL COMMENT '关联项目',
  `fields` text NOT NULL COMMENT '字段配置',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `status_value` varchar(1000) NOT NULL COMMENT '状态值',
  `member_record` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否记录用户',
  `list_count` int(11) NOT NULL DEFAULT '0' COMMENT '列表数',
  `list_display` varchar(1000) NOT NULL COMMENT '列表显示字段',
  `status_default` varchar(255) NOT NULL COMMENT '状态默认字段',
  `sort_field` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_models
-- ----------------------------
BEGIN;
INSERT INTO `t_models` VALUES (1, '友情链接', 'fanslink', 1, 1483627377, 1540918124, 8, 'name|名称|char|请输入名称\r\nurl|链接|char|请输入链接http/https开头)\r\ncover|图片|img|亲输入图片', 1, '0:禁用|1:正常', 0, 0, 'name,url,cover', '1', 'create_time desc');
COMMIT;

-- ----------------------------
-- Table structure for t_p2p_finance
-- ----------------------------
DROP TABLE IF EXISTS `t_p2p_finance`;
CREATE TABLE `t_p2p_finance` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL DEFAULT '' COMMENT '备案编号',
  `num` int(20) NOT NULL COMMENT '金额',
  `time_limit` int(11) NOT NULL DEFAULT '1' COMMENT '借款期限',
  `raise_start_time` int(11) NOT NULL,
  `raise_end_time` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL DEFAULT '0',
  `member_id` bigint(20) NOT NULL DEFAULT '0',
  `product_id` bigint(20) NOT NULL DEFAULT '0',
  `interest_rate` float(11,4) NOT NULL COMMENT '年利率',
  `repayment` tinyint(4) NOT NULL DEFAULT '1' COMMENT '还款方式',
  `uid` bigint(20) NOT NULL DEFAULT '0',
  `min_num` bigint(20) NOT NULL DEFAULT '0' COMMENT '最低起投金额',
  `income_rate` float(11,4) NOT NULL DEFAULT '0.0000' COMMENT '收益利率',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_p2p_finance
-- ----------------------------
BEGIN;
INSERT INTO `t_p2p_finance` VALUES (3, '20170406123456789', 800000, 3, 1491321600, 1491839999, 1, 1491465777, 1491466424, 1, 1, 9.6000, 1, 2, 20000, 8.0000);
COMMIT;

-- ----------------------------
-- Table structure for t_p2p_loan
-- ----------------------------
DROP TABLE IF EXISTS `t_p2p_loan`;
CREATE TABLE `t_p2p_loan` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `phone` varchar(16) NOT NULL,
  `sex` tinyint(1) NOT NULL DEFAULT '0',
  `city` varchar(255) NOT NULL,
  `occupation` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1:上班族2:个体经商99:其他',
  `assets` text COMMENT '资产',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL,
  `step` int(11) NOT NULL DEFAULT '0',
  `member_id` bigint(20) NOT NULL DEFAULT '0',
  `uid` bigint(20) NOT NULL,
  `finance_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '关联金融项目id',
  `start_date` int(11) NOT NULL DEFAULT '0' COMMENT '开始日期',
  `end_date` int(11) NOT NULL DEFAULT '0' COMMENT '结束日期，还贷日期',
  `value_pre` int(20) NOT NULL DEFAULT '0' COMMENT '预计金额',
  `value_real` int(20) NOT NULL DEFAULT '0' COMMENT '实际金额',
  `use_info` varchar(1000) NOT NULL COMMENT '借款用途',
  `time_limit` int(11) DEFAULT '1' COMMENT '借款期限',
  `repayment` tinyint(4) NOT NULL DEFAULT '1' COMMENT '还款方式',
  `guarantee` varchar(1000) DEFAULT '' COMMENT '抵押担保信息',
  `examine` varchar(1000) DEFAULT '' COMMENT '审核信息',
  `raise_limit` varchar(1000) NOT NULL COMMENT '募集期限',
  `info_complete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '资料是否提交完毕',
  `level_value` tinyint(4) NOT NULL DEFAULT '0' COMMENT '用户评级',
  `info_content` varchar(1000) NOT NULL DEFAULT '' COMMENT '资料类型',
  `check_complete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '审核是否完成',
  `loan_date` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_p2p_loan
-- ----------------------------
BEGIN;
INSERT INTO `t_p2p_loan` VALUES (4, '鲁聪', '18676669410', 1, '洛杉矶', 2, '1,2', 1, 1491465113, 1491467017, 8, 1, 2, 3, 0, 0, 800000, 0, '资金周转', 3, 1, '2,3', '1,2,3,4,5,6', '', 1, 2, '1,2,3,4,5', 1, 1491408000);
COMMIT;

-- ----------------------------
-- Table structure for t_p2p_loan_log
-- ----------------------------
DROP TABLE IF EXISTS `t_p2p_loan_log`;
CREATE TABLE `t_p2p_loan_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `loan_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '关联借款id',
  `uid` bigint(20) NOT NULL DEFAULT '0' COMMENT '操作人',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '操作人名称',
  `step` tinyint(4) NOT NULL DEFAULT '0' COMMENT '步骤',
  `info` text,
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `pics` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_p2p_loan_log
-- ----------------------------
BEGIN;
INSERT INTO `t_p2p_loan_log` VALUES (40, 4, 2, '', 0, '确认受理', 1491465596, 1491465596, 1, '');
INSERT INTO `t_p2p_loan_log` VALUES (41, 4, 2, '', 1, '资料确认接收完毕', 1491465651, 1491465651, 1, '');
INSERT INTO `t_p2p_loan_log` VALUES (42, 4, 2, '', 2, '确认通过审核', 1491465688, 1491465688, 1, '');
INSERT INTO `t_p2p_loan_log` VALUES (43, 4, 2, '', 3, '确认生成协议', 1491465777, 1491465777, 1, '');
INSERT INTO `t_p2p_loan_log` VALUES (44, 4, 2, '', 5, '确认发放贷款', 1491466474, 1491466474, 1, '');
INSERT INTO `t_p2p_loan_log` VALUES (45, 4, 2, '', 5, '确认发放', 1491466599, 1491466599, 1, '');
COMMIT;

-- ----------------------------
-- Table structure for t_p2p_order
-- ----------------------------
DROP TABLE IF EXISTS `t_p2p_order`;
CREATE TABLE `t_p2p_order` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `member_id` bigint(20) NOT NULL DEFAULT '0',
  `finance_id` bigint(20) NOT NULL DEFAULT '0',
  `loan_id` bigint(20) NOT NULL DEFAULT '0',
  `bill_date` int(11) NOT NULL COMMENT '账单日',
  `pay_deadline` int(11) NOT NULL COMMENT '还款截止日',
  `sum` bigint(20) NOT NULL COMMENT '金额',
  `status` tinyint(20) NOT NULL DEFAULT '0' COMMENT '状态',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL,
  `principal` bigint(20) NOT NULL COMMENT '本金',
  `interest` bigint(20) NOT NULL COMMENT '利息',
  `late_fee` bigint(20) NOT NULL DEFAULT '0' COMMENT '滞纳金',
  `late_days` int(11) NOT NULL COMMENT '滞纳天数',
  `pay_time` int(11) NOT NULL DEFAULT '0' COMMENT '还款时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_p2p_order
-- ----------------------------
BEGIN;
INSERT INTO `t_p2p_order` VALUES (74, 1, 3, 4, 1494000000, 1494086399, 270945, 1, 1491466599, 1491468881, 264545, 6400, 542, 1, 0);
INSERT INTO `t_p2p_order` VALUES (75, 1, 3, 4, 1496678400, 1496764799, 270945, 1, 1491466599, 1491467013, 266661, 4284, 0, 0, 0);
INSERT INTO `t_p2p_order` VALUES (76, 1, 3, 4, 1499270400, 1499356799, 270945, 1, 1491466599, 1491467017, 268795, 2150, 0, 0, 0);
COMMIT;

-- ----------------------------
-- Table structure for t_p2p_product
-- ----------------------------
DROP TABLE IF EXISTS `t_p2p_product`;
CREATE TABLE `t_p2p_product` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `info` text COMMENT '详细信息',
  `status` int(11) NOT NULL DEFAULT '1',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL,
  `yield_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '收益描述',
  `uid` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_p2p_product
-- ----------------------------
BEGIN;
INSERT INTO `t_p2p_product` VALUES (1, '橙金宝', NULL, 1, 0, 0, '', 2);
INSERT INTO `t_p2p_product` VALUES (2, '橙银宝', NULL, 1, 0, 0, '', 2);
COMMIT;

-- ----------------------------
-- Table structure for t_p2p_raise
-- ----------------------------
DROP TABLE IF EXISTS `t_p2p_raise`;
CREATE TABLE `t_p2p_raise` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `member_id` bigint(20) NOT NULL,
  `finance_id` bigint(20) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `num` bigint(20) NOT NULL DEFAULT '0',
  `interest` bigint(20) NOT NULL DEFAULT '0',
  `sum` bigint(20) NOT NULL DEFAULT '0',
  `recycle_date` int(11) NOT NULL DEFAULT '0' COMMENT '回收日期',
  `recycle_time` int(11) NOT NULL DEFAULT '0' COMMENT '回收时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_p2p_raise
-- ----------------------------
BEGIN;
INSERT INTO `t_p2p_raise` VALUES (12, 3, 3, 1, 1491466405, 1491469666, 500000, 10000, 510000, 1499270400, 1499529600);
INSERT INTO `t_p2p_raise` VALUES (13, 2, 3, 1, 1491466424, 1491469666, 300000, 6000, 306000, 1499270400, 1499529600);
COMMIT;

-- ----------------------------
-- Table structure for t_posts
-- ----------------------------
DROP TABLE IF EXISTS `t_posts`;
CREATE TABLE `t_posts` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `content` text NOT NULL,
  `info` text NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `seo` text NOT NULL,
  `pid` bigint(20) NOT NULL DEFAULT '0',
  `views` int(11) NOT NULL DEFAULT '0',
  `published_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sort` int(11) NOT NULL DEFAULT '0',
  `cover` varchar(255) NOT NULL,
  `config` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_posts
-- ----------------------------
BEGIN;
INSERT INTO `t_posts` VALUES (4, 7, 'da-ye', '大爷', '2', '3', '{\"banner\":\"\\/uploads\\/image\\/20161219\\/20161219235720_61331.jpg\",\"des\":\"5\"}', 1, 1482389869, 1482680248, '/work/program/da-ye.html', '{\"title\":\"6\",\"keywords\":\"7\",\"description\":\"8\"}', 0, 0, '2016-12-22 14:57:50', 9, '', '');
INSERT INTO `t_posts` VALUES (6, 7, '分页测试', '分页测试', '分页', '哈啊啊啊啊啊啊啊', '{\"banner\":\"\",\"des\":\"\"}', 1, 1482680850, 1540967469, '/news/enterprise/分页测试.html', '{\"title\":\"\",\"keywords\":\"\",\"description\":\"\"}', 0, 0, '2016-12-25 23:47:30', 0, '/static/kindeditor/php/../../../uploads/image/20181031/20181031143107_65753.jpg', '');
INSERT INTO `t_posts` VALUES (7, 7, 'xin-wen-biao-ti-1', '新闻标题1', '新闻描述', '新闻详细内容', '{\"banner\":\"\",\"des\":\"\"}', 1, 1540966310, 1540966310, '/news/enterprise/xin-wen-biao-ti-1.html', '{\"title\":\"\",\"keywords\":\"\",\"description\":\"\"}', 0, 0, '2018-10-31 14:11:50', 0, '', '');
INSERT INTO `t_posts` VALUES (8, 25, 'zui-xin-an-li-1', '最新案例1', '最新案例1描述', '', '', 1, 1540966629, 1540966775, '/service/case/zui-xin-an-li-1.html', '{\"title\":\"\",\"keywords\":\"\",\"description\":\"\"}', 0, 0, '2018-10-31 14:17:09', 0, '/static/kindeditor/php/../../../uploads/image/20181031/20181031141932_85151.jpg', '');
INSERT INTO `t_posts` VALUES (9, 25, 'an-li-2', '案例2', '.。。。。。。。。。。。', '', '', 1, 1540971319, 1540971319, '/service/case/an-li-2.html', '{\"title\":\"\",\"keywords\":\"\",\"description\":\"\"}', 0, 0, '2018-10-31 15:35:19', 0, '/static/kindeditor/php/../../../uploads/image/20181031/20181031153517_18444.jpg', '');
COMMIT;

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
  `domain` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `config` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `info` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `theme_id` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of t_project
-- ----------------------------
BEGIN;
INSERT INTO `t_project` VALUES (8, '聚仁文化传媒', 'default', 1, 1482136104, 1540963369, 2, 'localhost', '/static/kindeditor/php/../../../uploads/image/20181031/20181031131311_58196.png', 'name|公司网站全称|char|请填写全称\r\nqrcode|二维码|img\r\ntel|客服电话|char\r\ncopyright|版权|char\r\nbeian|备案|char\r\nintro|首页公司介绍|text\r\nadvantage|首页合作优势图片|img\r\n\r\n', '{\"name\":\"\\u805a\\u4ec1(\\u6df1\\u5733)\\u6587\\u5316\\u4f20\\u5a92\\u6709\\u9650\\u516c\\u53f8\",\"qrcode\":\"\\/static\\/kindeditor\\/php\\/..\\/..\\/..\\/uploads\\/image\\/20181031\\/20181031131400_74002.jpg\",\"tel\":\"400-836-5151\",\"copyright\":\"Copyright@ \\u7248\\u6743\\u6240\\u6709\\uff1a\\u805a\\u4ec1(\\u6df1\\u5733)\\u6587\\u5316\\u4f20\\u5a92\\u6709\\u9650\\u516c\\u53f8\",\"beian\":\"\\u7ca4ICP\\u590718093440\\u53f7-1\",\"intro\":\"\\u4e00\\u5bb6\\u7531\\u5a92\\u4f53\\u8d44\\u6df1\\u4eba\\u58eb\\u521b\\u7acb\\u7684\\u591a\\u5a92\\u4f53\\u8d44\\u6e90\\u6574\\u5408\\u8fd0\\u8425\\u5546\\uff0c\\u516c\\u53f8\\u6210\\u7acb\\u4e8e2009\\u5e74\\uff0c\\u5728\\u8fd99\\u5e74\\u7684\\u5e02\\u573a\\u53d1\\u5c55\\u4e2d\\uff0c\\u516c\\u53f8\\u4ece\\u6700\\u521d\\u7684\\u5e7f\\u544a\\u670d\\u52a1\\u6162\\u6162\\u5ef6\\u4f38\\u5230\\u5e02\\u573a\\u5a92\\u4f53\\u6574\\u5408\\uff0c2011\\u5e74\\u516c\\u53f8\\u6b63\\u5f0f\\u6d89\\u53ca\\u79fb\\u52a8\\u4e92\\u8054\\u7f51\\u5a92\\u4f53\\uff0c\\u5e76\\u6210\\u529f\\u83b7\\u5f97\\u884c\\u4e1a\\u4e13\\u4e1a\\u4eba\\u58eb\\u98ce\\u6295\\uff0c\\u6210\\u4e3a\\u4e00\\u5bb6\\u4ee5\\u591a\\u5a92\\u4f53\\u6570\\u636e\\u5e93\\u53ca\\u79fb\\u52a8\\u4e92\\u8054\\u7f51\\u4e3a\\u6838\\u5fc3\\u7684\\u8d44\\u6e90\\u3001\\u6280\\u672f\\u3001\\u670d\\u52a1\\u4e00\\u4f53\\u5316\\u4f01\\u4e1a\\u3002\\u516c\\u53f8\\u96c6\\u6210\\u8f6f\\u4ef6\\u6280\\u672f\\u5f00\\u53d1\\u3001\\u786c\\u4ef6\\u914d\\u5957\\u7814\\u53d1\\u3001\\u8d44\\u6e90\\u7ba1\\u7406\\u6574\\u5408\\u3001\\u5e02\\u573a\\u8425\\u9500\\u670d\\u52a1\\u56db\\u5927\\u53d1\\u5c55\\u6a21\\u5f0f\\uff0c\\u8fc5\\u901f\\u5728\\u6fc0\\u70c8\\u7684\\u5e02\\u573a\\u7ade\\u4e89\\u4e2d\\u83b7\\u5f97\\u4e86\\u5e7f\\u5927\\u5ba2\\u6237\\u7684\\u8ba4\\u53ef\\u3002\\u6301\\u7eed\\u3001\\u4e13\\u6ce8\\u3001\\u521b\\u65b0\\u7684\\u4f01\\u4e1a\\u7406\\u5ff5\\u4e3a\\u66f4\\u591a\\u7684\\u5408\\u4f5c\\u4f19\\u4f34\\u521b\\u9020\\u4e00\\u6d41\\u7684\\u5a92\\u4f53\\u4ef7\\u503c\\uff01\",\"advantage\":\"\\/static\\/kindeditor\\/php\\/..\\/..\\/..\\/uploads\\/image\\/20181031\\/20181031131548_41963.png\"}', 1);
COMMIT;

-- ----------------------------
-- Table structure for t_sms
-- ----------------------------
DROP TABLE IF EXISTS `t_sms`;
CREATE TABLE `t_sms` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `template_id` bigint(20) NOT NULL,
  `template_name` varchar(24) NOT NULL,
  `params` varchar(1000) NOT NULL,
  `code` varchar(12) NOT NULL DEFAULT '',
  `deadline` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL,
  `phone` varchar(24) NOT NULL,
  `uid` bigint(20) NOT NULL DEFAULT '0' COMMENT '操作人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_sms
-- ----------------------------
BEGIN;
INSERT INTO `t_sms` VALUES (1, 1, 'sms_check_code', '{\"code\":\"1234\"}', '1234', 1490638104, 1, 1490637921, 1490715904, '18676669410', 2);
INSERT INTO `t_sms` VALUES (2, 1, 'sms_check_code', '{\"code\":\"213456\"}', '213456', 1490716741, 0, 1490716440, 1490716440, '18676669410', 2);
INSERT INTO `t_sms` VALUES (3, 1, 'sms_check_code', '{\"code\":\"439366\"}', '439366', 1490717285, 1, 1490716985, 1490717086, '18676669410', 0);
INSERT INTO `t_sms` VALUES (4, 1, 'sms_check_code', '{\"code\":\"768064\"}', '768064', 1490718444, 0, 1490718143, 1490718143, '18676669410', 0);
INSERT INTO `t_sms` VALUES (5, 1, 'sms_check_code', '{\"code\":\"935879\"}', '935879', 0, 1, 1490718206, 1490878179, '18676669410', 0);
COMMIT;

-- ----------------------------
-- Table structure for t_sms_template
-- ----------------------------
DROP TABLE IF EXISTS `t_sms_template`;
CREATE TABLE `t_sms_template` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `name` varchar(64) NOT NULL,
  `type` varchar(24) NOT NULL DEFAULT '1' COMMENT '1:阿里大鱼',
  `content` varchar(1000) NOT NULL,
  `extra` text,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL,
  `uid` bigint(20) NOT NULL DEFAULT '0',
  `id_no` varchar(24) NOT NULL COMMENT '模板ID',
  `is_code` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否验证码 0:不是1:是',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_sms_template
-- ----------------------------
BEGIN;
INSERT INTO `t_sms_template` VALUES (1, '短信验证码', 'sms_check_code', '1', '您绑定的手机号的验证码:${code},5分钟有效,请妥善保存.', 'code:验证码', 1, 1490631588, 1490715890, 2, 'SMS_47155017', 1);
COMMIT;

-- ----------------------------
-- Table structure for t_template
-- ----------------------------
DROP TABLE IF EXISTS `t_template`;
CREATE TABLE `t_template` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL,
  `name` varchar(32) NOT NULL,
  `cover` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL,
  `type` varchar(12) NOT NULL,
  `theme_id` bigint(20) NOT NULL DEFAULT '0',
  `config` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_template
-- ----------------------------
BEGIN;
INSERT INTO `t_template` VALUES (2, '主页', 'index', '', 1, 1482246388, 1482247242, 'index', 1, '');
INSERT INTO `t_template` VALUES (3, '栏目-单页', 'page', '', 1, 1482247813, 1540971707, 'category', 1, 'title_sub|副标题|char\r\ncover|栏目横幅或图标|img');
INSERT INTO `t_template` VALUES (4, '栏目-列表(新闻)', 'list', '', 1, 1482247962, 1540971953, 'category', 1, 'title_sub|副标题|char\r\ncover|栏目横幅或图标|img');
INSERT INTO `t_template` VALUES (5, '详情页', 'detail', '', 1, 1482247991, 1540971963, 'posts', 1, '');
INSERT INTO `t_template` VALUES (6, '首页', 'index', '', 1, 1490948854, 1490948854, 'index', 2, '');
INSERT INTO `t_template` VALUES (7, '首页', 'index', '', 1, 1492504671, 1492504671, 'index', 3, '');
INSERT INTO `t_template` VALUES (8, '栏目单页', 'category', '', 1, 1492504724, 1492504724, 'category', 3, '');
INSERT INTO `t_template` VALUES (9, '栏目-列表', 'list', '', 1, 1492504749, 1492504749, 'category', 3, '');
INSERT INTO `t_template` VALUES (10, '文章页', 'detail', '', 1, 1492504768, 1492504768, 'posts', 3, '');
INSERT INTO `t_template` VALUES (11, '产品-列表', 'product', '', 1, 1492504794, 1492504803, 'category', 3, '');
INSERT INTO `t_template` VALUES (12, '产品-详情', 'product-detail', '', 1, 1492504834, 1492504834, 'posts', 3, '');
INSERT INTO `t_template` VALUES (13, '栏目-列表(案例)', 'list_case', '', 1, 1540914795, 1540971946, 'category', 1, 'title_sub|副标题|char\r\ncover|栏目横幅或图标|img');
COMMIT;

-- ----------------------------
-- Table structure for t_theme
-- ----------------------------
DROP TABLE IF EXISTS `t_theme`;
CREATE TABLE `t_theme` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `cover` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_theme
-- ----------------------------
BEGIN;
INSERT INTO `t_theme` VALUES (1, '默认官网主题', 'default', '', 1, 1482244956, 1490948788);
INSERT INTO `t_theme` VALUES (2, 'p2p模板', 'p2p', '', 1, 1490948814, 1490948814);
INSERT INTO `t_theme` VALUES (3, '商城模板', 'mall', '', 1, 1492504646, 1492504646);
COMMIT;

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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of t_user
-- ----------------------------
BEGIN;
INSERT INTO `t_user` VALUES (1, 'root开发者', 'root@dev.io', 'e10adc3949ba59abbe56e057f20f883e', '', 1, 0, 1482130035, 0);
INSERT INTO `t_user` VALUES (2, 'Admin', 'admin@admin.com', 'e10adc3949ba59abbe56e057f20f883e', '123456789', 1, 1480489405, 1489928835, 0);
INSERT INTO `t_user` VALUES (9, '网站管理', 'web@admin.com', 'e10adc3949ba59abbe56e057f20f883e', '18676669410', 0, 1490455912, 1490456171, 2);
COMMIT;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for t_user_config
-- ----------------------------
DROP TABLE IF EXISTS `t_user_config`;
CREATE TABLE `t_user_config` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) NOT NULL,
  `wechat` text COLLATE utf8mb4_unicode_ci,
  `sms_alidayu` text COLLATE utf8mb4_unicode_ci,
  `email` text COLLATE utf8mb4_unicode_ci,
  `domain` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of t_user_config
-- ----------------------------
BEGIN;
INSERT INTO `t_user_config` VALUES (2, 6, '1', '2', '3', '4', 0, 0);
INSERT INTO `t_user_config` VALUES (3, 2, '{\"name\":\"\\u6a59\\u5b50\\u6d4b\\u8bd5\",\"app_id\":\"wx8d46158d0a0b5f26\",\"app_secret\":\"ae12a206a30d5061d433758bafcbe877\",\"mch_id\":\"1445475902\",\"pay_notify_url\":\"\\/pay\\/wxNotify\",\"pay_api_secret\":\"e6d91ce13d421863e67cda73a79b158e\",\"access_token_type\":\"db\"}', '{\"appkey\":\"23623781\",\"secret\":\"48677c8acb6f5c7f38bb25f258c860e4\",\"sms_sing_name\":\"ORANGE\\u6a59\\u5b50\",\"sms_deadline\":\"5\"}', '{\"debug\":\"1\",\"host\":\"smtp.yeah.net\",\"username\":\"smallgroup@yeah.net\",\"password\":\"lc19890512\",\"port\":\"465\",\"from_address\":\"smallgroup@yeah.net\",\"from_name\":\"orange\"}', '', 0, 1490883642);
COMMIT;

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
BEGIN;
INSERT INTO `t_user_project` VALUES (1, 7, '1,2,3');
INSERT INTO `t_user_project` VALUES (2, 8, '2');
COMMIT;

-- ----------------------------
-- Table structure for t_wx_token
-- ----------------------------
DROP TABLE IF EXISTS `t_wx_token`;
CREATE TABLE `t_wx_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appid` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `time_out` int(11) NOT NULL,
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of t_wx_token
-- ----------------------------
BEGIN;
INSERT INTO `t_wx_token` VALUES (1, 'wx8d46158d0a0b5f26', 'vEZB5vNABhP1FgQwSvw1glcpXLiwLHEKr3RedVNo13G79VXMjiqhbQrX9HiDEPsa9OEttLQ51WK4Xsb8WNJcVgQe4OArPXERiC8rULNpw-y9sFIqTPndSKbWt7M-3eeRRHXaADAUKA', 1491886032, 1489996033, 1491878932, 'access_token');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
