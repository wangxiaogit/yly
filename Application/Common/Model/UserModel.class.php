<?php

namespace Common\Model;
use Common\Model\AdminModel;

class UserModel extends AdminModel
{
    //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('org_type', 'require', '机构类型不能为空！', self::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH),
        array('org_id', 'require', '机构名称不能为空！', self::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH),
        array('dept_id', 'require', '部门不能为空！', self::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH),
        array('position_id', 'require', '职位不能为空！', self::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH),
        array('user_name', 'require', '账户不能为空！', self::MUST_VALIDATE, 'regex', AdminModel:: MODEL_INSERT),
        array('user_name', '/^[0-9A-Za-z]{6,20}$/', '账户只能由6到20位数字字母组成！', self::MUST_VALIDATE, 'regex', AdminModel:: MODEL_INSERT),
        array('password', 'require', '部门名称不能为空！', self::MUST_VALIDATE, 'regex', AdminModel:: MODEL_INSERT),
        array('true_name', 'require', '真实姓名不能为空！', self::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH),
        array('phone', 'require', '联系电话不能为空！', self::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH),
        array('group_id', 'require', '权限不能为空！', self::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH),
        array('phone', '/^1[3|4|5|8][0-9]\d{4,8}$/', '联系电话格式错误！', self::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH),
        array('id,user_name', 'checkUser', '账户名称已存在！', self::MUST_VALIDATE, 'callback', AdminModel::MODEL_BOTH),
        array('id,phone', 'checkPhone', '联系电话已存在！', self::MUST_VALIDATE, 'callback', AdminModel::MODEL_BOTH)
    );
    
    //自动完成
    protected $_auto = array(
        array('create_time','time',AdminModel:: MODEL_INSERT,'function'),
        array('password','md5',AdminModel:: MODEL_INSERT,'function')
    );

    public function checkUser($data) 
    {
        $where['user_name'] = $data['user_name'];
        
        if ($data['id']) {
            $where['id'] = array('neq', $data['id']);
        } 
        
        $result = $this->where($where)->find();
        return $result ? false : true;
    }
    
    public function checkPhone($data) 
    {
        $where['phone'] = $data['phone'];
        
        if ($data['id']) {
            $where['id'] = array('neq', $data['id']);
        } 
        
        $result = $this->where($where)->find();
        
        return $result ? false : true;
    }
    
    public function afterLogin($data) 
    {
        //权限组
        $auth_group = M('AuthGroupUser')->where(array('user_id'=>$data['id'], 'status'=>1))->getField("group_id", true); 
        $data['auth_group'] = $auth_group ? $auth_group : array();
        
        //流程组
        $flow_group = D('Common/WorkflowGroupUser')->where(array('user_id'=>$data['id'], 'status'=>1))->getField("group_id", true);
        $data['flow_group'] = $flow_group ? $flow_group : array();
        
        session('userInfo', $data, 24*3600);
        
        $this->where(array('id'=>$data['id']))->save(array('login_time'=>time() ,'login_ip'=> get_client_ip()));
    } 
    
//    public function _before_insert($data, $options) {
//        
//    }
    
//    public function _after_update($data, $options) {
//        
//    }
}
