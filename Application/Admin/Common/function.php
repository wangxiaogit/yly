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

/**
 * 获取对接用户
 * 
 * @param int $handle_type 类型 1 个人 2 组 3 其他
 * @param int $handle_id 
 */
function get_handle_name ($handle_type, $handle_id)
{
    if ($handle_type == 1) {
        return D('User')->where(array("id"=>$handle_id))->getField("true_name");
    }
    else if($handle_type ==2) {
        return D("WorkflowGroup")->where(array("id"=>$handle_id))->getField("name");
    } else {
        return '';
    }
}        