<?php
return array(
    //页面开启Trace
    'SHOW_PAGE_TRACE' => true,
    
    /*跳转页面*/
    'TMPL_ACTION_ERROR' => MODULE_PATH."View/Public/error.html",
    'TMPL_ACTION_SUCCESS' => MODULE_PATH."View/Public/success.html",

    //模版配置
    'TMPL_PARSE_STRING'    => array(
    	'__STATIC__' => __ROOT__ . '/Public/Static',
    	'__IMG__'    => __ROOT__ . '/Public/' . MODULE_NAME . '/img',
        '__CSS__'    => __ROOT__ . '/Public/' . MODULE_NAME . '/css',
        '__JS__'     => __ROOT__ . '/Public/' . MODULE_NAME . '/js',
        '__THEME__'     => __ROOT__ . '/Public/' . MODULE_NAME . '/theme'
    ),
    
    //语言
    'LANG_SWITCH_ON'    => true,        //开启多语言支持开关
    'DEFAULT_LANG'        => 'zh-cn',    // 默认语言
    'LANG_AUTO_DETECT'    => true,    // 自动侦测语言
);