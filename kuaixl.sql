-- phpMyAdmin SQL Dump
-- version phpStudy 2014
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2020 �?09 �?21 �?01:27
-- 服务器版本: 5.5.53
-- PHP 版本: 5.5.38

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `kuaixl`
--

-- --------------------------------------------------------

--
-- 表的结构 `zy_action`
--

CREATE TABLE IF NOT EXISTS `zy_action` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` char(30) NOT NULL DEFAULT '' COMMENT '行为唯一标识',
  `title` char(80) NOT NULL DEFAULT '' COMMENT '行为说明',
  `remark` char(140) NOT NULL DEFAULT '' COMMENT '行为描述',
  `rule` text COMMENT '行为规则',
  `log` text COMMENT '日志规则',
  `type` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '类型',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='系统行为表' AUTO_INCREMENT=20 ;

--
-- 转存表中的数据 `zy_action`
--

INSERT INTO `zy_action` (`id`, `name`, `title`, `remark`, `rule`, `log`, `type`, `status`, `update_time`) VALUES
(12, 'update_shopp_order', '更新订单', '订单状态更新', '', '', 1, 1, 1570786769),
(13, 'del_shopp_order', '订单删除', '删除订单', '', '', 1, 1, 1570786800),
(14, 'updata_good', '更新商品', '商品添加，修改，删除', '', '', 1, 1, 1570786945),
(15, 'updata_coupon', '更新优惠券', '优惠券添加，修改，删除', '', '', 1, 1, 1570787353);

-- --------------------------------------------------------

--
-- 表的结构 `zy_action_log`
--

CREATE TABLE IF NOT EXISTS `zy_action_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `action_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '行为id',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '执行用户id',
  `action_ip` bigint(20) NOT NULL COMMENT '执行行为者ip',
  `model` varchar(50) NOT NULL DEFAULT '' COMMENT '触发行为的表',
  `record_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '触发行为的数据id',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '日志备注',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '执行行为的时间',
  PRIMARY KEY (`id`),
  KEY `action_ip_ix` (`action_ip`),
  KEY `action_id_ix` (`action_id`),
  KEY `user_id_ix` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED COMMENT='行为日志表' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `zy_action_log`
--

INSERT INTO `zy_action_log` (`id`, `action_id`, `user_id`, `action_ip`, `model`, `record_id`, `remark`, `status`, `create_time`) VALUES
(1, 14, 1, 2130706433, 'good', 1, '操作url：/admin/good/addgoods.html', 1, 1600239392);

-- --------------------------------------------------------

--
-- 表的结构 `zy_ad`
--

CREATE TABLE IF NOT EXISTS `zy_ad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '名称',
  `desc` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `url` varchar(255) NOT NULL DEFAULT '',
  `tid` int(3) NOT NULL DEFAULT '0' COMMENT '广告位ID',
  `addtime` varchar(11) NOT NULL DEFAULT '',
  `endtime` varchar(11) NOT NULL DEFAULT '',
  `sortorder` varchar(255) NOT NULL DEFAULT '0',
  `img` varchar(255) NOT NULL DEFAULT '' COMMENT '链接地址',
  `isurl` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='广告表' AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `zy_ad`
--

INSERT INTO `zy_ad` (`id`, `name`, `desc`, `url`, `tid`, `addtime`, `endtime`, `sortorder`, `img`, `isurl`) VALUES
(1, '年前不服输', '', '', 1, '1600332979', '1608972979', '0', '/upload/images/20200917/786e8d72d60ecdd0c4f6a61a0559c721.jpg', 0),
(2, '抓住夏天计划', '', '', 2, '1600333184', '1608973184', '0', '/upload/images/20200917/ee5559238a6f78d228b693604b5dbd9c.jpg', 0);

-- --------------------------------------------------------

--
-- 表的结构 `zy_address`
--

CREATE TABLE IF NOT EXISTS `zy_address` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `name` varchar(15) NOT NULL COMMENT '姓名',
  `mobile` varchar(11) NOT NULL COMMENT '电话',
  `city` varchar(50) NOT NULL COMMENT '地区',
  `address` varchar(150) NOT NULL COMMENT '详细地址',
  `is_more` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1默认地址',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='地址' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `zy_adtype`
--

CREATE TABLE IF NOT EXISTS `zy_adtype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `isdel` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `zy_adtype`
--

INSERT INTO `zy_adtype` (`id`, `name`, `isdel`) VALUES
(1, '顶部广告位', 0),
(2, '底部广告位', 0);

-- --------------------------------------------------------

--
-- 表的结构 `zy_article`
--

