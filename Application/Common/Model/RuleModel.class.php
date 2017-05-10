<?php
namespace Common\Model;
use Common\Model\AdminModel;

class RuleModel extends AdminModel
{
    //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('title', 'require', '节点名称不能为空！', self::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH),
        array('id,title,url', 'checkAction', '节点名称或URL已存在！', self::MUST_VALIDATE, 'callback', AdminModel:: MODEL_INSERT)   
    );
    
    public function checkAction($data) 
    {
        if ($data['id']) {
            $where['id'] = array('neq', $data['id']);
        }
        
        $where['_string'] = $data['url'] ? '(title eq "'.$data['title'].'") OR (url eq "'.$data['url'].'")' :  '(title eq "'.$data['title'].'")';
        
        $result = $this->where($where)->find();
        
        return $result ? false : true;
    }
}
