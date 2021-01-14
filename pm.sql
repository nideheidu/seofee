-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: 2019-12-10 18:06:58
-- 服务器版本： 5.6.35
-- PHP Version: 5.5.38

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `seo1210ys`
--

-- --------------------------------------------------------

--
-- 表的结构 `seo_admin_token`
--

CREATE TABLE `seo_admin_token` (
  `uid` int(10) UNSIGNED NOT NULL,
  `access_token` varchar(500) NOT NULL,
  `last_login_ip` varchar(32) DEFAULT NULL,
  `last_login_time` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `seo_admin_token`
--

INSERT INTO `seo_admin_token` (`uid`, `access_token`, `last_login_ip`, `last_login_time`) VALUES
(5, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJ3d3cubG9uZ2h1MzIxLmNvbSIsImF1ZCI6NSwiaWF0IjoxNTc1OTY5OTk5LCJuYmYiOjE1NzU5Njk5OTksImRhdGEiOnsidXNlcl9pZCI6NSwidXNlcm5hbWUiOiJ0aGluayIsInVzZXJfaXAiOiIxODU3ODkyODA2IiwibWVtYmVyX3R5cGUiOjN9LCJzY29wZXMiOiJyb2xlX2FjY2VzcyIsImV4cCI6MTU3NTk3NzE5OX0.A0DzTMQjQ2HzMpQJ3vCelWbKu-y949Nt2LKc9iikP7Q', '1857892806', 1575969999);

-- --------------------------------------------------------

--
-- 表的结构 `seo_admin_user`
--

CREATE TABLE `seo_admin_user` (
  `uid` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL DEFAULT '',
  `password` char(64) NOT NULL DEFAULT '',
  `mobile` char(11) NOT NULL DEFAULT '' COMMENT '手机号',
  `contacts` varchar(20) NOT NULL DEFAULT '' COMMENT '联系人',
  `company_name` varchar(50) NOT NULL DEFAULT '' COMMENT '公司名',
  `email` varchar(255) NOT NULL DEFAULT '',
  `qq_number` varchar(15) NOT NULL DEFAULT '',
  `create_time` int(10) DEFAULT NULL,
  `update_time` int(10) DEFAULT NULL,
  `lev` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1：普通会员 2：XXX。。。。。。',
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '余额',
  `role` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0普通客户 1：管理员',
  `rz_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '认证类型 0未认证，1个人 2企业',
  `login_error_count` tinyint(1) DEFAULT NULL,
  `login_error_count_time` int(10) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `seo_admin_user`
--

INSERT INTO `seo_admin_user` (`uid`, `username`, `password`, `mobile`, `contacts`, `company_name`, `email`, `qq_number`, `create_time`, `update_time`, `lev`, `balance`, `role`, `rz_type`, `login_error_count`, `login_error_count_time`, `status`) VALUES
(5, 'think', '$2y$10$EdydyP2p2kQs6Iqbx96.dOBTni/HOiZckelAU0YSQEAPp87qHHjwS', '15760633369', '', '', '', '', 1539738515, NULL, 1, '0.00', 1, 0, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- 表的结构 `seo_authentication_enterprise`
--

CREATE TABLE `seo_authentication_enterprise` (
  `id` int(10) NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `fr_name` varchar(20) DEFAULT '' COMMENT '法人',
  `fr_crad` char(18) NOT NULL DEFAULT '' COMMENT '法人身份证',
  `enterprise_name` varchar(255) NOT NULL DEFAULT '' COMMENT '企业名',
  `business_license_number` varchar(50) NOT NULL DEFAULT '' COMMENT '营业执照(编号)',
  `business_license_img` varchar(255) NOT NULL DEFAULT '' COMMENT '营业执照照片',
  `creat_time` int(10) DEFAULT NULL,
  `update_time` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='客户认证-企业认证';

-- --------------------------------------------------------

--
-- 表的结构 `seo_authentication_personal`
--

CREATE TABLE `seo_authentication_personal` (
  `id` int(10) NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `f_name` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '姓名',
  `id_card` varchar(25) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '身份证号',
  `phone` char(11) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '手机号',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT ' 认证状态',
  `update_time` int(10) DEFAULT NULL COMMENT '修改时间',
  `create_time` int(10) DEFAULT NULL COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='客户个人认证表';

-- --------------------------------------------------------

--
-- 表的结构 `seo_command`
--

CREATE TABLE `seo_command` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '订单ID',
  `name` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `sort` int(8) DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `create_time` int(10) NOT NULL,
  `delete_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='命令表';

--
-- 转存表中的数据 `seo_command`
--

INSERT INTO `seo_command` (`id`, `name`, `content`, `sort`, `status`, `create_time`, `delete_time`) VALUES
(6, '输入文本', '[{\"key\":\"\\u547d\\u4ee4\",\"value\":\"\\u8f93\\u5165\\u6587\\u672c\",\"sort\":\"0\",\"isdefault\":1},{\"key\":\"\\u5185\\u5bb9\",\"value\":\"a\",\"sort\":\"1\"},{\"key\":\"\\u8f93\\u5165\\u95f4\\u9694\",\"value\":\"20\",\"sort\":\"2\"},{\"key\":\"id\",\"value\":\"kw\",\"sort\":\"3\"},{\"key\":\"tagName\",\"value\":\"INPUT\",\"sort\":\"4\"}]', 0, 0, 1564987338, NULL),
(8, '打开网址', '[{\"key\":\"\\u547d\\u4ee4\",\"value\":\"\\u6253\\u5f00\\u7f51\\u5740\",\"sort\":\"0\",\"isdefault\":1},{\"key\":\"url\",\"value\":\"http:\\/\\/baidu.com\",\"sort\":\"1\"}]', 0, 0, 1564989403, NULL),
(9, '回车', '[{\"key\":\"\\u547d\\u4ee4\",\"value\":\"\\u56de\\u8f66\",\"sort\":\"0\",\"isdefault\":1}]', 0, 0, 1564989414, NULL),
(10, '随机延迟', '[{\"key\":\"\\u547d\\u4ee4\",\"value\":\"\\u968f\\u673a\\u5ef6\\u8fdf\",\"sort\":\"0\",\"isdefault\":1},{\"key\":\"\\u5f00\\u59cb\",\"value\":\"\",\"sort\":\"1\"},{\"key\":\"\\u7ed3\\u675f\",\"value\":\"\",\"sort\":\"2\"},{\"key\":\"\\u5355\\u4f4d\",\"value\":\"\",\"sort\":\"3\"}]', 0, 0, 1564989429, NULL),
(11, '滚动', '[{\"key\":\"\\u547d\\u4ee4\",\"value\":\"\\u6eda\\u52a8\",\"sort\":\"0\",\"isdefault\":1}]', 0, 0, 1564989437, NULL),
(12, '前进', '[{\"key\":\"\\u547d\\u4ee4\",\"value\":\"\\u524d\\u8fdb\",\"sort\":\"0\",\"isdefault\":1}]', 0, 0, 1564989441, NULL),
(13, '后退', '[{\"key\":\"\\u547d\\u4ee4\",\"value\":\"\\u540e\\u9000\",\"sort\":\"0\",\"isdefault\":1}]', 0, 0, 1564989444, NULL),
(14, '随机点击', '[{\"key\":\"\\u547d\\u4ee4\",\"value\":\"\\u968f\\u673a\\u70b9\\u51fb\",\"sort\":\"0\",\"isdefault\":1},{\"key\":\"\\u6b21\\u6570\",\"value\":\"2\",\"sort\":\"1\"},{\"key\":\"\\u95f4\\u9694\\u65f6\\u95f4\",\"value\":\"100\",\"sort\":\"2\"}]', 0, 0, 1564989459, NULL),
(15, '随机移动', '[{\"key\":\"\\u547d\\u4ee4\",\"value\":\"\\u968f\\u673a\\u79fb\\u52a8\",\"sort\":\"0\",\"isdefault\":1},{\"key\":\"\\u6b21\\u6570\",\"value\":\"2\",\"sort\":\"1\"},{\"key\":\"\\u95f4\\u9694\\u65f6\\u95f4\",\"value\":\"500\",\"sort\":\"2\"}]', 0, 0, 1564989475, NULL),
(16, '深入点击', '[{\"key\":\"\\u547d\\u4ee4\",\"value\":\"\\u6df1\\u5165\\u70b9\\u51fb\",\"sort\":\"0\",\"isdefault\":1},{\"key\":\"\\u6df1\\u5165\\u6b21\\u6570\",\"value\":\"2\",\"sort\":\"1\"},{\"key\":\"\\u95f4\\u9694\\u65f6\\u95f4\",\"value\":\"100\",\"sort\":\"2\"}]', 0, 0, 1564989488, NULL),
(17, '设置ua', '[{\"key\":\"\\u547d\\u4ee4\",\"value\":\"\\u8bbe\\u7f6eua\",\"sort\":\"0\",\"isdefault\":1}]', 0, 0, 1564989497, NULL),
(18, '换ip', '[{\"key\":\"\\u547d\\u4ee4\",\"value\":\"\\u6362ip\",\"sort\":\"0\",\"isdefault\":1},{\"key\":\"\\u76d1\\u63a7\\u65f6\\u95f4\",\"value\":\"120\",\"sort\":\"1\"}]', 0, 0, 1564989512, NULL),
(19, '设置分辨率', '[{\"key\":\"\\u547d\\u4ee4\",\"value\":\"\\u8bbe\\u7f6e\\u5206\\u8fa8\\u7387\",\"sort\":\"0\",\"isdefault\":1}]', 0, 0, 1564989516, NULL),
(20, '删除CK', '[{\"key\":\"\\u547d\\u4ee4\",\"value\":\"\\u5220\\u9664CK\",\"sort\":\"0\",\"isdefault\":1}]', 0, 0, 1564989522, NULL),
(21, '创建浏览器', '[{\"key\":\"\\u547d\\u4ee4\",\"value\":\"\\u521b\\u5efa\\u6d4f\\u89c8\\u5668\",\"sort\":\"0\",\"isdefault\":1}]', 0, 0, 1564989527, NULL),
(22, '修改cpu', '[{\"key\":\"\\u547d\\u4ee4\",\"value\":\"\\u4fee\\u6539cpu\",\"sort\":\"0\",\"isdefault\":1}]', 0, 0, 1564989531, NULL),
(23, '修改计算机信息', '[{\"key\":\"\\u547d\\u4ee4\",\"value\":\"\\u4fee\\u6539\\u8ba1\\u7b97\\u673a\\u4fe1\\u606f\",\"sort\":\"0\",\"isdefault\":1}]', 0, 0, 1564989535, NULL),
(24, '修改mac', '[{\"key\":\"\\u547d\\u4ee4\",\"value\":\"\\u4fee\\u6539mac\",\"sort\":\"0\",\"isdefault\":1}]', 0, 0, 1564989540, NULL),
(25, '修改系统型号', '[{\"key\":\"\\u547d\\u4ee4\",\"value\":\"\\u4fee\\u6539\\u7cfb\\u7edf\\u578b\\u53f7\",\"sort\":\"0\",\"isdefault\":1}]', 0, 0, 1564989544, NULL),
(26, '修改系统型号', '[{\"key\":\"\\u547d\\u4ee4\",\"value\":\"\\u4fee\\u6539\\u7cfb\\u7edf\\u578b\\u53f7\",\"sort\":\"0\",\"isdefault\":1},{\"key\":\"\\u7c7b\\u578b\",\"value\":\"1\",\"sort\":\"1\"},{\"key\":\"tagName\",\"value\":\"A\",\"sort\":\"2\"},{\"key\":\"innerText\",\"value\":\"5173\\u6e38\\u620f\\u5e01\\u5546\\u57ce\",\"sort\":\"3\"}]', 2, 0, 1564989576, NULL),
(27, '下一页', '[{\"key\":\"\\u547d\\u4ee4\",\"value\":\"\\u4e0b\\u4e00\\u9875\",\"sort\":\"0\",\"isdefault\":1},{\"key\":\"\\u6700\\u5927\\u9875\\u6570\",\"value\":\"\",\"sort\":\"1\"},{\"key\":\"\\u7c7b\\u578b\",\"value\":\"4\",\"sort\":\"2\"},{\"key\":\"tagName\",\"value\":\"A\",\"sort\":\"3\"},{\"key\":\"innerText\",\"value\":\"5173\\u6e38\\u620f\\u5e01\\u5546\\u57ce\",\"sort\":\"4\"}]', 1, 0, 1564989602, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `seo_command_item`
--

CREATE TABLE `seo_command_item` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '订单ID',
  `cid` int(11) NOT NULL,
  `key` varchar(100) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `sort` int(8) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL,
  `delete_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='命令内容表';

-- --------------------------------------------------------

--
-- 表的结构 `seo_command_template`
--

CREATE TABLE `seo_command_template` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '订单ID',
  `name` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `desction` text,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL,
  `delete_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='单条命令模板';

--
-- 转存表中的数据 `seo_command_template`
--

INSERT INTO `seo_command_template` (`id`, `name`, `content`, `desction`, `status`, `create_time`, `delete_time`) VALUES
(8, '标准动作', '\"[[{\\\"key\\\":\\\"\\u547d\\u4ee4\\\",\\\"value\\\":\\\"\\u6253\\u5f00\\u7f51\\u5740\\\",\\\"sort\\\":\\\"0\\\",\\\"isdefault\\\":1},{\\\"key\\\":\\\"url\\\",\\\"value\\\":\\\"http:\\/\\/baidu.com\\\",\\\"sort\\\":\\\"1\\\"}],[{\\\"key\\\":\\\"\\u547d\\u4ee4\\\",\\\"value\\\":\\\"\\u8f93\\u5165\\u6587\\u672c\\\",\\\"sort\\\":\\\"0\\\",\\\"isdefault\\\":1},{\\\"key\\\":\\\"\\u5185\\u5bb9\\\",\\\"value\\\":\\\"a\\\",\\\"sort\\\":\\\"1\\\"},{\\\"key\\\":\\\"\\u8f93\\u5165\\u95f4\\u9694\\\",\\\"value\\\":\\\"20\\\",\\\"sort\\\":\\\"2\\\"},{\\\"key\\\":\\\"id\\\",\\\"value\\\":\\\"kw\\\",\\\"sort\\\":\\\"3\\\"},{\\\"key\\\":\\\"tagName\\\",\\\"value\\\":\\\"INPUT\\\",\\\"sort\\\":\\\"4\\\"}],[{\\\"key\\\":\\\"\\u547d\\u4ee4\\\",\\\"value\\\":\\\"\\u56de\\u8f66\\\",\\\"sort\\\":\\\"0\\\",\\\"isdefault\\\":1}],[{\\\"key\\\":\\\"\\u547d\\u4ee4\\\",\\\"value\\\":\\\"\\u968f\\u673a\\u5ef6\\u8fdf\\\",\\\"sort\\\":\\\"0\\\",\\\"isdefault\\\":1},{\\\"key\\\":\\\"\\u5f00\\u59cb\\\",\\\"value\\\":\\\"12\\\",\\\"sort\\\":\\\"1\\\"},{\\\"key\\\":\\\"\\u7ed3\\u675f\\\",\\\"value\\\":\\\"20\\\",\\\"sort\\\":\\\"2\\\"},{\\\"key\\\":\\\"\\u5355\\u4f4d\\\",\\\"value\\\":\\\"s\\\",\\\"sort\\\":\\\"3\\\"}],[{\\\"key\\\":\\\"\\u547d\\u4ee4\\\",\\\"value\\\":\\\"\\u524d\\u8fdb\\\",\\\"sort\\\":\\\"0\\\",\\\"isdefault\\\":1}],[{\\\"key\\\":\\\"\\u547d\\u4ee4\\\",\\\"value\\\":\\\"\\u540e\\u9000\\\",\\\"sort\\\":\\\"0\\\",\\\"isdefault\\\":1}],[{\\\"key\\\":\\\"\\u547d\\u4ee4\\\",\\\"value\\\":\\\"\\u968f\\u673a\\u70b9\\u51fb\\\",\\\"sort\\\":\\\"0\\\",\\\"isdefault\\\":1},{\\\"key\\\":\\\"\\u6b21\\u6570\\\",\\\"value\\\":\\\"2\\\",\\\"sort\\\":\\\"1\\\"},{\\\"key\\\":\\\"\\u95f4\\u9694\\u65f6\\u95f4\\\",\\\"value\\\":\\\"100\\\",\\\"sort\\\":\\\"2\\\"}],[{\\\"key\\\":\\\"\\u547d\\u4ee4\\\",\\\"value\\\":\\\"\\u968f\\u673a\\u70b9\\u51fb\\\",\\\"sort\\\":\\\"0\\\",\\\"isdefault\\\":1},{\\\"key\\\":\\\"\\u6b21\\u6570\\\",\\\"value\\\":\\\"2\\\",\\\"sort\\\":\\\"1\\\"},{\\\"key\\\":\\\"\\u95f4\\u9694\\u65f6\\u95f4\\\",\\\"value\\\":\\\"100\\\",\\\"sort\\\":\\\"2\\\"}],[{\\\"key\\\":\\\"\\u547d\\u4ee4\\\",\\\"value\\\":\\\"\\u968f\\u673a\\u79fb\\u52a8\\\",\\\"sort\\\":\\\"0\\\",\\\"isdefault\\\":1},{\\\"key\\\":\\\"\\u6b21\\u6570\\\",\\\"value\\\":\\\"2\\\",\\\"sort\\\":\\\"1\\\"},{\\\"key\\\":\\\"\\u95f4\\u9694\\u65f6\\u95f4\\\",\\\"value\\\":\\\"500\\\",\\\"sort\\\":\\\"2\\\"}],[{\\\"key\\\":\\\"\\u547d\\u4ee4\\\",\\\"value\\\":\\\"\\u6df1\\u5165\\u70b9\\u51fb\\\",\\\"sort\\\":\\\"0\\\",\\\"isdefault\\\":1},{\\\"key\\\":\\\"\\u6df1\\u5165\\u6b21\\u6570\\\",\\\"value\\\":\\\"2\\\",\\\"sort\\\":\\\"1\\\"},{\\\"key\\\":\\\"\\u95f4\\u9694\\u65f6\\u95f4\\\",\\\"value\\\":\\\"100\\\",\\\"sort\\\":\\\"2\\\"}],[{\\\"key\\\":\\\"\\u547d\\u4ee4\\\",\\\"value\\\":\\\"\\u8bbe\\u7f6eua\\\",\\\"sort\\\":\\\"0\\\",\\\"isdefault\\\":1}],[{\\\"key\\\":\\\"\\u547d\\u4ee4\\\",\\\"value\\\":\\\"\\u6362ip\\\",\\\"sort\\\":\\\"0\\\",\\\"isdefault\\\":1},{\\\"key\\\":\\\"\\u76d1\\u63a7\\u65f6\\u95f4\\\",\\\"value\\\":\\\"120\\\",\\\"sort\\\":\\\"1\\\"}],[{\\\"key\\\":\\\"\\u547d\\u4ee4\\\",\\\"value\\\":\\\"\\u8bbe\\u7f6e\\u5206\\u8fa8\\u7387\\\",\\\"sort\\\":\\\"0\\\",\\\"isdefault\\\":1}],[{\\\"key\\\":\\\"\\u547d\\u4ee4\\\",\\\"value\\\":\\\"\\u5220\\u9664CK\\\",\\\"sort\\\":\\\"0\\\",\\\"isdefault\\\":1}],[{\\\"key\\\":\\\"\\u547d\\u4ee4\\\",\\\"value\\\":\\\"\\u4fee\\u6539\\u8ba1\\u7b97\\u673a\\u4fe1\\u606f\\\",\\\"sort\\\":\\\"0\\\",\\\"isdefault\\\":1}],[{\\\"key\\\":\\\"\\u547d\\u4ee4\\\",\\\"value\\\":\\\"\\u4fee\\u6539\\u7cfb\\u7edf\\u578b\\u53f7\\\",\\\"sort\\\":\\\"0\\\",\\\"isdefault\\\":1}],[{\\\"key\\\":\\\"\\u547d\\u4ee4\\\",\\\"value\\\":\\\"\\u4fee\\u6539mac\\\",\\\"sort\\\":\\\"0\\\",\\\"isdefault\\\":1}],[{\\\"key\\\":\\\"\\u547d\\u4ee4\\\",\\\"value\\\":\\\"\\u4fee\\u6539\\u7cfb\\u7edf\\u578b\\u53f7\\\",\\\"sort\\\":\\\"0\\\",\\\"isdefault\\\":1},{\\\"key\\\":\\\"\\u7c7b\\u578b\\\",\\\"value\\\":\\\"1\\\",\\\"sort\\\":\\\"1\\\"},{\\\"key\\\":\\\"tagName\\\",\\\"value\\\":\\\"A\\\",\\\"sort\\\":\\\"2\\\"},{\\\"key\\\":\\\"innerText\\\",\\\"value\\\":\\\"5173\\u6e38\\u620f\\u5e01\\u5546\\u57ce\\\",\\\"sort\\\":\\\"3\\\"}],[{\\\"key\\\":\\\"\\u547d\\u4ee4\\\",\\\"value\\\":\\\"\\u4e0b\\u4e00\\u9875\\\",\\\"sort\\\":\\\"0\\\",\\\"isdefault\\\":1},{\\\"key\\\":\\\"\\u6700\\u5927\\u9875\\u6570\\\",\\\"value\\\":\\\"15\\\",\\\"sort\\\":\\\"1\\\"},{\\\"key\\\":\\\"\\u7c7b\\u578b\\\",\\\"value\\\":\\\"4\\\",\\\"sort\\\":\\\"2\\\"},{\\\"key\\\":\\\"tagName\\\",\\\"value\\\":\\\"A\\\",\\\"sort\\\":\\\"3\\\"},{\\\"key\\\":\\\"innerText\\\",\\\"value\\\":\\\"{$keywords}\\\",\\\"sort\\\":\\\"4\\\"}],[{\\\"key\\\":\\\"\\u547d\\u4ee4\\\",\\\"value\\\":\\\"\\u4e0b\\u4e00\\u9875\\\",\\\"sort\\\":\\\"0\\\",\\\"isdefault\\\":1},{\\\"key\\\":\\\"\\u6700\\u5927\\u9875\\u6570\\\",\\\"value\\\":\\\"\\\",\\\"sort\\\":\\\"1\\\"},{\\\"key\\\":\\\"\\u7c7b\\u578b\\\",\\\"value\\\":\\\"4\\\",\\\"sort\\\":\\\"2\\\"},{\\\"key\\\":\\\"tagName\\\",\\\"value\\\":\\\"A\\\",\\\"sort\\\":\\\"3\\\"},{\\\"key\\\":\\\"innerText\\\",\\\"value\\\":\\\"5173\\u6e38\\u620f\\u5e01\\u5546\\u57ce\\\",\\\"sort\\\":\\\"4\\\"}],[{\\\"key\\\":\\\"\\u547d\\u4ee4\\\",\\\"value\\\":\\\"\\u8f93\\u5165\\u6587\\u672c\\\",\\\"sort\\\":\\\"0\\\",\\\"isdefault\\\":1},{\\\"key\\\":\\\"\\u5185\\u5bb9\\\",\\\"value\\\":\\\"{$xiongzhang}\\\",\\\"sort\\\":\\\"1\\\"},{\\\"key\\\":\\\"\\u8f93\\u5165\\u95f4\\u9694\\\",\\\"value\\\":\\\"20\\\",\\\"sort\\\":\\\"2\\\"},{\\\"key\\\":\\\"id\\\",\\\"value\\\":\\\"kw\\\",\\\"sort\\\":\\\"3\\\"},{\\\"key\\\":\\\"tagName\\\",\\\"value\\\":\\\"INPUT\\\",\\\"sort\\\":\\\"4\\\"}],[{\\\"key\\\":\\\"\\u547d\\u4ee4\\\",\\\"value\\\":\\\"\\u6253\\u5f00\\u7f51\\u5740\\\",\\\"sort\\\":\\\"0\\\",\\\"isdefault\\\":1},{\\\"key\\\":\\\"url\\\",\\\"value\\\":\\\"{$url}\\\",\\\"sort\\\":\\\"1\\\"}]]\"', NULL, 0, 1565149480, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `seo_command_template_itme`
--

CREATE TABLE `seo_command_template_itme` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '订单ID',
  `cid` int(11) NOT NULL COMMENT '对应命令的id',
  `tid` int(11) NOT NULL COMMENT '对应模板的id',
  `sort` int(8) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL,
  `delete_time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='单条命令模板';

-- --------------------------------------------------------

--
-- 表的结构 `seo_customer`
--

CREATE TABLE `seo_customer` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL DEFAULT '' COMMENT '登录名',
  `password` varchar(128) NOT NULL DEFAULT '' COMMENT '密码',
  `email` varchar(255) NOT NULL DEFAULT '',
  `qq_number` varchar(20) NOT NULL DEFAULT '',
  `member_level` tinyint(3) NOT NULL DEFAULT '1' COMMENT '会员等级  1： 普通会员 2：高级会员',
  `contacts` varchar(20) NOT NULL DEFAULT '' COMMENT '联系人',
  `company_name` varchar(100) NOT NULL DEFAULT '' COMMENT '公司名',
  `rz_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '认证类型 0未认证，1个人 2企业',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  `create_time` int(10) DEFAULT NULL,
  `isagent` int(2) NOT NULL DEFAULT '0' COMMENT '是否是代理商',
  `upid` int(9) NOT NULL DEFAULT '0' COMMENT '上级代理商id',
  `delete_time` int(10) DEFAULT NULL,
  `phone` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `seo_customer_account`
--

CREATE TABLE `seo_customer_account` (
  `id` int(10) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `total_sum` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '总金额',
  `total_consumption` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '总消费',
  `update_time` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='客户账单表';

-- --------------------------------------------------------

--
-- 表的结构 `seo_customer_info`
--

CREATE TABLE `seo_customer_info` (
  `id` int(11) UNSIGNED NOT NULL,
  `customer_id` int(11) UNSIGNED NOT NULL COMMENT '客户ID',
  `token` text NOT NULL,
  `last_login_time` int(10) DEFAULT NULL COMMENT '登录时间',
  `last_login_ip` varchar(20) DEFAULT NULL,
  `login_error_count` tinyint(3) DEFAULT '0' COMMENT '密码错误次数',
  `login_error_count_time` int(10) DEFAULT NULL COMMENT '错误登录时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `seo_fee`
--

CREATE TABLE `seo_fee` (
  `id` int(11) UNSIGNED NOT NULL,
  `minnum` int(65) NOT NULL COMMENT '最低',
  `maxnum` int(65) NOT NULL DEFAULT '0' COMMENT '最高',
  `fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '扣费',
  `fee2` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '次页指数扣费',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `create_time` int(10) DEFAULT NULL,
  `agent_id` int(9) DEFAULT NULL COMMENT '代理商id',
  `group_id` int(9) NOT NULL DEFAULT '0' COMMENT '会员组id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员等级配置表';

-- --------------------------------------------------------

--
-- 表的结构 `seo_keywords`
--

CREATE TABLE `seo_keywords` (
  `id` int(11) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL COMMENT '用户ID',
  `web_url` varchar(100) NOT NULL COMMENT '网站地址',
  `task_number` varchar(20) NOT NULL DEFAULT '' COMMENT '任务编号',
  `keywords` varchar(100) NOT NULL DEFAULT '' COMMENT '关键字',
  `create_time` int(11) DEFAULT NULL,
  `search_ngines` tinyint(1) NOT NULL DEFAULT '1' COMMENT ' 搜索引擎 1：百度PC 2：百度移动 3360PC 4 360移动 5搜狗pc 6 搜狗移动',
  `current_ranking` int(11) NOT NULL DEFAULT '0' COMMENT '当前排名',
  `status` tinyint(1) DEFAULT '0' COMMENT '0:提交 1：已提交 2进行中 3已完成申请完成 4 确认完成 5审核未通过',
  `agent_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '代理审核状 1：已提交 2审核通过 5审核未通过',
  `cycle` int(3) NOT NULL DEFAULT '90' COMMENT '合作周期',
  `billing_mode` tinyint(1) NOT NULL DEFAULT '1' COMMENT '计费方式 1按天计费（前10名）',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '优化类型 1站内优化  2站外优化【这个字段没用】',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '达标后每天扣费价格 这里是后台配置的每个会员组不一样',
  `agent_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '平台扣代理商费用',
  `testing` tinyint(1) NOT NULL DEFAULT '0' COMMENT '检测状态',
  `delete_time` int(10) DEFAULT NULL,
  `complete_time` int(11) UNSIGNED DEFAULT NULL COMMENT '结束时间',
  `web_id` int(5) DEFAULT NULL,
  `rank_time` int(11) DEFAULT NULL COMMENT '最近一次的排名时间',
  `original_rank` int(11) NOT NULL DEFAULT '0' COMMENT '初始排名',
  `standard` int(20) DEFAULT '0' COMMENT '达标天数',
  `start_time` int(20) NOT NULL DEFAULT '0' COMMENT '开始时间',
  `fee` decimal(10,2) DEFAULT '0.00',
  `BaiduPc` int(20) DEFAULT NULL,
  `BaiduMobile` int(20) DEFAULT NULL,
  `xiongzhang` varchar(64) NOT NULL,
  `is_submit` int(1) NOT NULL DEFAULT '0' COMMENT '是否已提交到第三方平台',
  `yun_id` varchar(20) NOT NULL COMMENT '三方平台id',
  `yun_time` int(11) NOT NULL COMMENT '三方平台更新时间',
  `yun_price` float(10,2) NOT NULL COMMENT '云平台价格'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='关键字表';

-- --------------------------------------------------------

--
-- 表的结构 `seo_keywords_rank`
--

CREATE TABLE `seo_keywords_rank` (
  `id` int(11) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL COMMENT '用户ID',
  `web_url` varchar(100) NOT NULL COMMENT '网站地址',
  `task_number` varchar(20) NOT NULL DEFAULT '' COMMENT '任务编号',
  `keywords` varchar(100) NOT NULL DEFAULT '' COMMENT '关键字',
  `create_time` int(11) DEFAULT NULL,
  `search_ngines` tinyint(1) NOT NULL DEFAULT '1' COMMENT ' 搜索引擎 1：百度PC 2：百度移动',
  `current_ranking` int(11) NOT NULL DEFAULT '0' COMMENT '当前排名',
  `status` tinyint(1) DEFAULT '0' COMMENT '0:提交 1：已提交 2进行中 3已完成',
  `cycle` int(3) NOT NULL DEFAULT '90' COMMENT '合作周期',
  `billing_mode` tinyint(1) NOT NULL DEFAULT '1' COMMENT '计费方式 1按天计费（前10名）',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '优化类型 1站内优化  2站外优化【这个字段没用】',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '达标后每天扣费价格 这里是后台配置的每个会员组不一样',
  `testing` tinyint(1) NOT NULL DEFAULT '0' COMMENT '检测状态',
  `delete_time` int(10) DEFAULT NULL,
  `rank_time` datetime DEFAULT NULL COMMENT '最近一次的排名时间',
  `complete_time` int(11) DEFAULT NULL COMMENT '结束时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='关键字排名优化表';

-- --------------------------------------------------------

--
-- 表的结构 `seo_mingxi`
--

CREATE TABLE `seo_mingxi` (
  `id` int(11) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `kid` int(10) NOT NULL DEFAULT '0' COMMENT '关键词id',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '交易类型 1：关键字扣费 2系统充值退款,3代理商扣费',
  `change_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '变动类型 1为增加 2减少',
  `free` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '变动金额',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '优化url',
  `keywords` varchar(100) NOT NULL DEFAULT '' COMMENT '优化关键字',
  `remarks` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `create_time` int(10) DEFAULT NULL COMMENT '变动时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户资金明细表';

-- --------------------------------------------------------

--
-- 表的结构 `seo_notice`
--

CREATE TABLE `seo_notice` (
  `id` int(11) UNSIGNED NOT NULL,
  `group` int(11) NOT NULL DEFAULT '1' COMMENT '公告权限，1全部 2代理商 3普通会员',
  `title` varchar(100) DEFAULT NULL COMMENT '标题',
  `content` mediumtext COMMENT '内容',
  `is_display` int(11) NOT NULL DEFAULT '1' COMMENT '是否显示',
  `create_time` int(11) DEFAULT NULL,
  `delete_time` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='关键字排名优化表';

-- --------------------------------------------------------

--
-- 表的结构 `seo_notice_log`
--

CREATE TABLE `seo_notice_log` (
  `id` int(11) UNSIGNED NOT NULL,
  `create_time` int(11) DEFAULT NULL,
  `delete_time` int(10) DEFAULT NULL,
  `nid` int(10) NOT NULL COMMENT '公告id',
  `uid` int(10) NOT NULL COMMENT '用户id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='关键字排名优化表';

-- --------------------------------------------------------

--
-- 表的结构 `seo_order`
--

CREATE TABLE `seo_order` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '订单ID',
  `order_number` varchar(18) NOT NULL DEFAULT '' COMMENT '订单编号',
  `uid` int(10) UNSIGNED NOT NULL,
  `web_id` int(10) UNSIGNED NOT NULL COMMENT '网站ID',
  `kid` int(11) UNSIGNED NOT NULL COMMENT '关键字ID',
  `original_rank` int(11) NOT NULL DEFAULT '0' COMMENT '原始排名',
  `current_ranking` int(11) NOT NULL DEFAULT '0' COMMENT '当前排名',
  `standard` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '达标天数',
  `create_time` int(10) DEFAULT '0',
  `update_time` int(10) DEFAULT '0' COMMENT '更新时间 ',
  `shenhe_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '审核状态',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '优化状态 1：优化中 2:优化暂停 0未优化',
  `shenhe_time` int(10) DEFAULT NULL COMMENT '审核时间',
  `cycle` int(10) NOT NULL DEFAULT '90',
  `type` int(4) DEFAULT NULL COMMENT '类型 1 seo 2关键字'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `seo_rank_log`
--

CREATE TABLE `seo_rank_log` (
  `id` int(11) UNSIGNED NOT NULL,
  `taskid` int(10) UNSIGNED NOT NULL COMMENT '关键字id',
  `result` varchar(16) NOT NULL DEFAULT '0' COMMENT '返回的值',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '搜索引擎',
  `action` int(1) NOT NULL DEFAULT '1' COMMENT '1获取排名 2获取百度字数3获取id',
  `key` varchar(32) NOT NULL COMMENT 'api key',
  `message` varchar(128) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  `delete_time` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='关键字排名优化表';

-- --------------------------------------------------------

--
-- 表的结构 `seo_system_config`
--

CREATE TABLE `seo_system_config` (
  `id` int(10) UNSIGNED NOT NULL,
  `system` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否为系统配置(1是，0否)',
  `group` varchar(20) NOT NULL DEFAULT 'base' COMMENT '分组',
  `title` varchar(20) NOT NULL COMMENT '配置标题',
  `name` varchar(50) NOT NULL COMMENT '配置名称，由英文字母和下划线组成',
  `value` text NOT NULL COMMENT '配置值',
  `type` varchar(20) NOT NULL DEFAULT 'input' COMMENT '配置类型()',
  `options` text COMMENT '配置项(选项名:选项值)',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '文件上传接口',
  `tips` varchar(255) NOT NULL COMMENT '配置提示',
  `sort` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) UNSIGNED NOT NULL COMMENT '状态',
  `ctime` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `mtime` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `agent_id` int(9) NOT NULL DEFAULT '0' COMMENT '所属代理，999999 为系统'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='[系统] 系统配置';

--
-- 转存表中的数据 `seo_system_config`
--

INSERT INTO `seo_system_config` (`id`, `system`, `group`, `title`, `name`, `value`, `type`, `options`, `url`, `tips`, `sort`, `status`, `ctime`, `mtime`, `agent_id`) VALUES
(13, 1, 'base', '网站域名', 'site_domain', 'http://seo.17s.cn', 'input', '', '', '', 1, 1, 1492140215, 1492140215, 999999),
(41, 1, 'base', '网站标题', 'site_title', 'SEO计费系统', 'input', '', '', '网站标题是体现一个网站的主旨，要做到主题突出、标题简洁、连贯等特点，建议不超过28个字', 2, 1, 1492502354, 1494695131, 999999),
(42, 1, 'base', '网站关键词', 'site_keywords', 'SEO', 'input', '', '', '网页内容所包含的核心搜索关键词，多个关键字请用英文逗号&quot;,&quot;分隔', 3, 1, 1494690508, 1494690780, 999999),
(43, 1, 'base', '网站描述', 'site_description', '212312', 'textarea', '', '', '网页的描述信息，搜索引擎采纳后，作为搜索结果中的页面摘要显示，建议不超过80个字', 4, 1, 1494690669, 1494691075, 999999),
(44, 1, 'base', 'ICP备案信息', 'site_icp', '', 'input', '', '', '请填写ICP备案号，用于展示在网站底部，ICP备案官网：&lt;a href=&quot;http://www.miibeian.gov.cn&quot; target=&quot;_blank&quot;&gt;http://www.miibeian.gov.cn&lt;/a&gt;', 5, 1, 1494691721, 1494692046, 999999),
(46, 1, 'base', '网站名称', 'site_name', 'SEO计费系统', 'input', '', '', '将显示在浏览器窗口标题等位置', 6, 1, 1494692103, 1494694680, 999999),
(47, 1, 'base', '用户最低余额', 'site_price', '0', 'input', NULL, '', '', 7, 1, 0, 0, 999999),
(65, 1, 'api', '百度PC-KEY', 'baidu_pc_key', 'KEY', 'input', '', '', '百度PC查询KEY', 9, 1, 1494692781, 1494693966, 999999),
(66, 1, 'api', '百度移动-KEY', 'baidu_mobile_key', 'KEY', 'input', '', '', '百度移动查询KEY', 10, 1, 1494692781, 1494693966, 999999),
(67, 1, 'base', '结算规则', 'site_settlement', '1', 'select', '选择结算规则,会员组优先,关键字指数优先', '', '网站结算规则，使用用户组结算还是更具关键字指数结算', 8, 1, 1494691721, 1494692046, 999999),
(68, 1, 'api', '指数-KEY', 'baidu_zhihshu_key', 'KEY', 'input', '', '', '百度指数查询KEY', 11, 1, 1494692781, 1494693966, 999999),
(69, 1, 'base', '结算规则', 'site_settlement', '1', 'select', '选择结算规则,会员组优先,关键字指数优先', '', '网站结算规则，使用用户组结算还是更具关键字指数结算', 9, 1, 1494691721, 1494692046, 8),
(73, 1, 'base', '结算规则', 'site_settlement', '1', 'select', '选择结算规则,会员组优先,关键字指数优先', '', '网站结算规则，使用用户组结算还是更具关键字指数结算', 0, 1, 1551678225, 1551678225, 9),
(74, 1, 'base', '结算规则', 'site_settlement', '1', 'select', '选择结算规则,会员组优先,关键字指数优先', '', '网站结算规则，使用用户组结算还是更具关键字指数结算', 0, 1, 1551688527, 1551688527, 5),
(75, 1, 'base', '结算规则', 'site_settlement', '1', 'select', '选择结算规则,会员组优先,关键字指数优先', '', '网站结算规则，使用用户组结算还是更具关键字指数结算', 0, 1, 1551762541, 1551762541, 17),
(76, 1, 'base', '结算规则', 'site_settlement', '1', 'select', '选择结算规则,会员组优先,关键字指数优先', '', '网站结算规则，使用用户组结算还是更具关键字指数结算', 0, 1, 1551864228, 1551864228, 21),
(77, 1, 'base', '结算规则', 'site_settlement', '1', 'select', '选择结算规则,会员组优先,关键字指数优先', '', '网站结算规则，使用用户组结算还是更具关键字指数结算', 0, 1, 1551919063, 1551919063, 24),
(79, 1, 'base', '结算规则', 'site_settlement', '1', 'select', '选择结算规则,会员组优先,关键字指数优先', '', '网站结算规则，使用用户组结算还是更具关键字指数结算', 0, 1, 1551940058, 1551940058, 29),
(80, 1, 'base', '结算规则', 'site_settlement', '1', 'select', '选择结算规则,会员组优先,关键字指数优先', '', '网站结算规则，使用用户组结算还是更具关键字指数结算', 0, 1, 1552012261, 1552012261, 26),
(81, 1, 'base', '结算规则', 'site_settlement', '1', 'select', '选择结算规则,会员组优先,关键字指数优先', '', '网站结算规则，使用用户组结算还是更具关键字指数结算', 0, 1, 1553000780, 1553000780, 40),
(82, 1, 'base', '结算规则', 'site_settlement', '1', 'select', '选择结算规则,会员组优先,关键字指数优先', '', '网站结算规则，使用用户组结算还是更具关键字指数结算', 0, 1, 1553744190, 1553744190, 54),
(83, 1, 'base', '结算规则', 'site_settlement', '1', 'select', '选择结算规则,会员组优先,关键字指数优先', '', '网站结算规则，使用用户组结算还是更具关键字指数结算', 0, 1, 1553759574, 1553759574, 55),
(84, 1, 'api', '360PC-KEY', '360_pc_key', 'KEY', 'input', '', '', '360-PC-排名查询', 12, 1, 1494692781, 1494693966, 999999),
(85, 1, 'base', '结算规则', 'site_settlement', '1', 'select', '选择结算规则,会员组优先,关键字指数优先', '', '网站结算规则，使用用户组结算还是更具关键字指数结算', 0, 1, 1555051648, 1555051648, 58),
(101, 1, 'base', '结算规则', 'site_settlement', '2', 'select', '选择结算规则,会员组优先,关键字指数优先', '', '网站结算规则，使用用户组结算还是更具关键字指数结算', 2, 1, 1555464213, 1555464213, 25),
(102, 1, 'base', '网站名称', 'site_name', '速达SEO计费系统', 'input', '', '', '将显示在浏览器窗口标题等位置', 1, 1, 1555464242, 1555464242, 25),
(103, 1, 'base', '网站名称', 'site_name', '速达SEO计费系统', 'input', '', '', '将显示在浏览器窗口标题等位置', 1, 1, 1555480557, 1555480557, 54),
(104, 1, 'base', '结算规则', 'site_settlement', '1', 'select', '选择结算规则,会员组优先,关键字指数优先', '', '网站结算规则，使用用户组结算还是更具关键字指数结算', 2, 1, 1556187379, 1556187379, 69),
(105, 1, 'base', '网站名称', 'site_name', '速达SEO计费系统', 'input', '', '', '将显示在浏览器窗口标题等位置', 1, 1, 1556187379, 1556187379, 69),
(106, 1, 'base', '结算规则', 'site_settlement', '1', 'select', '选择结算规则,会员组优先,关键字指数优先', '', '网站结算规则，使用用户组结算还是更具关键字指数结算', 2, 1, 1557281809, 1557281809, 72),
(107, 1, 'base', '网站名称', 'site_name', '速达SEO计费系统', 'input', '', '', '将显示在浏览器窗口标题等位置', 1, 1, 1557281809, 1557281809, 72),
(108, 1, 'api', '360移动-KEY', '360_mobile_key', 'KEY', 'input', '', '', '360-移动-排名查询', 12, 1, 1494692781, 1494693966, 999999),
(109, 1, 'api', '搜狗PC-KEY', 'sougou_pc_key', 'KEY', 'input', '', '', '搜狗-PC-排名查询', 12, 1, 1494692781, 1494693966, 999999),
(110, 1, 'api', '搜狗移动-KEY', 'sougou_mobile_key', 'KEY', 'input', '', '', '搜狗-移动-排名查询', 12, 1, 1494692781, 1494693966, 999999),
(111, 1, 'base', '结算规则', 'site_settlement', '1', 'select', '选择结算规则,会员组优先,关键字指数优先', '', '网站结算规则，使用用户组结算还是更具关键字指数结算', 2, 1, 1558602756, 1558602756, 74),
(112, 1, 'base', '网站名称', 'site_name', '速达SEO计费系统', 'input', '', '', '将显示在浏览器窗口标题等位置', 1, 1, 1558602756, 1558602756, 74),
(113, 1, 'base', '结算规则', 'site_settlement', '1', 'select', '选择结算规则,会员组优先,关键字指数优先', '', '网站结算规则，使用用户组结算还是更具关键字指数结算', 2, 1, 1558606427, 1558606427, 76),
(114, 1, 'base', '网站名称', 'site_name', '速达SEO计费系统', 'input', '', '', '将显示在浏览器窗口标题等位置', 1, 1, 1558606427, 1558606427, 76),
(115, 1, 'base', '结算规则', 'site_settlement', '1', 'select', '选择结算规则,会员组优先,关键字指数优先', '', '网站结算规则，使用用户组结算还是更具关键字指数结算', 2, 1, 1558686981, 1558686981, 80),
(116, 1, 'base', '网站名称', 'site_name', '速达SEO计费系统', 'input', '', '', '将显示在浏览器窗口标题等位置', 1, 1, 1558686981, 1558686981, 80),
(117, 1, 'base', '结算规则', 'site_settlement', '1', 'select', '选择结算规则,会员组优先,关键字指数优先', '', '网站结算规则，使用用户组结算还是更具关键字指数结算', 2, 1, 1558947688, 1558947688, 66),
(118, 1, 'base', '网站名称', 'site_name', '速达SEO计费系统a', 'input', '', '', '将显示在浏览器窗口标题等位置', 1, 1, 1558947688, 1558947688, 66),
(119, 1, 'base', '结算规则', 'site_settlement', '1', 'select', '选择结算规则,会员组优先,关键字指数优先', '', '网站结算规则，使用用户组结算还是更具关键字指数结算', 2, 1, 1558948366, 1558948366, 82),
(120, 1, 'base', '网站名称', 'site_name', '速排优化系统', 'input', '', '', '将显示在浏览器窗口标题等位置', 1, 1, 1558948366, 1558948366, 82),
(121, 1, 'base', '结算规则', 'site_settlement', '1', 'select', '选择结算规则,会员组优先,关键字指数优先', '', '网站结算规则，使用用户组结算还是更具关键字指数结算', 2, 1, 1559927357, 1559927357, 78),
(122, 1, 'base', '网站名称', 'site_name', '速达SEO计费系统', 'input', '', '', '将显示在浏览器窗口标题等位置', 1, 1, 1559927357, 1559927357, 78),
(123, 1, 'base', '结算规则', 'site_settlement', '2', 'select', '选择结算规则,会员组优先,关键字指数优先', '', '网站结算规则，使用用户组结算还是更具关键字指数结算', 2, 1, 1561533673, 1561533673, 91),
(124, 1, 'base', '网站名称', 'site_name', '速达SEO计费系统', 'input', '', '', '将显示在浏览器窗口标题等位置', 1, 1, 1561533673, 1561533673, 91),
(231, 1, 'engines', '百度PC', 'baidupcs', '95', 'input', '', '', '%,设置百度PC搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1561688748, 1561688748, 91),
(232, 1, 'engines', '百度移动', 'baidumobiles', '90', 'input', '', '', '%,设置百度移动搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1561688748, 1561688748, 91),
(233, 1, 'engines', '360PC', '360pcs', '85', 'input', '', '', '%,设置360PC搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1561688748, 1561688748, 91),
(234, 1, 'engines', '360移动', '360mobiles', '80', 'input', '', '', '%,设置360移动搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1561688748, 1561688748, 91),
(235, 1, 'engines', '搜狗PC', 'sougoupcs', '65', 'input', '', '', '%,设置搜狗PC搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1561688748, 1561688748, 91),
(236, 1, 'engines', '搜狗移动', 'sougoumobiles', '60', 'input', '', '', '%,设置搜狗移动搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1561688748, 1561688748, 91),
(255, 1, 'engines', '百度PC', 'baidupcs', '90', 'input', '', '', '%,设置百度PC搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1561690703, 1561690703, 999999),
(256, 1, 'engines', '百度移动', 'baidumobiles', '100', 'input', '', '', '%,设置百度移动搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1561690703, 1561690703, 999999),
(257, 1, 'engines', '360PC', '360pcs', '70', 'input', '', '', '%,设置360PC搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1561690703, 1561690703, 999999),
(258, 1, 'engines', '360移动', '360mobiles', '80', 'input', '', '', '%,设置360移动搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1561690703, 1561690703, 999999),
(259, 1, 'engines', '搜狗PC', 'sougoupcs', '60', 'input', '', '', '%,设置搜狗PC搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1561690703, 1561690703, 999999),
(260, 1, 'engines', '搜狗移动', 'sougoumobiles', '70', 'input', '', '', '%,设置搜狗移动搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1561690703, 1561690703, 999999),
(261, 1, 'engines', '百度PC', 'baidupcs', '100', 'input', '', '', '%,设置百度PC搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1561712285, 1561712285, 66),
(262, 1, 'engines', '百度移动', 'baidumobiles', '100', 'input', '', '', '%,设置百度移动搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1561712285, 1561712285, 66),
(263, 1, 'engines', '360PC', '360pcs', '100', 'input', '', '', '%,设置360PC搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1561712285, 1561712285, 66),
(264, 1, 'engines', '360移动', '360mobiles', '100', 'input', '', '', '%,设置360移动搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1561712285, 1561712285, 66),
(265, 1, 'engines', '搜狗PC', 'sougoupcs', '100', 'input', '', '', '%,设置搜狗PC搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1561712285, 1561712285, 66),
(266, 1, 'engines', '搜狗移动', 'sougoumobiles', '100', 'input', '', '', '%,设置搜狗移动搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1561712285, 1561712285, 66),
(267, 1, 'engines', '百度PC', 'baidupcs', '100', 'input', '', '', '%,设置百度PC搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1561714814, 1561714814, 74),
(268, 1, 'engines', '百度移动', 'baidumobiles', '100', 'input', '', '', '%,设置百度移动搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1561714814, 1561714814, 74),
(269, 1, 'engines', '360PC', '360pcs', '100', 'input', '', '', '%,设置360PC搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1561714814, 1561714814, 74),
(270, 1, 'engines', '360移动', '360mobiles', '100', 'input', '', '', '%,设置360移动搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1561714814, 1561714814, 74),
(271, 1, 'engines', '搜狗PC', 'sougoupcs', '100', 'input', '', '', '%,设置搜狗PC搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1561714814, 1561714814, 74),
(272, 1, 'engines', '搜狗移动', 'sougoumobiles', '100', 'input', '', '', '%,设置搜狗移动搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1561714814, 1561714814, 74),
(273, 1, 'recharge', '支付设置', 'recharge', '{\"image\":\"\\/images\\/recharge\\/54\\/bc1f00d4d818ffeda586c465dc4aba.png\",\"text\":\"\\u8bc6\\u522b\\u4e8c\\u7ef4\\u7801\\u8054\\u7cfb\\u5728\\u7ebf\\u5ba2\\u670d\\u5145\\u503c\\uff01\\uff01\\uff01\"}', 'input', '', '', '', 1, 1, 1562055093, 1562055093, 91),
(274, 1, 'recharge', '支付设置', 'recharge', '{\"image\":\"\\/images\\/1562053100.png\",\"text\":\"\\u8bc6\\u522b\\u4e8c\\u7ef4\\u7801\\u8054\\u7cfb\\u5728\\u7ebf\\u5ba2\\u670d\\u5145\\u503c\\uff01\"}', 'input', '', '', '', 1, 1, 1562056395, 1562056395, 92),
(275, 1, 'engines', '百度PC', 'baidupcs', '100', 'input', '', '', '%,设置百度PC搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1562056688, 1562056688, 92),
(276, 1, 'engines', '百度移动', 'baidumobiles', '100', 'input', '', '', '%,设置百度移动搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1562056688, 1562056688, 92),
(277, 1, 'engines', '360PC', '360pcs', '100', 'input', '', '', '%,设置360PC搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1562056688, 1562056688, 92),
(278, 1, 'engines', '360移动', '360mobiles', '100', 'input', '', '', '%,设置360移动搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1562056688, 1562056688, 92),
(279, 1, 'engines', '搜狗PC', 'sougoupcs', '100', 'input', '', '', '%,设置搜狗PC搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1562056688, 1562056688, 92),
(280, 1, 'engines', '搜狗移动', 'sougoumobiles', '100', 'input', '', '', '%,设置搜狗移动搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1562056688, 1562056688, 92),
(281, 1, 'recharge', '支付设置', 'recharge', '{\"image\":\"\\/images\\/1562053100.png\",\"text\":\"\\u8bc6\\u522b\\u4e8c\\u7ef4\\u7801\\u8054\\u7cfb\\u5728\\u7ebf\\u5ba2\\u670d\\u5145\\u503c\\uff01\"}', 'input', '', '', '', 1, 1, 1562057268, 1562057268, 70),
(282, 1, 'recharge', '支付设置', 'recharge', '{\"image\":\"\\/images\\/recharge\\/78\\/59d902a53d1967c07f54492b63aeb4.png\",\"text\":\"\\u6682\\u672a\\u5f00\\u901a\\u5728\\u7ebf\\u652f\\u4ed81\\uff0c\\u8bf7\\u8054\\u7cfb\\u5728\\u7ebf\\u5ba2\\u670d\\uff01\"}', 'input', '', '', '', 1, 1, 1562057282, 1562057282, 66),
(283, 1, 'base', '结算规则', 'site_settlement', '1', 'select', '选择结算规则,会员组优先,关键字指数优先', '', '网站结算规则，使用用户组结算还是更具关键字指数结算', 2, 1, 1562120200, 1562120200, 96),
(284, 1, 'base', '网站名称', 'site_name', '速达SEO计费系统', 'input', '', '', '将显示在浏览器窗口标题等位置', 1, 1, 1562120200, 1562120200, 96),
(285, 1, 'recharge', '支付设置', 'recharge', '{\"image\":\"\\/images\\/1562053100.png\",\"text\":\"\\u8bc6\\u522b\\u4e8c\\u7ef4\\u7801\\u8054\\u7cfb\\u5728\\u7ebf\\u5ba2\\u670d\\u5145\\u503c\\uff01\"}', 'input', '', '', '', 1, 1, 1562120202, 1562120202, 96),
(286, 1, 'engines', '百度PC', 'baidupcs', '100', 'input', '', '', '%,设置百度PC搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1562120203, 1562120203, 96),
(287, 1, 'engines', '百度移动', 'baidumobiles', '100', 'input', '', '', '%,设置百度移动搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1562120203, 1562120203, 96),
(288, 1, 'engines', '360PC', '360pcs', '100', 'input', '', '', '%,设置360PC搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1562120203, 1562120203, 96),
(289, 1, 'engines', '360移动', '360mobiles', '100', 'input', '', '', '%,设置360移动搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1562120203, 1562120203, 96),
(290, 1, 'engines', '搜狗PC', 'sougoupcs', '100', 'input', '', '', '%,设置搜狗PC搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1562120203, 1562120203, 96),
(291, 1, 'engines', '搜狗移动', 'sougoumobiles', '100', 'input', '', '', '%,设置搜狗移动搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1562120203, 1562120203, 96),
(292, 1, 'recharge', '支付设置', 'recharge', '{\"image\":\"\\/images\\/recharge\\/54\\/bc1f00d4d818ffeda586c465dc4aba.png\",\"text\":\"\\u8bc6\\u522b\\u4e8c\\u7ef4\\u7801\\u8054\\u7cfb\\u5728\\u7ebf\\u5ba2\\u670d\\u5145\\u503c\\uff01\"}', 'input', '', '', '', 1, 1, 1562143032, 1562143032, 74),
(293, 1, 'base', '结算规则', 'site_settlement', '1', 'select', '选择结算规则,会员组优先,关键字指数优先', '', '网站结算规则，使用用户组结算还是更具关键字指数结算', 2, 1, 1562564888, 1562564888, 99),
(294, 1, 'base', '网站名称', 'site_name', '计费系统', 'input', '', '', '将显示在浏览器窗口标题等位置', 1, 1, 1562564888, 1562564888, 99),
(295, 1, 'recharge', '支付设置', 'recharge', '{\"image\":\"\\/images\\/1562053100.png\",\"text\":\"\\u8bc6\\u522b\\u4e8c\\u7ef4\\u7801\\u8054\\u7cfb\\u5728\\u7ebf\\u5ba2\\u670d\\u5145\\u503c\\uff01\"}', 'input', '', '', '', 1, 1, 1562564891, 1562564891, 99),
(296, 1, 'engines', '百度PC', 'baidupcs', '100', 'input', '', '', '%,设置百度PC搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1562564892, 1562564892, 99),
(297, 1, 'engines', '百度移动', 'baidumobiles', '100', 'input', '', '', '%,设置百度移动搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1562564892, 1562564892, 99),
(298, 1, 'engines', '360PC', '360pcs', '100', 'input', '', '', '%,设置360PC搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1562564892, 1562564892, 99),
(299, 1, 'engines', '360移动', '360mobiles', '100', 'input', '', '', '%,设置360移动搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1562564892, 1562564892, 99),
(300, 1, 'engines', '搜狗PC', 'sougoupcs', '100', 'input', '', '', '%,设置搜狗PC搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1562564892, 1562564892, 99),
(301, 1, 'engines', '搜狗移动', 'sougoumobiles', '100', 'input', '', '', '%,设置搜狗移动搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1562564892, 1562564892, 99),
(302, 1, 'recharge', '支付设置', 'recharge', '{\"image\":\"\\/images\\/recharge\\/19\\/4d85f94ebcf4456f9d1f92f482422c.jpg\",\"text\":\"\\u8bc6\\u522b\\u4e8c\\u7ef4\\u7801\\u8054\\u7cfb\\u5728\\u7ebf\\u5ba2\\u670d\\u5145\\u503c\\uff01\"}', 'input', '', '', '', 1, 1, 1566460203, 1566460203, 103),
(303, 1, 'slider', '幻灯片', 'slider', '{\"image\":\"\\/images\\/slider\\/77\\/f1fa13cce7e63bb00e5485b82312f5.png\",\"text\":\"\\u5e7b\\u706f\\u7247\"}', 'input', '', '', '', 1, 1, 1568103904, 1568103904, 91),
(304, 1, 'slider', '幻灯片', 'slider', '{\"image\":\"\\/images\\/slider\\/3e\\/4772f53b8f5d47970e43eba05b8368.png\",\"text\":\"\\u5e7b\\u706f\\u7247\"}', 'input', '', '', '', 2, 1, 1568103904, 1568103904, 91),
(305, 1, 'logo', '支付设置', 'logo', '{\"image\":\"\\/images\\/logo\\/80\\/43b7169dd9c08406b60affc1cf760b.png\",\"text\":\"\\u7f51\\u7ad9logo\"}', 'input', '', '', '', 1, 1, 1568105426, 1568105426, 91),
(306, 1, 'topmenu', '首页', 'topmenu', 'http://www.baidu.com', 'input', '', '', '', 1, 1, 1568110468, 1568110468, 91),
(307, 1, 'topmenu', '云产品', 'topmenu', 'http://www.baidu.com', 'input', '', '', '留空则不显示，最多7个', 1, 1, 1568111304, 1568111304, 91),
(308, 1, 'topmenu', '数据监控', 'topmenu', 'http://www.baidu.com', 'input', '', '', '留空则不显示，最多7个', 1, 1, 1568111304, 1568111304, 91),
(310, 1, 'bottommenu', '关于我们', 'bottommenu', 'http://www.baidu.com', 'input', '', '', '留空则不显示，最多7个', 1, 1, 1568111545, 1568111545, 91),
(311, 1, 'bottommenu', '新闻动态', 'bottommenu', 'http://www.baidu.com', 'input', '', '', '留空则不显示，最多7个', 1, 1, 1568111545, 1568111545, 91),
(314, 1, 'domain', '域名', 'domain', 'seo1xx1x.17s.cn', 'input', '', '', '设置访问的域名，徐管理员审核后生效', 1, 1, 1568112521, 1568112521, 91),
(323, 1, 'copyright', '备案信息', 'ipcinfo', '蜀ICP备12345678', 'input', '', '', '底部备案信息,示例：蜀ICP备12345678', 3, 1, 1568190473, 1568190473, 91),
(324, 1, 'copyright', '版权信息', 'copyrightinfo', 'Copyright © 2012-2017 17s.cn All Rights Reserved. 速达网络 版权所有', 'input', '', '', '底部版权信息,示例：Copyright © 2012-2017 17s.cn All Rights Reserved. 速达网络 版权所有', 1, 1, 1568190534, 1568190534, 91),
(325, 1, 'copyright', '公司信息', 'companyinfo', '南充速达网络有限公司 地址：南充市金融广场5栋', 'input', '', '', '底部公司信息,示例：南充速达网络有限公司 地址：南充市金融广场5栋', 2, 1, 1568190534, 1568190534, 91),
(328, 1, 'copyright', '公司信息', 'companyinfo', '', 'input', '', '', '底部公司信息,示例：南充速达网络有限公司 地址：南充市金融广场5栋', 2, 1, 1568193499, 1568193499, 5),
(329, 1, 'copyright', '备案信息', 'ipcinfo', '', 'input', '', '', '底部备案信息,示例：蜀ICP备12345678', 3, 1, 1568193499, 1568193499, 5),
(330, 1, 'slider', '幻灯片', 'slider', '{\"image\":\"\\/images\\/slider\\/77\\/f1fa13cce7e63bb00e5485b82312f5.png\",\"text\":\"\\u5e7b\\u706f\\u7247\"}', 'input', '', '', '', 1, 1, 1568193570, 1568193570, 999999),
(331, 1, 'slider', '幻灯片', 'slider', '{\"image\":\"\\/images\\/slider\\/3e\\/4772f53b8f5d47970e43eba05b8368.png\",\"text\":\"\\u5e7b\\u706f\\u7247\"}', 'input', '', '', '', 2, 1, 1568193570, 1568193570, 999999),
(332, 1, 'logo', '支付设置', 'logo', '{\"image\":\"\\/images\\/logo\\/f9\\/ae53ecfb191be97f04a6ea21626285.png\",\"text\":\"\\u7f51\\u7ad9logo\"}', 'input', '', '', '', 1, 1, 1568193604, 1568193604, 999999),
(333, 1, 'domain', '域名', 'domain', 'seo.17s.cn', 'input', '', '', '设置访问的域名，徐管理员审核后生效', 1, 0, 1568193604, 1568193604, 999999),
(334, 1, 'copyright', '版权信息', 'copyrightinfo', 'Copyright © 2012-2017 17s.cn All Rights Reserved. 速达网络 版权所有', 'input', '', '', '底部版权信息,示例：Copyright © 2012-2017 17s.cn All Rights Reserved. 速达网络 版权所有', 1, 1, 1568193800, 1568193800, 999999),
(335, 1, 'copyright', '公司信息', 'companyinfo', '网络有限公司 地址：南充市金融广场5栋', 'input', '', '', '底部公司信息,示例：南充速度网络有限公司 地址：南充市金融广场5栋', 2, 1, 1568193800, 1568193800, 999999),
(336, 1, 'copyright', '备案信息', 'ipcinfo', '蜀ICP备12345678', 'input', '', '', '底部备案信息,示例：蜀ICP备12345678', 3, 1, 1568193800, 1568193800, 999999),
(337, 1, 'topmenu', '网站首页', 'topmenu', 'http://www.17s.cn/', 'input', '', '', '留空则不显示，最多7个', 1, 1, 1568194254, 1568194254, 999999),
(338, 1, 'bottommenu', '关于我们', 'bottommenu', 'http://www.17s.cn/guanyuwomen/', 'input', '', '', '留空则不显示，最多7个', 1, 1, 1568194254, 1568194254, 999999),
(339, 1, 'topmenu', '公司资讯', 'topmenu', 'http://www.17s.cn/xinwenzhongxin/', 'input', '', '', '留空则不显示，最多7个', 1, 1, 1568194261, 1568194261, 999999),
(340, 1, 'bottommenu', '服务介绍', 'bottommenu', 'http://www.17s.cn/zaixianliuyan/', 'input', '', '', '留空则不显示，最多7个', 1, 1, 1568194261, 1568194261, 999999),
(341, 1, 'topmenu', '开发案例', 'topmenu', 'http://www.17s.cn/kehuanli/', 'input', '', '', '留空则不显示，最多7个', 1, 1, 1568199338, 1568199338, 999999),
(342, 1, 'topmenu', '报价', 'topmenu', 'http://www.17s.cn/rongyuzhengshu/', 'input', '', '', '留空则不显示，最多7个', 1, 1, 1568199338, 1568199338, 999999),
(343, 1, 'bottommenu', '联系我们', 'bottommenu', 'http://www.17s.cn/lianxiwomen/', 'input', '', '', '留空则不显示，最多7个', 1, 1, 1568199338, 1568199338, 999999),
(344, 1, 'bottommenu', '电话咨询', 'bottommenu', 'tel:13547444474', 'input', '', '', '留空则不显示，最多7个', 1, 1, 1568199338, 1568199338, 999999),
(345, 1, 'topmenu', '产品简介', 'topmenu', 'http://seo.17s.cn/brief.html', 'input', '', '', '留空则不显示，最多7个', 1, 1, 1568199421, 1568199421, 999999),
(346, 1, 'topmenu', '产品介绍', 'topmenu', 'http://seo.17s.cn/pro.html', 'input', '', '', '留空则不显示，最多7个', 1, 1, 1568199421, 1568199421, 999999),
(347, 1, 'slider', '幻灯片', 'slider', '{\"image\":\"\\/images\\/login_pattern_1.png\",\"text\":\"\\u5e7b\\u706f\\u7247\"}', 'input', '', '', '', 1, 1, 1568261671, 1568261671, 66),
(348, 1, 'slider', '幻灯片', 'slider', '{\"image\":\"\\/images\\/login_pattern_2.png\",\"text\":\"\\u5e7b\\u706f\\u7247\"}', 'input', '', '', '', 2, 1, 1568261671, 1568261671, 66),
(349, 1, 'copyright', '版权信息', 'copyrightinfo', '', 'input', '', '', '底部版权信息,示例：Copyright © 2012-2017 17s.cn All Rights Reserved. 速达网络 版权所有', 1, 1, 1568261671, 1568261671, 66),
(350, 1, 'copyright', '公司信息', 'companyinfo', '', 'input', '', '', '底部公司信息,示例：南充速度网络有限公司 地址：南充市金融广场5栋', 2, 1, 1568261671, 1568261671, 66),
(351, 1, 'copyright', '备案信息', 'ipcinfo', '', 'input', '', '', '底部备案信息,示例：蜀ICP备12345678', 3, 1, 1568261671, 1568261671, 66),
(352, 1, 'logo', '支付设置', 'logo', '{\"image\":\"\\/images\\/logo.png\",\"text\":\"\\u7f51\\u7ad9logo\"}', 'input', '', '', '', 1, 1, 1568261671, 1568261671, 66),
(353, 1, 'domain', '域名', 'domain', 'www.17s.cn', 'input', '', '', '设置访问的域名，需联系管理员审核通过后生效，修改后需再次审核，请谨慎操作。', 1, 1, 1568261672, 1568261672, 66),
(354, 1, 'slider', '幻灯片', 'slider', '{\"image\":\"\\/images\\/login_pattern_1.png\",\"text\":\"\\u5e7b\\u706f\\u7247\"}', 'input', '', '', '', 1, 1, 1569230926, 1569230926, 103),
(355, 1, 'slider', '幻灯片', 'slider', '{\"image\":\"\\/images\\/login_pattern_2.png\",\"text\":\"\\u5e7b\\u706f\\u7247\"}', 'input', '', '', '', 2, 1, 1569230926, 1569230926, 103),
(356, 1, 'logo', '支付设置', 'logo', '{\"image\":\"\\/images\\/logo.png\",\"text\":\"\\u7f51\\u7ad9logo\"}', 'input', '', '', '', 1, 1, 1569230927, 1569230927, 103),
(357, 1, 'copyright', '版权信息', 'copyrightinfo', '', 'input', '', '', '底部版权信息,示例：Copyright © 2012-2017 17s.cn All Rights Reserved. 速达网络 版权所有', 1, 1, 1569230927, 1569230927, 103),
(358, 1, 'copyright', '公司信息', 'companyinfo', '', 'input', '', '', '底部公司信息,示例：某某公司 地址：某某地址', 2, 1, 1569230927, 1569230927, 103),
(359, 1, 'copyright', '备案信息', 'ipcinfo', '', 'input', '', '', '底部备案信息,示例：蜀ICP备12345678', 3, 1, 1569230927, 1569230927, 103),
(360, 1, 'domain', '域名', 'domain', '', 'input', '', '', '设置访问的域名，需联系管理员审核通过后生效，修改后需再次审核，请谨慎操作。', 1, 0, 1569230927, 1569230927, 103),
(361, 1, 'engines', '百度PC', 'baidupcs', '100', 'input', '', '', '%,设置百度PC搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1569231150, 1569231150, 103),
(362, 1, 'engines', '百度移动', 'baidumobiles', '100', 'input', '', '', '%,设置百度移动搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1569231150, 1569231150, 103),
(363, 1, 'engines', '360PC', '360pcs', '100', 'input', '', '', '%,设置360PC搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1569231150, 1569231150, 103),
(364, 1, 'engines', '360移动', '360mobiles', '100', 'input', '', '', '%,设置360移动搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1569231150, 1569231150, 103),
(365, 1, 'engines', '搜狗PC', 'sougoupcs', '100', 'input', '', '', '%,设置搜狗PC搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1569231150, 1569231150, 103),
(366, 1, 'engines', '搜狗移动', 'sougoumobiles', '100', 'input', '', '', '%,设置搜狗移动搜索引擎的扣费百分比，如填80表示此搜索引擎优化费用为设置基础费用的80%', 1, 1, 1569231150, 1569231150, 103),
(367, 1, 'base', '结算规则', 'site_settlement', '1', 'select', '选择结算规则,会员组优先,关键字指数优先', '', '网站结算规则，使用用户组结算还是更具关键字指数结算', 2, 1, 1569231213, 1569231213, 103),
(368, 1, 'base', '网站名称', 'site_name', '计费系统', 'input', '', '', '将显示在浏览器窗口标题等位置', 1, 1, 1569231213, 1569231213, 103),
(369, 1, 'logo', '支付设置', 'logo', '{\"image\":\"\\/images\\/logo.png\",\"text\":\"\\u7f51\\u7ad9logo\"}', 'input', '', '', '', 1, 1, 1571195745, 1571195745, 74),
(370, 1, 'slider', '幻灯片', 'slider', '{\"image\":\"\\/images\\/login_pattern_1.png\",\"text\":\"\\u5e7b\\u706f\\u7247\"}', 'input', '', '', '', 1, 1, 1571195745, 1571195745, 74),
(371, 1, 'slider', '幻灯片', 'slider', '{\"image\":\"\\/images\\/login_pattern_2.png\",\"text\":\"\\u5e7b\\u706f\\u7247\"}', 'input', '', '', '', 2, 1, 1571195745, 1571195745, 74),
(372, 1, 'copyright', '版权信息', 'copyrightinfo', '', 'input', '', '', '底部版权信息,示例：Copyright © 2012-2017 17s.cn All Rights Reserved. 速达网络 版权所有', 1, 1, 1571195746, 1571195746, 74),
(373, 1, 'copyright', '公司信息', 'companyinfo', '', 'input', '', '', '底部公司信息,示例：某某公司 地址：某某地址', 2, 1, 1571195746, 1571195746, 74),
(374, 1, 'copyright', '备案信息', 'ipcinfo', '', 'input', '', '', '底部备案信息,示例：蜀ICP备12345678', 3, 1, 1571195746, 1571195746, 74),
(375, 1, 'domain', '域名', 'domain', '', 'input', '', '', '设置访问的域名，需联系管理员审核通过后生效，修改后需再次审核，请谨慎操作。', 1, 0, 1571195747, 1571195747, 74),
(376, 1, 'slider', '幻灯片', 'slider', '{\"image\":\"\\/images\\/login_pattern_1.png\",\"text\":\"\\u5e7b\\u706f\\u7247\"}', 'input', '', '', '', 1, 1, 1573451981, 1573451981, 96),
(377, 1, 'slider', '幻灯片', 'slider', '{\"image\":\"\\/images\\/login_pattern_2.png\",\"text\":\"\\u5e7b\\u706f\\u7247\"}', 'input', '', '', '', 2, 1, 1573451981, 1573451981, 96),
(378, 1, 'logo', '支付设置', 'logo', '{\"image\":\"\\/images\\/logo.png\",\"text\":\"\\u7f51\\u7ad9logo\"}', 'input', '', '', '', 1, 1, 1573451981, 1573451981, 96),
(379, 1, 'copyright', '版权信息', 'copyrightinfo', '', 'input', '', '', '底部版权信息,示例：Copyright © 2012-2017 17s.cn All Rights Reserved. 速达网络 版权所有', 1, 1, 1573451982, 1573451982, 96),
(380, 1, 'copyright', '公司信息', 'companyinfo', '', 'input', '', '', '底部公司信息,示例：某某公司 地址：某某地址', 2, 1, 1573451982, 1573451982, 96),
(381, 1, 'copyright', '备案信息', 'ipcinfo', '', 'input', '', '', '底部备案信息,示例：蜀ICP备12345678', 3, 1, 1573451982, 1573451982, 96),
(382, 1, 'domain', '域名', 'domain', '', 'input', '', '', '设置访问的域名，需联系管理员审核通过后生效，修改后需再次审核，请谨慎操作。', 1, 1, 1573451983, 1573451983, 96);

-- --------------------------------------------------------

--
-- 表的结构 `seo_test`
--

CREATE TABLE `seo_test` (
  `id` int(11) UNSIGNED NOT NULL,
  `pid` int(11) UNSIGNED NOT NULL,
  `name` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `seo_user_group`
--

CREATE TABLE `seo_user_group` (
  `id` int(11) UNSIGNED NOT NULL,
  `group_name` varchar(20) NOT NULL COMMENT '等级名',
  `seo_free` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'SEO任务达标每天扣费',
  `descr` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `create_time` int(10) DEFAULT NULL,
  `keyword_free` decimal(10,2) DEFAULT NULL COMMENT '关键词排名费用',
  `keyword_free2` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '次页扣费',
  `agent_id` int(9) DEFAULT '0' COMMENT '属于哪个代理商 null代表系统',
  `code` varchar(8) DEFAULT '0' COMMENT '推荐码',
  `isdefault` int(2) NOT NULL DEFAULT '0' COMMENT '是否是默认分组，默认分组时会员注册将分配到该分组的代理商名下'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员等级配置表';

-- --------------------------------------------------------

--
-- 表的结构 `seo_web_url`
--

CREATE TABLE `seo_web_url` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '网址',
  `url` varchar(255) NOT NULL,
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `admin_host` varchar(255) NOT NULL DEFAULT '' COMMENT '后台URL',
  `admin_access` varchar(100) NOT NULL DEFAULT '' COMMENT '后台账号',
  `admin_password` varchar(100) NOT NULL DEFAULT '' COMMENT '后台密码',
  `ftp_host` varchar(255) NOT NULL DEFAULT '' COMMENT 'ftp地址',
  `ftp_port` int(5) NOT NULL DEFAULT '21' COMMENT 'FTP端口',
  `ftp_access` varchar(100) NOT NULL DEFAULT '' COMMENT 'FTP账号',
  `ftp_password` varchar(100) NOT NULL DEFAULT '' COMMENT 'FTP密码',
  `create_time` int(10) DEFAULT NULL COMMENT '添加时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '伪删除',
  `xiongzhang` varchar(32) NOT NULL COMMENT '熊掌号'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='网站表 【url字段唯一】';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `seo_admin_token`
--
ALTER TABLE `seo_admin_token`
  ADD PRIMARY KEY (`uid`),
  ADD KEY `uid` (`uid`,`access_token`(255)) USING BTREE;

--
-- Indexes for table `seo_admin_user`
--
ALTER TABLE `seo_admin_user`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `username` (`username`) USING BTREE,
  ADD KEY `mobile` (`mobile`) USING BTREE;

--
-- Indexes for table `seo_authentication_enterprise`
--
ALTER TABLE `seo_authentication_enterprise`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seo_authentication_personal`
--
ALTER TABLE `seo_authentication_personal`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seo_command`
--
ALTER TABLE `seo_command`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seo_command_item`
--
ALTER TABLE `seo_command_item`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seo_command_template`
--
ALTER TABLE `seo_command_template`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seo_command_template_itme`
--
ALTER TABLE `seo_command_template_itme`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seo_customer`
--
ALTER TABLE `seo_customer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seo_customer_account`
--
ALTER TABLE `seo_customer_account`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seo_customer_account_ibfk_1` (`uid`);

--
-- Indexes for table `seo_customer_info`
--
ALTER TABLE `seo_customer_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seo_fee`
--
ALTER TABLE `seo_fee`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seo_keywords`
--
ALTER TABLE `seo_keywords`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`) USING BTREE,
  ADD KEY `uid_2` (`uid`,`keywords`) USING BTREE;

--
-- Indexes for table `seo_keywords_rank`
--
ALTER TABLE `seo_keywords_rank`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`) USING BTREE,
  ADD KEY `uid_2` (`uid`,`keywords`) USING BTREE;

--
-- Indexes for table `seo_mingxi`
--
ALTER TABLE `seo_mingxi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seo_notice`
--
ALTER TABLE `seo_notice`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seo_notice_log`
--
ALTER TABLE `seo_notice_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seo_order`
--
ALTER TABLE `seo_order`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seo_rank_log`
--
ALTER TABLE `seo_rank_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`taskid`) USING BTREE,
  ADD KEY `uid_2` (`taskid`) USING BTREE;

--
-- Indexes for table `seo_system_config`
--
ALTER TABLE `seo_system_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seo_test`
--
ALTER TABLE `seo_test`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seo_user_group`
--
ALTER TABLE `seo_user_group`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seo_web_url`
--
ALTER TABLE `seo_web_url`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `url` (`url`) USING BTREE;

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `seo_admin_user`
--
ALTER TABLE `seo_admin_user`
  MODIFY `uid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- 使用表AUTO_INCREMENT `seo_command`
--
ALTER TABLE `seo_command`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '订单ID', AUTO_INCREMENT=28;
--
-- 使用表AUTO_INCREMENT `seo_command_item`
--
ALTER TABLE `seo_command_item`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '订单ID';
--
-- 使用表AUTO_INCREMENT `seo_command_template`
--
ALTER TABLE `seo_command_template`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '订单ID', AUTO_INCREMENT=9;
--
-- 使用表AUTO_INCREMENT `seo_command_template_itme`
--
ALTER TABLE `seo_command_template_itme`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '订单ID';
--
-- 使用表AUTO_INCREMENT `seo_customer`
--
ALTER TABLE `seo_customer`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `seo_customer_account`
--
ALTER TABLE `seo_customer_account`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `seo_customer_info`
--
ALTER TABLE `seo_customer_info`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `seo_fee`
--
ALTER TABLE `seo_fee`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `seo_keywords`
--
ALTER TABLE `seo_keywords`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `seo_keywords_rank`
--
ALTER TABLE `seo_keywords_rank`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `seo_mingxi`
--
ALTER TABLE `seo_mingxi`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `seo_notice`
--
ALTER TABLE `seo_notice`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `seo_notice_log`
--
ALTER TABLE `seo_notice_log`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `seo_order`
--
ALTER TABLE `seo_order`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '订单ID';
--
-- 使用表AUTO_INCREMENT `seo_rank_log`
--
ALTER TABLE `seo_rank_log`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `seo_system_config`
--
ALTER TABLE `seo_system_config`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=383;
--
-- 使用表AUTO_INCREMENT `seo_test`
--
ALTER TABLE `seo_test`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `seo_user_group`
--
ALTER TABLE `seo_user_group`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `seo_web_url`
--
ALTER TABLE `seo_web_url`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '网址';
--
-- 限制导出的表
--

--
-- 限制表 `seo_customer_account`
--
ALTER TABLE `seo_customer_account`
  ADD CONSTRAINT `seo_customer_account_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `seo_customer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