CREATE TABLE IF NOT EXISTS `zy_article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `typeid` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `keyword` varchar(255) NOT NULL DEFAULT '' COMMENT '关键词',
  `content` longtext NOT NULL,
  `addtime` varchar(255) NOT NULL DEFAULT '0',
  `is_del` varchar(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文章表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `zy_arttype`
--

CREATE TABLE IF NOT EXISTS `zy_arttype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL DEFAULT '',
  `pid` varchar(255) NOT NULL DEFAULT '',
  `is_foarticle` varchar(255) NOT NULL DEFAULT '0' COMMENT '是否为可发布文章分类',
  `t_content` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文章类型' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `zy_auth_extend`
--

CREATE TABLE IF NOT EXISTS `zy_auth_extend` (
  `group_id` mediumint(10) unsigned NOT NULL COMMENT '用户id',
  `extend_id` mediumint(8) unsigned NOT NULL COMMENT '扩展表中数据的id',
  `type` tinyint(1) unsigned NOT NULL COMMENT '扩展类型标识 1:栏目分类权限;2:模型权限',
  UNIQUE KEY `group_extend_type` (`group_id`,`extend_id`,`type`),
  KEY `uid` (`group_id`),
  KEY `group_id` (`extend_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户组与分类的对应关系表';

-- --------------------------------------------------------

--
-- 表的结构 `zy_auth_group`
--

CREATE TABLE IF NOT EXISTS `zy_auth_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户组id,自增主键',
  `module` varchar(20) NOT NULL DEFAULT '' COMMENT '用户组所属模块',
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '组类型',
  `title` char(20) NOT NULL DEFAULT '' COMMENT '用户组中文名称',
  `description` varchar(80) NOT NULL DEFAULT '' COMMENT '描述信息',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '用户组状态：为1正常，为0禁用,-1为删除',
  `rules` varchar(500) NOT NULL DEFAULT '' COMMENT '用户组拥有的规则id，多个规则 , 隔开',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

--
-- 转存表中的数据 `zy_auth_group`
--

INSERT INTO `zy_auth_group` (`id`, `module`, `type`, `title`, `description`, `status`, `rules`) VALUES
(1, 'admin', 1, '默认用户组', '首页、用户、系统权限', 1, '1,3,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,88,94,95,107,108,109,110,229,230,231,232,235,236,239,240,241,242,243,244,245,246,247,248,249,250,252,253,254,255,256,257,258,259,260,261,262,263,264,266,268,269,270,271,272,273');

-- --------------------------------------------------------

--
-- 表的结构 `zy_auth_group_access`
--

CREATE TABLE IF NOT EXISTS `zy_auth_group_access` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `group_id` mediumint(8) unsigned NOT NULL COMMENT '用户组id',
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `zy_auth_group_access`
--

INSERT INTO `zy_auth_group_access` (`uid`, `group_id`) VALUES
(2, 1);

-- --------------------------------------------------------

--
-- 表的结构 `zy_auth_rule`
--

CREATE TABLE IF NOT EXISTS `zy_auth_rule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '规则id,自增主键',
  `module` varchar(20) NOT NULL COMMENT '规则所属module',
  `type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1-url;2-主菜单',
  `name` char(80) NOT NULL DEFAULT '' COMMENT '规则唯一英文标识',
  `title` char(20) NOT NULL DEFAULT '' COMMENT '规则中文描述',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否有效(0:无效,1:有效)',
  `condition` varchar(300) NOT NULL DEFAULT '' COMMENT '规则附加条件',
  PRIMARY KEY (`id`),
  KEY `module` (`module`,`status`,`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=463 ;

-- --------------------------------------------------------

--
-- 表的结构 `zy_cate`
--

CREATE TABLE IF NOT EXISTS `zy_cate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '链接',
  `img` varchar(255) NOT NULL DEFAULT '',
  `addtime` varchar(255) NOT NULL DEFAULT '',
  `is_show` varchar(255) NOT NULL DEFAULT '1',
  `sort_order` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='分类表' AUTO_INCREMENT=5 ;

--
-- 转存表中的数据 `zy_cate`
--

INSERT INTO `zy_cate` (`id`, `name`, `url`, `img`, `addtime`, `is_show`, `sort_order`) VALUES
(1, '首页', '', '', '1600238997', '1', '0'),
(2, '资讯', '', '', '1600239020', '1', '0'),
(3, '活动', '', '', '1600239034', '1', '0'),
(4, '经销商', '', '', '1600239111', '1', '0');

-- --------------------------------------------------------

--
-- 表的结构 `zy_category`
--

CREATE TABLE IF NOT EXISTS `zy_category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `catename` varchar(50) NOT NULL COMMENT '分类名',
  `photo_x` varchar(255) NOT NULL COMMENT '分类图片',
  `sort` int(11) NOT NULL COMMENT '排序',
  `is_show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1显示',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1删除',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='商品分类' AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `zy_category`
--

INSERT INTO `zy_category` (`id`, `pid`, `catename`, `photo_x`, `sort`, `is_show`, `is_delete`) VALUES
(1, 0, '虾仁系列', '/upload/uploads/20200916/da03e243c76e68e2f9976acae0b5deaa.png', 0, 1, 0),
(2, 1, '芝麻油', '/upload/uploads/20200916/b96e6ccac8422267740df6a68d4eebc1.png', 213, 1, 0);

-- --------------------------------------------------------

--
-- 表的结构 `zy_config`
--

CREATE TABLE IF NOT EXISTS `zy_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '配置ID',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '配置名称',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '配置类型',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '配置说明',
  `group` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '配置分组',
  `extra` varchar(255) NOT NULL DEFAULT '' COMMENT '配置值',
  `remark` varchar(100) NOT NULL DEFAULT '' COMMENT '配置说明',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `value` text COMMENT '配置值',
  `sort` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `is_show` int(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_name` (`name`),
  KEY `type` (`type`),
  KEY `group` (`group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=66 ;

--
-- 转存表中的数据 `zy_config`
--

INSERT INTO `zy_config` (`id`, `name`, `type`, `title`, `group`, `extra`, `remark`, `create_time`, `update_time`, `status`, `value`, `sort`, `is_show`) VALUES
(4, 'web_site_close', 4, '关闭站点', 1, '0:关闭,1:开启', '站点关闭后其他用户不能访问，管理员可以正常访问', 1378898976, 1379235296, 1, '1', 4, 0),
(5, 'web_site_admin_title', 1, '后台标题', 1, '后台内容管理系统', '后台网站标题', 1532333752, 1532333945, 1, '后台管理', 4, 0),
(9, 'config_type_list', 3, '配置类型列表', 4, '', '主要用于数据解析和页面表单的生成', 1378898976, 1540459373, 1, '0:数字\r\n1:字符\r\n2:文本\r\n3:数组\r\n4:枚举\r\n5:图片', 4, 0),
(20, 'config_group_list', 3, '配置分组', 4, '', '配置分组', 1379228036, 1540459384, 1, '1:基本\r\n2:内容\r\n3:用户\r\n4:系统', 12, 0),
(22, 'auth_config', 3, 'Auth配置', 4, '', '自定义Auth.class.php类配置', 1379409310, 1379409564, 1, 'auth_on:1\r\nauth_type:2', 22, 0),
(25, 'list_rows', 0, '后台每页记录数', 2, '', '后台数据每页显示记录数', 1379503896, 1576551188, 1, '15', 27, 0),
(26, 'user_allow_register', 4, '是否允许用户注册', 3, '0:关闭注册\r\n1:允许注册', '是否开放用户注册', 1379504487, 1379504580, 1, '0', 8, 0),
(28, 'data_backup_path', 1, '数据库备份根路径', 4, '', '路径必须以 / 结尾', 1381482411, 1381482411, 1, './static/data/', 14, 0),
(29, 'data_backup_part_size', 0, '数据库备份卷大小', 4, '', '该值用于限制压缩后的分卷最大长度。单位：B；建议设置20M', 1381482488, 1381729564, 1, '20971520', 19, 0),
(30, 'data_backup_compress', 4, '数据库备份文件是否启用压缩', 4, '0:不压缩\r\n1:启用压缩', '压缩备份文件需要PHP环境支持gzopen,gzwrite函数', 1381713345, 1381729544, 1, '1', 24, 0),
(31, 'data_backup_compress_level', 4, '数据库备份文件压缩级别', 4, '1:普通\r\n4:一般\r\n9:最高', '数据库备份文件的压缩级别，该配置在开启压缩时生效', 1381713408, 1381713408, 1, '9', 29, 0),
(32, 'develop_mode', 4, '开启开发者模式', 4, '0:关闭\r\n1:开启', '是否开启开发者模式', 1383105995, 1578360113, 1, '0', 30, 0),
(33, 'allow_visit', 3, '不受限控制器方法', 0, '', '', 1386644047, 1483670290, 1, '0:article/draftbox\r\n1:article/mydocument\r\n2:Category/tree\r\n3:Index/verify\r\n4:file/upload\r\n5:file/download\r\n6:user/updatePassword\r\n7:user/updateNickname\r\n8:user/submitPassword\r\n9:user/submitNickname\r\n10:file/uploadpicture\r\n11:admin/delcache', 6, 0),
(34, 'deny_visit', 3, '超管专限控制器方法', 0, '', '仅超级管理员可访问的控制器方法', 1386644141, 1386644659, 1, '0:Addons/addhook\r\n1:Addons/edithook\r\n2:Addons/delhook\r\n3:Addons/updateHook\r\n4:Admin/getMenus\r\n5:Admin/recordList\r\n6:AuthManager/updateRules\r\n7:AuthManager/tree', 10, 0),
(36, 'admin_allow_ip', 2, '后台允许访问IP', 4, '', '多个用逗号分隔，如果不配置表示不限制IP访问', 1387165454, 1387165553, 1, '', 31, 0),
(37, 'app_trace', 4, '是否显示页面Trace', 4, '0:关闭\r\n1:开启', '是否显示页面Trace信息', 1387165685, 1387165685, 1, '0', 5, 0),
(38, 'app_debug', 4, '应用调试模式', 4, '0:关闭\r\n1:开启', '网站正式部署建议关闭', 1478522232, 1578360133, 1, '0', 15, 0),
(39, 'template.view_path', 1, '模板主题', 0, 'dd', '', 1479883093, 1479883093, 1, 'dd', 17, 0),
(40, 'admin_view_path', 4, '后台模板主题', 1, 'default:默认 ', '添加主题请在配置管理添加', 1479986058, 1479991430, 1, 'default', 6, 0),
(41, 'home_view_path', 4, '前台模板主题', 1, 'default:默认', '添加主题请在配置管理添加', 1479986147, 1479991437, 1, 'default', 7, 0),
(63, 'web_site_title', 0, '网站标题', 1, '', '', 1600239966, 1600239976, 1, '', 0, 1),
(64, 'web_site_description', 0, '站点描述', 1, '', '', 1600240027, 1600240054, 1, '', 0, 1),
(65, 'web_site_keywords', 0, '站点关键词', 1, '', '', 1600240174, 1600240174, 1, '', 0, 1);

-- --------------------------------------------------------

--
-- 表的结构 `zy_coupon`
--

CREATE TABLE IF NOT EXISTS `zy_coupon` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '优惠券名称',
  `maxprice` decimal(5,0) NOT NULL DEFAULT '0' COMMENT '启用金额',
  `minprice` decimal(5,0) NOT NULL DEFAULT '0' COMMENT '减免金额',
  `total` int(8) NOT NULL DEFAULT '0' COMMENT '库存',
  `number` int(3) NOT NULL DEFAULT '1' COMMENT '每人限领',
  `days` int(5) NOT NULL DEFAULT '0' COMMENT '有效时间',
  `is_show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1显示',
  `addtime` varchar(10) NOT NULL COMMENT '添加时间',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1删除',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商城优惠券' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `zy_good`
--

CREATE TABLE IF NOT EXISTS `zy_good` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cateid` int(11) NOT NULL DEFAULT '0' COMMENT '所属分类',
  `title` varchar(50) NOT NULL COMMENT '商品名',
  `price` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '价格',
  `photo_x` varchar(150) DEFAULT NULL COMMENT '缩略图',
  `total` int(8) NOT NULL DEFAULT '0' COMMENT '库存',
  `iscommend` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1推荐',
  `isoffer` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1特卖',
  `detail` varchar(100) NOT NULL COMMENT '商品描述',
  `content` text NOT NULL COMMENT '商品详情',
  `photo_string` text COMMENT '商品轮播图',
  `weight` int(10) NOT NULL DEFAULT '0' COMMENT '重量',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `is_show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0上架1上架',
  `addtime` varchar(10) NOT NULL COMMENT '添加时间',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1删除',
  `is_group` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否开启团购',
  `groupprice` decimal(8,2) NOT NULL COMMENT '团购价格',
  `groupendtime` varchar(10) NOT NULL COMMENT '拼团限时',
  `grouptime` tinyint(5) NOT NULL COMMENT '组团限时',
  `group_num` int(5) NOT NULL COMMENT '拼团人数',
  `buy_limit` int(5) NOT NULL DEFAULT '0' COMMENT '购买拼团次数限制',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='商品' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `zy_good`
--

INSERT INTO `zy_good` (`id`, `cateid`, `title`, `price`, `photo_x`, `total`, `iscommend`, `isoffer`, `detail`, `content`, `photo_string`, `weight`, `sort`, `is_show`, `addtime`, `is_delete`, `is_group`, `groupprice`, `groupendtime`, `grouptime`, `group_num`, `buy_limit`) VALUES
(1, 1, '2019中国环境产业高峰论坛', '48.00', '/upload/images/20200916/2e131dbbeed38a2c4fb057b6d01aea89.png', 10000, 1, 1, '如何打造出既省钱又美观的居室生活？如何打造出既省钱又美观的居室生活？如何打造出既省钱又美观的居室生活？如何打造出既省钱又美观的居室生活？', '', NULL, 5, 0, 1, '1600239392', 0, 0, '0.00', '', 0, 0, 0);

-- --------------------------------------------------------

--
-- 表的结构 `zy_good_option`
--

CREATE TABLE IF NOT EXISTS `zy_good_option` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `goodid` int(11) NOT NULL DEFAULT '0' COMMENT '商品ID',
  `title` varchar(50) NOT NULL COMMENT '规格名',
  `price` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '价格',
  `groupprice` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '团购价格',
  `total` int(8) NOT NULL DEFAULT '0' COMMENT '库存',
  `weight` int(8) NOT NULL DEFAULT '0' COMMENT '重量（克）',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商品规格' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `zy_info`
--

CREATE TABLE IF NOT EXISTS `zy_info` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `appid` varchar(50) DEFAULT NULL COMMENT '小程序appid',
  `appsecret` varchar(50) DEFAULT NULL COMMENT '小程序appsecret',
  `mchid` varchar(50) DEFAULT NULL COMMENT '商户号',
  `mchkey` varchar(50) DEFAULT NULL COMMENT '商户密钥',
  `discount` decimal(3,1) DEFAULT NULL COMMENT '折扣',
  `score` int(5) DEFAULT '0' COMMENT '积分兑换',
  `minprice` decimal(8,0) DEFAULT '0' COMMENT '充值限额',
  `content` text COMMENT '充值说明',
  `jfcontent` text COMMENT '积分规则',
  `shopplabel` text,
  `playlabel` text NOT NULL COMMENT '游玩评论标签',
  `hotellabel` text NOT NULL COMMENT '酒店评论标签',
  `ticketlabel` text NOT NULL COMMENT '套票评论标签',
  `activlabel` text NOT NULL COMMENT '活动评论标签',
  `refundcontent` text COMMENT '退款原因',
  `levelrebate` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `zy_menu`
--

CREATE TABLE IF NOT EXISTS `zy_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '文档ID',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类ID',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序（同级有效）',
  `url` char(255) NOT NULL DEFAULT '' COMMENT '链接地址',
  `hide` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否隐藏',
  `tip` varchar(255) NOT NULL DEFAULT '' COMMENT '提示',
  `group` varchar(50) DEFAULT '' COMMENT '分组',
  `is_dev` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否仅开发者模式可见',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  `icon` varchar(20) DEFAULT NULL COMMENT '字体图标',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=384 ;

--
-- 转存表中的数据 `zy_menu`
--

INSERT INTO `zy_menu` (`id`, `title`, `pid`, `sort`, `url`, `hide`, `tip`, `group`, `is_dev`, `status`, `icon`) VALUES
(1, '平台首页', 0, 1, 'Index/index', 0, '', '', 0, 1, 'fa-home'),
(2, '权限管理', 0, 12, 'User/index', 0, '', '', 0, 1, 'fa-user'),
(3, '系统设置', 0, 100, 'Config/group', 0, '', '', 0, 1, 'fa-gear'),
(4, '用户信息', 2, 0, 'User/index', 0, '', '用户管理', 0, 1, ''),
(5, '权限管理', 2, 0, 'AuthManager/index', 0, '', '用户管理', 0, 1, NULL),
(6, '用户行为', 2, 0, 'User/action', 0, '', '行为管理', 1, 1, NULL),
(7, '行为日志', 2, 0, 'Action/actionlog', 0, '', '行为管理', 0, 1, NULL),
(8, '修改密码', 2, 0, 'User/updatePassword', 1, '', '', 0, 1, NULL),
(9, '修改昵称', 2, 0, 'User/updateNickname', 1, '', '', 0, 1, NULL),
(10, '查看行为日志', 7, 0, 'action/edit', 1, '', '', 0, 1, NULL),
(11, '新增用户', 4, 0, 'User/add', 0, '添加新用户', '', 0, 1, NULL),
(12, '新增用户行为', 6, 0, 'User/addaction', 0, '', '', 0, 1, NULL),
(13, '编辑用户行为', 6, 0, 'User/editaction', 0, '', '', 0, 1, NULL),
(14, '保存用户行为', 6, 0, 'User/saveAction', 0, '"用户->用户行为"保存编辑和新增的用户行为', '', 0, 1, NULL),
(15, '变更行为状态', 6, 0, 'User/setStatus', 0, '"用户->用户行为"中的启用,禁用和删除权限', '', 0, 1, NULL),
(16, '禁用会员', 6, 0, 'User/changeStatus?method=forbidUser', 0, '"用户->用户信息"中的禁用', '', 0, 1, NULL),
(17, '启用会员', 6, 0, 'User/changeStatus?method=resumeUser', 0, '"用户->用户信息"中的启用', '', 0, 1, NULL),
(18, '删除会员', 6, 0, 'User/changeStatus?method=deleteUser', 0, '"用户->用户信息"中的删除', '', 0, 1, NULL),
(19, '删除', 5, 0, 'AuthManager/changeStatus?method=deleteGroup', 0, '删除用户组', '', 0, 1, NULL),
(20, '禁用', 5, 0, 'AuthManager/changeStatus?method=forbidGroup', 0, '禁用用户组', '', 0, 1, NULL),
(21, '恢复', 5, 0, 'AuthManager/changeStatus?method=resumeGroup', 0, '恢复已禁用的用户组', '', 0, 1, NULL),
(22, '新增', 5, 0, 'AuthManager/createGroup', 0, '创建新的用户组', '', 0, 1, NULL),
(23, '编辑', 5, 0, 'AuthManager/editGroup', 0, '编辑用户组名称和描述', '', 0, 1, NULL),
(24, '保存用户组', 5, 0, 'AuthManager/writeGroup', 0, '新增和编辑用户组的"保存"按钮', '', 0, 1, NULL),
(25, '授权', 5, 0, 'AuthManager/group', 0, '"后台 \\ 用户 \\ 用户信息"列表页的"授权"操作按钮,用于设置用户所属用户组', '', 0, 1, NULL),
(26, '访问授权', 5, 0, 'AuthManager/access', 0, '"后台 \\ 用户 \\ 权限管理"列表页的"访问授权"操作按钮', '', 0, 1, NULL),
(27, '成员授权', 5, 0, 'AuthManager/user', 0, '"后台 \\ 用户 \\ 权限管理"列表页的"成员授权"操作按钮', '', 0, 1, NULL),
(28, '解除授权', 5, 0, 'AuthManager/removeFromGroup', 0, '"成员授权"列表页内的解除授权操作按钮', '', 0, 1, NULL),
(29, '保存成员授权', 5, 0, 'AuthManager/addToGroup', 0, '"用户信息"列表页"授权"时的"保存"按钮和"成员授权"里右上角的"添加"按钮)', '', 0, 1, NULL),
(30, '分类授权', 5, 0, 'AuthManager/category', 0, '"后台 \\ 用户 \\ 权限管理"列表页的"分类授权"操作按钮', '', 0, 1, NULL),
(31, '保存分类授权', 5, 0, 'AuthManager/addToCategory', 0, '"分类授权"页面的"保存"按钮', '', 0, 1, NULL),
(32, '模型授权', 5, 0, 'AuthManager/modelauth', 0, '"后台 \\ 用户 \\ 权限管理"列表页的"模型授权"操作按钮', '', 0, 1, NULL),
(33, '保存模型授权', 5, 0, 'AuthManager/addToModel', 0, '"分类授权"页面的"保存"按钮', '', 0, 1, NULL),
(34, '网站设置', 3, 0, 'Config/group', 0, '', '系统设置', 0, 1, ''),
(35, '配置管理', 3, 0, 'Config/index', 0, '', '系统设置', 0, 1, ''),
(36, '菜单管理', 3, 0, 'Menu/index', 0, '', '系统设置', 0, 1, NULL),
(37, '备份数据库', 3, 0, 'Database/index?type=export', 0, '', '数据备份', 0, 1, ''),
(38, '还原数据库', 3, 0, 'Database/index?type=import', 0, '', '数据备份', 0, 1, NULL),
(39, '编辑', 35, 0, 'Config/edit', 0, '新增编辑和保存配置', '', 0, 1, NULL),
(40, '删除', 35, 0, 'Config/del', 0, '删除配置', '', 0, 1, NULL),
(41, '新增', 35, 0, 'Config/add', 0, '新增配置', '', 0, 1, NULL),
(42, '保存', 35, 0, 'Config/save', 0, '保存配置', '', 0, 1, NULL),
(43, '备份', 37, 0, 'Database/export', 0, '备份数据库', '', 0, 1, NULL),
(44, '优化表', 37, 0, 'Database/optimize', 0, '优化数据表', '', 0, 1, NULL),
(45, '修复表', 37, 0, 'Database/repair', 0, '修复数据表', '', 0, 1, NULL),
(46, '恢复', 38, 0, 'Database/import', 0, '数据库恢复', '', 0, 1, NULL),
(47, '删除', 38, 0, 'Database/del', 0, '删除备份文件', '', 0, 1, NULL),
(48, '新增', 36, 0, 'Menu/add', 0, '', '系统设置', 0, 1, NULL),
(49, '编辑', 36, 0, 'Menu/edit', 0, '', '', 0, 1, NULL),
(50, '导入', 36, 0, 'Menu/import', 0, '', '', 0, 1, NULL),
(51, '排序', 36, 0, 'Menu/sort', 1, '', '', 0, 1, NULL),
(52, '排序', 35, 0, 'Config/sort', 1, '', '', 0, 1, NULL),
(123, '导航菜单', 150, 1, 'category/index', 0, '', '', 0, 1, ''),
(171, '商品管理', 167, 0, 'Good/index', 0, '', '', 0, 1, ''),
(128, '会员管理', 0, 5, 'myuser/index', 0, '', '', 0, 1, ''),
(132, '文章分类', 136, 0, 'category/type', 0, '', '', 0, 1, ''),
(167, '商城管理', 0, 5, 'Good/index', 0, '', '', 0, 1, ''),
(168, '商品分类', 167, 0, 'Good/cate', 0, '', '', 0, 1, ''),
(141, '广告管理', 0, 8, 'ad/index', 0, '广告管理', '', 0, 1, ''),
(142, '广告位置', 141, 0, 'ad/index', 0, '', '', 0, 1, ''),
(150, '系统设置', 0, 1, 'Webconfig/group', 0, '', '', 0, 1, ''),
(157, '基础信息', 150, 0, 'webconfig/group', 0, '', '', 0, 1, ''),
(158, '文章列表', 136, 0, 'category/article', 0, '', '', 0, 1, ''),
(166, '广告管理', 141, 0, 'ad/bander', 0, '', '', 0, 1, ''),
(190, '优惠管理', 167, 0, 'Good/coupon', 0, '', '', 0, 1, ''),
(211, '小程序配置', 150, 0, 'Info/index', 0, '', '', 0, 1, ''),
(218, '商城订单', 169, 0, 'Group/order', 0, '', '', 0, 1, ''),
(226, '等级管理', 128, 0, 'myuser/level', 0, '', '', 0, 1, ''),
(227, '用户管理', 128, 0, 'Myuser/index', 0, '', '', 0, 1, ''),
(229, '添加', 168, 0, 'Good/addcate', 0, '', '', 0, 1, ''),
(230, '显隐', 168, 0, 'Good/setproperty?model=category', 0, '', '', 0, 1, ''),
(231, '删除', 168, 0, 'Good/changeSent?method=deleteuser&model=category', 0, '', '', 0, 1, ''),
(270, '添加', 171, 0, 'Good/addgoods', 0, '', '', 0, 1, ''),
(273, '属性修改', 171, 0, 'Good/setproperty?model=good', 0, '', '', 0, 1, ''),
(275, '删除', 171, 0, 'Good/changeSent?method=deleteuser&model=good', 0, '', '', 0, 1, ''),
(277, '添加', 190, 0, 'Good/addcoupon', 0, '', '', 0, 1, ''),
(304, '显隐', 190, 0, 'Good/setproperty?model=coupon', 0, '', '', 0, 1, ''),
(309, '删除', 190, 0, 'Good/changeSent?method=deleteuser&model=coupon', 0, '', '', 0, 1, ''),
(311, '状态', 195, 0, 'Good/setproperty?model=miaosha_good', 0, '', '', 0, 1, ''),
(317, '用户管理', 227, 0, 'Myuser/index', 0, '', '', 0, 1, ''),
(319, '改变核销身份', 227, 0, 'Myuser/set_type', 0, '', '', 0, 1, ''),
(321, '详情', 227, 0, 'Myuser/info', 0, '', '', 0, 1, ''),
(376, '订单详情', 218, 0, 'Group/order_info', 0, '', '', 0, 1, ''),
(377, '删除', 218, 0, 'Group/order_del', 0, '', '', 0, 1, ''),
(378, '退款', 218, 0, 'Group/orderrefund', 0, '', '', 0, 1, ''),
(379, '操作状态', 218, 0, 'Group/order_sent', 0, '', '', 0, 1, '');

-- --------------------------------------------------------

--
-- 表的结构 `zy_mycoupon`
--

CREATE TABLE IF NOT EXISTS `zy_mycoupon` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `couponid` int(11) NOT NULL COMMENT '优惠券Id',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `maxprice` float(8,0) NOT NULL DEFAULT '0' COMMENT '启用金额',
  `minprice` float(5,0) NOT NULL DEFAULT '0' COMMENT '减免金额',
  `addtime` varchar(10) NOT NULL COMMENT '领取时间',
  `endtime` varchar(10) NOT NULL COMMENT '到期时间',
  `sent` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0未使用1使用2过期',
  `is_remind` tinyint(1) DEFAULT '0' COMMENT '1提醒',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `zy_shopp_order`
--

CREATE TABLE IF NOT EXISTS `zy_shopp_order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `order_sn` varchar(15) DEFAULT NULL COMMENT '订单号',
  `order_code` varchar(15) NOT NULL COMMENT '验证码',
  `price` decimal(8,2) NOT NULL COMMENT '订单总额',
  `total` int(5) NOT NULL DEFAULT '0' COMMENT '订单总数',
  `mailprice` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '邮发',
  `goodsprice` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '商品价格',
  `couponprice` decimal(8,2) DEFAULT '0.00' COMMENT '优惠券金额',
  `is_dispatch` tinyint(1) NOT NULL DEFAULT '0' COMMENT '配送方式:1线上2自提',
  `address` varchar(300) DEFAULT NULL COMMENT '配送地址',
  `dispatchid` int(11) NOT NULL DEFAULT '0' COMMENT '自提点id',
  `dispat_time` varchar(15) DEFAULT NULL COMMENT '自提预约时间',
  `pay_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '支付状态(1成功)',
  `pay_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '支付方式(0余额1微信支付)',
  `pay_time` varchar(10) DEFAULT NULL COMMENT '支付时间',
  `trade_no` varchar(50) DEFAULT NULL COMMENT '流水号',
  `couponid` int(11) NOT NULL DEFAULT '0' COMMENT '优惠券id',
  `addtime` varchar(10) NOT NULL COMMENT '订单提交时间',
  `sent` tinyint(1) NOT NULL DEFAULT '0' COMMENT '发货状态(1待发货2待收货3待评论4完成-1退货)',
  `qrcode` varchar(250) DEFAULT NULL COMMENT '验证码',
  `istypes` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0商城订单1秒杀2拼团',
  `msid` int(11) NOT NULL COMMENT '秒杀活动id',
  `is_group` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1拼团成功',
  `grouptime` varchar(10) DEFAULT NULL COMMENT '拼团失效时间',
  `goodid` int(11) NOT NULL DEFAULT '0' COMMENT '拼团商品id',
  `refundtime` varchar(10) DEFAULT NULL COMMENT '审请退款时间',
  `isrefund` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0未退款1退款',
  `express` varchar(30) DEFAULT NULL COMMENT '快递公司',
  `expresssn` varchar(50) DEFAULT NULL COMMENT '快递单号',
  `expresstime` varchar(10) DEFAULT NULL COMMENT '发货时间',
  `remark` varchar(300) DEFAULT NULL COMMENT '退款理由',
  `hsuser_id` int(11) DEFAULT '0' COMMENT '核销员ID',
  `is_delete` tinyint(1) DEFAULT '0' COMMENT '1删除',
  `is_cancle` tinyint(1) DEFAULT '0' COMMENT '1取消订单',
  `is_remind` tinyint(1) DEFAULT '0' COMMENT '1订单提醒',
  `detail` varchar(150) DEFAULT NULL COMMENT '留言',
  `is_sent` tinyint(1) DEFAULT '0' COMMENT '申请退款前原订单状态',
  `updata_time` varchar(10) DEFAULT NULL COMMENT '自提核销时间',
  `oprice` decimal(10,2) DEFAULT '0.00' COMMENT '原订单价格',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商城订单' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `zy_shopp_order_good`
--

CREATE TABLE IF NOT EXISTS `zy_shopp_order_good` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL COMMENT '订单id',
  `goodid` int(11) NOT NULL COMMENT '商品id',
  `num` int(11) NOT NULL DEFAULT '0' COMMENT '购买数量',
  `optionid` int(11) NOT NULL DEFAULT '0' COMMENT '商品规格',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商城订单商品' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `zy_ucenter_member`
--

CREATE TABLE IF NOT EXISTS `zy_ucenter_member` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` char(16) NOT NULL COMMENT '用户名',
  `password` char(32) NOT NULL COMMENT '密码',
  `mobile` char(15) NOT NULL DEFAULT '' COMMENT '用户手机',
  `login` int(10) DEFAULT '0',
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `reg_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '注册IP',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `last_login_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '最后登录IP',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) DEFAULT '0' COMMENT '用户状态',
  `email` char(32) NOT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `username` (`username`),
  KEY `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户表' AUTO_INCREMENT=18 ;

--
-- 转存表中的数据 `zy_ucenter_member`
--

INSERT INTO `zy_ucenter_member` (`uid`, `username`, `password`, `mobile`, `login`, `reg_time`, `reg_ip`, `last_login_time`, `last_login_ip`, `update_time`, `status`, `email`) VALUES
(1, 'admin', '00f2976e8f2328657522a4dbab635952', '', 7, 1532333652, 2130706433, 1600650478, 2130706433, 1600229093, 1, '');

-- --------------------------------------------------------

--
-- 表的结构 `zy_user`
--

CREATE TABLE IF NOT EXISTS `zy_user` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `nickname` char(16) NOT NULL DEFAULT '' COMMENT '昵称',
  `mobile` varchar(11) NOT NULL DEFAULT '' COMMENT '手机号',
  `sex` varchar(10) NOT NULL COMMENT '性别',
  `realname` varchar(100) NOT NULL COMMENT '真实姓名',
  `headimgurl` varchar(300) NOT NULL COMMENT '头像',
  `birthday` char(10) NOT NULL COMMENT '生日',
  `totalprice` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '用户余额',
  `totalscore` mediumint(8) NOT NULL DEFAULT '0' COMMENT '用户积分',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `openid` varchar(255) NOT NULL DEFAULT '',
  `istype` varchar(25) NOT NULL DEFAULT '0' COMMENT '0普通会员1游玩  2 美食  3 酒店  4套票  5 活动  6商城 核销员',
  `city` varchar(150) DEFAULT NULL COMMENT '地区',
  `level` tinyint(1) NOT NULL DEFAULT '0' COMMENT '会员等级',
  `status` tinyint(1) NOT NULL,
  `reserve_time` varchar(13) NOT NULL COMMENT '预定类订单上次查看时间',
  `message_time` varchar(13) NOT NULL COMMENT '消息上次查看时间',
  `activity_time` varchar(13) NOT NULL COMMENT '景区活动订单上次查看时间',
  `is_remind` tinyint(1) DEFAULT '0' COMMENT '1订单提醒',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
