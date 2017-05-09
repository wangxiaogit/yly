<?php
namespace Common\Model;
use Common\Model\AdminModel;

class AuthGroupModel extends AdminModel
{
    //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('name', 'require', '组名称不能为空！', self::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH),
        array('id,name', 'checkGroup', '该权限组已存在！', self::MUST_VALIDATE, 'callback', AdminModel:: MODEL_BOTH)
    );
    
    public function checkGroup ($data) 
    {
        if ($data['id']) {
            $data['id'] = array('neq', $data['id']);
        } else {
            unset($data['id']);
        }
        
        $position = $this->where($data)->find();
        
        return $position ? false : true;
    }
}