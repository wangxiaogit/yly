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

    /***挂起原因***/
    private $handupReasonList = array(
                                1 => '银行政策调整',
                                2 => '补资料',
                                3 => '客户未面签',
                                4 => '案件量突增，银行积压严重',
                                5 => '客户资料不全',
                                6 => '其它'
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
     * 获取业务类型
     */
    public function getCaseType()
    {
        return $this->caseType;
    }
    
    
    /*
     * 获取案例状态
     */
    public function getConfCaseStatus()
    {
        return $this->caseStatus;
    }

    
    
    
    
    /**
     * 获取数据表名配置
     * @param none
     * @return array 数据表名配置数组
     */
    public function getConfTableList()
    {
        return $this->confTableList;
    }
   
    
    /**
     * 获取非网签类型配置
     * @param none
     * @return array 非网签类型配置数组
     */
    public function getConfLoanFlowType()
    {
        return $this->confLoanFlowType;
    }
    
    /**
     * 获取挂起原因
     * @param none
     * @return array 
     */
    public function getHandupReason()
    {
        return $this->handupReasonList;
    }
    
    
    /**
     * 生成订单号
     * @param none
     * @return int 订单编号
     */
    function buildOrderNo() {
        return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }
    
    
     //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('broker_phone', 'number', '电话号码格式不正确！', AdminModel::VALUE_VALIDATE, 'regex', AdminModel:: MODEL_BOTH ),
        array('id', 'require', '案件编号不存在，请重试！', AdminModel::EXISTS_VALIDATE , 'regex', AdminModel:: MODEL_UPDATE ),
        array('bank_org_id', 'require', '银行总行必须选择！', AdminModel::EXISTS_VALIDATE , 'regex', AdminModel:: MODEL_UPDATE ),
        array('bank_dept_id', 'require', '银行支行必须选择！', AdminModel::EXISTS_VALIDATE , 'regex', AdminModel:: MODEL_UPDATE ),
        array('bank_uid', 'require', '信贷员必须选择！', AdminModel::EXISTS_VALIDATE , 'regex', AdminModel:: MODEL_UPDATE ),
    );
    
    function _before_insert(&$data,$options){
        if(!empty($data['case_tag'])) {
            $data['case_tag'] = implode(',', $data['case_tag']);
        }
        $data['creat_time'] = time();
        $data['case_no'] = $this->buildOrderNo();
        $data['accept_uid'] = session('user_info.uid');
        $data['accept_dept_id'] = session('user_info.dept_id');
        $data['accept_name'] = session('user_info.name');
    }
    
    function _before_update(&$data,$options){
        if(!empty($data['case_tag'])) {
            $data['case_tag'] = implode(',', $data['case_tag']);
        }
        $data['update_time'] = time();
    }
    
    function _after_insert($data, $options) {
        
    }
    
    function _after_update($data, $options) {

    }
}
