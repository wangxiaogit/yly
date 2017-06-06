<?php
namespace Common\Model;
use Common\Model\AdminModel;

class WorkflowTypeModel extends AdminModel
{
    //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('name', 'require', '类型名称不能为空！', self::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH), 
        array('id,name', 'checkAction', '类型名称已存在！', self::MUST_VALIDATE, 'callback', AdminModel:: MODEL_BOTH), 
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
