<?php
namespace Common\Model;
use Common\Model\AdminModel;

class PositionModel extends AdminModel
{
    //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('name', 'require', '职位名称不能为空！', self::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH),
        array('id,name', 'checkPosition', '该职位已存在！', self::MUST_VALIDATE, 'callback', AdminModel:: MODEL_BOTH)
    );
    
    public function checkPosition ($data) 
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
