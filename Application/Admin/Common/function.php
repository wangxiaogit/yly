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

/**
 * 获取机构类型 1 运营公司 2 银行 3 中介
 */
function get_organize_type ($org_id)
{
    return D('Common/Organize')->where(array('id'=>$org_id))->getField('type');
}
