<?php
namespace Admin\Model;
use Common\Model\AdminModel;

class OrganizeModel extends AdminModel
{
    public $ORGANIZE_TYPE = array(
        '1' => '运营公司',
        '2' => '银行',
        '3' => '中介',
        '4' => '保险公司'
    );
    
    //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('title', 'require', '菜单名称不能为空！', self::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH),
        array('title,url', 'checkAction', '菜单名称或URL已存在！', self::MUST_VALIDATE, 'callback', AdminModel:: MODEL_INSERT),
        array('id,title,url', 'checkActionUpdate', '菜单名称或URL已存在！', self::MUST_VALIDATE, 'callback', AdminModel::MODEL_UPDATE),
    );
    
}
