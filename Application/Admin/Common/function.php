<?php

/**
 * 获取菜单深度
 */
function _get_level ($id, $array, $i = 0) 
{
    if ($array[$id]['parentid']==0 || empty($array[$array[$id]['parentid']]) || $array[$id]['parentid']==$id){
        return  $i;
    }else{
        $i++;
        return _get_level($array[$id]['parentid'],$array,$i);
    }
}