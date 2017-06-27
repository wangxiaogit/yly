<?php

/**
 * 登陆
 */
function is_login() 
{
    return session('userInfo.id') ? session('userInfo.id') : false;
}

/**
 * 管理员
 */
function is_administrator ($uid=0)
{
    $current_id = $uid ? $uid : is_login();
    
    return ($current_id==1) ? true : false;
} 

/**
 * 验证码
 */
function check_verify($code, $id = 1){
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}

