<?php
namespace Common\Model;
use Common\Model\AdminModel;

class OrganizeModel extends AdminModel
{
    public $ORGANIZE_TYPE = array(
        '1' => '运营公司',
        '2' => '银行',
        '3' => '中介'
    );
    
    //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('type', 'require', '类型不能为空！', self::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH),
        array('name', 'require', '名称不能为空！', self::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH),
        array('id,type,name', 'checkOrganize', '该机构名称已存在！', self::MUST_VALIDATE, 'callback', AdminModel::MODEL_BOTH)
    );
    
    public function checkOrganize($data) 
    {
        if ($data['id']) {
            $data['id'] = array('neq', $data['id']);
        } else {
            unset($data['id']);
        }
         
        $data['status'] = 1;
        
        $organize = $this->where($data)->find();
        
        return $organize ? false : true;
    }
    
}
