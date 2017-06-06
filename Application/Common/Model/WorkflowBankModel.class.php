<?php
namespace Common\Model;
use Common\Model\AdminModel;

class WorkflowBankModel extends AdminModel
{
    //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('org_id', 'require', '总行不能为空！', self::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH), 
        array('dept_id', 'require', '支行不能为空！', self::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH), 
        array('handle_type', 'require', '对接类型不能为空！', self::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH), 
        array('handle_id', 'require', '对接类型不能为空！', self::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH), 
        array('id,org_id,dept_id,handle_type,handle_id', 'checkAction', '该行该对接人已存在！', self::MUST_VALIDATE, 'callback', AdminModel:: MODEL_BOTH), 
    );
    
    public function checkAction($data) 
    {
        if ($data['id']) {
            $data['id'] = array('neq', $data['id']);
        } else {
            unset($data['id']);
        }
        $data['status'] = 1;
        
        $result = $this->where($data)->find();
        
        return $result ? false : true;
    }
}
