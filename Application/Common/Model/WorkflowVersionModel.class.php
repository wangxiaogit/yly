<?php
namespace Common\Model;
use Common\Model\AdminModel;

class WorkflowVersionModel extends AdminModel
{
    //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('workflow_type_id', 'require', '流程类型不能为空！', self::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH), 
        array('id,workflow_type_id,version', 'checkAction', '流程版本已存在！', self::MUST_VALIDATE, 'callback', AdminModel:: MODEL_BOTH), 
    );
    
    public function checkAction($data) 
    {
        if ($data['id']) {
            $data['id'] = array('neq', $data['id']);
        } else {
            unset($data['id']);
        }
        
        $result = $this->where($data)->find();
        
        return $result ? false : true;
    }
}
