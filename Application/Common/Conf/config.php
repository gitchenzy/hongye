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
  //  'DB_PWD'    => 'root',  // 密码
    
    'DB_PORT'   => '3306', // 端口
    //'DB_PREFIX' => '', // 数据库表前缀

    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__PUBLIC__' => __ROOT__ . '/public',
    ),

    'LOG_RECORD' => true, // 开启日志记录
    'LOG_LEVEL'  =>'EMERG,ALERT,CRIT,ERR',
    'LOG_FILE_SIZE' => 1024 * 1024 * 256 ,

    /* 图片上传相关配置 */
    'PICTURE_UPLOAD' => array(
        'mimes'    => '', //允许上传的文件MiMe类型
        'maxSize'  => 1*1024*1024, //上传的文件大小限制 (0-不做限制)
        'exts'     => 'jpg,gif,png,jpeg', //允许上传的文件后缀
        'autoSub'  => true, //自动子目录保存文件
        'subName'  => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './uploads/picture/', //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt'  => '', //文件保存后缀，空则使用原后缀
        'replace'  => false, //存在同名是否覆盖
        'hash'     => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
    ), //图片上传相关配置（文件上传类配置）

    'UPLOAD_LOCAL_CONFIG'=>array(),
);