<?php
namespace Common\Model;
use Common\Model\AdminModel;

class RequiredDocumentModel extends AdminModel
{
    private  $documentType = array(
        1 => '基础资料',
        2 => '收入证明',
        3 => '垫资资料',
        4 => '企业资料'
    );
    
     /**
     * 构造函数
     */
    public function __construct() 
    {   
        //设置表名称
        //parent::setTableName('fee_standard');
        parent::__construct();
    }
    
    
    /*
     * 获取收费类型
     */
    public function getDocumentType()
    {
        return $this->documentType;
    }        
    
     //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('document_name', 'require', '资料名称不能为空！', AdminModel::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH )
    );
    
    //自动完成
    protected $_auto = array(
        //array(完成字段1,完成规则,[完成条件,附加规则])
        array('create_time', 'time', AdminModel::MODEL_BOTH, 'function')
    );
    
    public function return_org_id() {
        return $_SESSION['org_id']?$_SESSION['org_id']:1;
    }
    
}
