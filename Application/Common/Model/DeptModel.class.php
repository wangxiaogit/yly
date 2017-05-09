<?php
namespace Common\Model;
use Common\Model\AdminModel;

class DeptModel extends AdminModel
{
    //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('name', 'require', '部门名称不能为空！', self::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH),
        array('id,org_id,name', 'checkDept', '部门名称已存在！', self::MUST_VALIDATE, 'callback', AdminModel::MODEL_BOTH)
    );
    
    public function checkDept ($data) 
    {
        if ($data['id']) {
            $data['id'] = array('neq', $data['id']);
        } else {
            unset($data['id']);
        }
        
        $dept = $this->where($data)->find();
        
        return $dept ? false : true;
    }
    
}
