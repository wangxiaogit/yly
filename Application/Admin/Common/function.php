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

/*
 * 获取部门列表
 * @param int $type 类型 1公司 2中介 3银行
 */
function get_dept_list ($type = 1)
{
    $dept_list = D('Dept')->join('organize ON organize.id = dept.org_id')
            ->where('type='.$type)->field('dept.id,dept.org_id,dept.name')->select();
    $result = array();
    if ($dept_list) {
       foreach ($dept_list as $key=> $value) {
           $result[$key]['id'] = 'org_'.$value['org_id'].'|'.'dept_'.$value['id'];
           $result[$key]['text'] = $value['name'];
       }
    }
    return $result;
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

/**
 * 获取流程经办人
 */
function get_flow_handle($handle_str, $handle_group_id, $handle_id)
{
    $handle_lists = D('User')->where(array("id"=>array('in',$handle_str)))->getField("true_name", true);
    
    if ($handle_id) {
       return array('handle_name'=> implode(',', $handle_lists));
    }
    
    if ($handle_group_id) {
        $handle_name =  D("Common/WorkflowGroup")->where(array("id"=>$handle_group_id))->getField("name");
        
        return array('handle_name'=> $handle_name, 'handle_lists'=> implode(',', $handle_lists));
    }
    
    return array("handle_name"=>"多用户", 'handle_lists'=> implode(',', $handle_lists));
}        

/**
 * 用户部门
 */
function get_user_dept ($user_id) 
{
    $dept_id = D('Common/User')->where(array("id"=>$user_id))->getField("dept_id");
    
    return $dept_id;
}

/*
 * 案例标签
 */
function get_case_tag_text ($tag)
{   
    $tag_arr = array_filter(explode(',', $tag));
    $result = '';
    if (!empty($tag_arr)) {
        $where['id'] = array('IN', $tag_arr);
        $tag_list = M('CaseTag')->where($where)->field('tag_show,tag_color')->select();
        $result = '';
        foreach($tag_list as $vo) 
        {
            $result .= "<span class='btn_dingzhi ".$vo['tag_color']."'>".$vo['tag_show'].'</span>';
        }    
    }
    return $result;
}


/*
 * 案例详情tab
 */
function  get_case_info_tabs()
{
    return array(
        'CasePerson/index' => '交易人',
        'CaseHouse/index' => '房屋信息',
        'CaseList/bankInfo' => '银行匹配',
        'CaseDocument/index' => '收件列表',
        'CaseFee/index' => '收费记录',
        'CaseRemark/index' => '备注信息',
        'CaseAdvanceLoan/index' => '垫资信息'
    );
}


/*
 * 获取性别和生日
 */
function get_sex_birthday($idcard)
{   
    $birth = strlen($idcard)==15 ? ('19' . substr($idcard, 6, 6)) : substr($idcard, 6, 8);
    $sex = substr($idcard, (strlen($idcard)==15 ? -2 : -1), 1) % 2 ? '1' : '0'; //1为男 2为女
    return array(
        'birth'=> $birth,
        'sex'  => $sex
    );
}