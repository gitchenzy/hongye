<?php
return array(
	//'配置项'=>'配置值'
    'MULTI_MODULE'          =>  true,
    'MODULE_ALLOW_LIST'   => array('Admin','Home'),
    'DEFAULT_MODULE'        =>  'Home',  // 默认模块
  //  'MODULE_DENY_LIST'   => array('Home'),


    /* 数据库配置 */
    'DB_DEPLOY_TYPE' => 1,
    'DB_TYPE'   => 'mysqli', // 数据库类型
    'DB_HOST'   => 'localhost', // 服务器地址
    'DB_NAME'   => 'hongye', // 数据库名
    'DB_USER'   => 'root', // 用户名
    'DB_PWD'    => 'root',  // 密码
    'DB_PORT'   => '3306', // 端口
    //'DB_PREFIX' => '', // 数据库表前缀

    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__PUBLIC__' => __ROOT__ . '/Public',
    ),

    'LOG_RECORD' => true, // 开启日志记录
    'LOG_LEVEL'  =>'EMERG,ALERT,CRIT,ERR',
    'LOG_FILE_SIZE' => 1024 * 1024 * 256 ,


);