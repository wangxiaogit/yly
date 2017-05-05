<?php
namespace Admin\Model;
use Common\Model\AdminModel;

class MenuModel extends AdminModel
{
    //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('title', 'require', '菜单名称不能为空！', self::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH),
        array('title,url', 'checkAction', '菜单名称或URL已存在！', self::MUST_VALIDATE, 'callback', AdminModel:: MODEL_INSERT),
        array('id,title,url', 'checkActionUpdate', '菜单名称或URL已存在！', self::MUST_VALIDATE, 'callback', AdminModel::MODEL_UPDATE),
    );
    
    public function checkAction($data) 
    {
        $where['title'] = $data['title'];
        
        if ($data['url']) {
            $where['url'] = $data['url'];
            $where['_logic'] = 'OR';
        }
        
        $result = $this->where($where)->find();
        
        return $result ? false : true;
    }
    
    public function checkActionUpdate($data) 
    {
        $where['title'] = $data['title'];
        
        if ($data['url']) {
            $where['url'] = $data['url'];
            $where['_logic'] = 'OR';
        }
        
        $result = $this->where($where)->find();
         
        if ($result && $result['id'] != $data['id']) {
            return true;
        }
        
        return false;
    }
    
}
