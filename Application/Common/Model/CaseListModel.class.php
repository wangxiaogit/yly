<?php
namespace Common\Model;
use Common\Model\AdminModel;

class CaseListModel extends AdminModel
{   
    /*
     * 业务类型
     */
    private  $caseType = array(
        1 => '按揭业务',
        2 => '赎楼业务',
        3 => '提放业务'
    );
    
    
    /*
     * 案例状态
     */
    private $caseStatus = array(
        1 => '正常',
        2 => '挂起',
        3 => '撤案',
        4 => '完成'
    );




    /**
     * 构造函数
     */
    public function __construct() 
    {   
        //设置表名称
//        $table_name = '';
//        $this->trueTableName = strip_tags($table_name);
        parent::__construct();
    }
    
    
    /*
     * 获取收费类型
     */
    public function getCaseType()
    {
        return $this->caseType;
    }
    
    
    /*
     * 获取案例状态
     */
    public function getCaseStatus()
    {
        return $this->caseStatus;
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
