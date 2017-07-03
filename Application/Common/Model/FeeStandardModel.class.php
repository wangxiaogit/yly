<?php
namespace Common\Model;
use Common\Model\AdminModel;

class FeeStandardModel extends AdminModel
{
    private  $feeType = array(
        1 => '基础收费',
        2 => '疑难收费',
        3 => '资金业务',
        4 => '其他业务'
    );
    
    private  $payMode = array(
        1 => '现金',
        2 => 'POS机',
        3 => '银行转账'
    );   
     
    
    private  $paymentType = array(
        1 => '中介返费',
        2 => '经纪人返费',
        3 => '材料费用',
        4 => '营销费用',
        5 => ''
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
    public function getFeeType()
    {
        return $this->feeType;
    }        
    
    
    /*
     * 获取收费类型
     */
    public function getPayMode()
    {
        return $this->payMode;
    }
    
    
    /*
     * 获取收费类型
     */
    public function getPaymentType()
    {
        return $this->paymentType;
    }
    
    
     //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('fee_name', 'require', '费用名称不能为空！', AdminModel::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH ),
        array('fee', 'require', '费用金额不能为空！', AdminModel::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH ),
        array('fee', 'currency', '费用金额数值填写不正确！', AdminModel::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH ),
    );
    
    //自动完成
    protected $_auto = array(
        //array(完成字段1,完成规则,[完成条件,附加规则])
        array('org_id', 'return_org_id', AdminModel::MODEL_BOTH, 'callback')
    );
    
    public function return_org_id() {
        return $_SESSION['org_id']?$_SESSION['org_id']:1;
    }
    
}
