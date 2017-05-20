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
      private $confAddType = array(
                                1 => '交易人',
                                2 => '房屋信息',
                                4 => '银行匹配',
                                5 => '收件列表',
                                6 => '收费记录',
                                7 => '节点日期',
                                8 => '备注信息',
                                9 => '赎楼',
                                10 => '提放',
                                11 => '工作流'
                            );
    
    /***企业类型配置***/
    private $confCompanyType = array(
                                1 => '公务员',
                                2 => '事业单位',
                                3 => '上市公司',
                                4 => '私营企业',
                                5 => '外资企业',
                                6 => '个体工商户',
                                7 => '其他'
                            );
    
    /***权属类型配置***/
    private $confRightType = array(
                                1 => '买方',
                                2 => '卖方',
                                4 => '共同还款人',
                                5 => '抵押人'
                            );
    
    /***权属类型配置***/
    private $confPersonType = array(
                                1 => '自然人',
                                2 => '法人',                             
                            );
    
    /***套数配置***/
    private $confHouseSet = array(
                                1 => '首套房',
                                2 => '二套房',
                                3 => '三套房',
                                4 => '三套以上房',
                            );    
    
    
     /***房屋性质配置***/
    private $confHouseType = array(
                                1 => '私有',               
                                2 => '商品房',    
                                3 => '房改房',
                                4 => '集资房', 
                                5 => '经济适用住房',
                                6 => '福利房',
                                7 => '使用权房',
                                8 => '限价房',
                                9 => '存量房',
                            );
    
     /***土地性质类型配置***/
    private $confLandType= array(
                                1 => '出让',
                                2 => '划拨',                             
                            );

      /***房屋用途配置***/
    private $confHouseUse= array(
                                1 => '住宅',                            
                                2 => '办公',    
                            );
    
    
    /***收件双方配置***/
    private $confTranSpart = array(
                                1 => '买方',
                                2 => '卖方',
                            );
    
    
     /***贷款类型配置***/
    private $confLoanType = array(
                                1 => '纯商',
                                2 => '市公',
                                3 => '省公',
                                4 => '市组',
                                5 => '省组',
                                6 => '铁公'
                            );

   
    /***案例状态***/
    private $confCaseStatusStr = array(
                                'normal' => 1,    //正常
                                'changing' => 2,   //整改
                                'pending' => 3,    //挂起
                                'revoking' => 4,   //申请撤案
                                'revoked' => 5,     //撤案
                                'completed' => 6    //完成
                            );
    
    /***学区房定义***/
    private $confSchoolHouse = array(
                                0 => '非学区房',
                                1 => '一般学区房',
                                2 => '优质学区房',                            
                            );
    
    
    /***非网签类型定义***/
    private $confLoanFlowType = array(
                                1 => '非网签预审批',
                                2 => '非网签出证审批',
                                3 => '网签审批'
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
     * 获取新增案例标签配置
     * @param none
     * @return array 新增案例标签数组
     */
    public function getConfAddType()
    {
        return $this->confAddType;
    }
    
    
    /**
     * 获取工作单位类型配置
     * @param none
     * @return array 工作单位类型数组
     */
    public function getCompanyType()
    {
        return $this->confCompanyType;
    }
    
    
    /**
     * 获取权属类型配置
     * @param none
     * @return array 权属类型数组
     */
    public function getRightType()
    {
        return $this->confRightType;
    }
    
    
    /**
     * 获取人物类型配置
     * @param none
     * @return array 人物类型数组
     */
    public function getPersonType()
    {
        return $this->confPersonType;
    }
    
    
    /**
     * 获取房屋套数配置
     * @param none
     * @return array 房屋套数配置
     */
    public function getHouseSet()
    {
        return $this->confHouseSet;
    }
    
    
    /**
     * 获取房屋性质配置
     * @param none
     * @return array 房屋性质配置
     */
    public function getHouseType()
    {
        return $this->confHouseType;
    }
    
    
    /**
     * 获取土地性质配置
     * @param none
     * @return array 土地性质配置
     */
    public function getLandType()
    {
        return $this->confLandType;
    }
    
    
    /**
     * 获取房屋用途配置
     * @param none
     * @return array 房屋用途配置
     */
    public function getHouseUse()
    {
        return $this->confHouseUse;
    }
    
    
    /**
     * 获取买/卖配置
     * @param none
     * @return array 获取买/卖配置
     */
    public function getTranSpart()
    {
        return $this->confTranSpart;
    }
    
    
    /**
     * 获取贷款类型配置
     * @param none
     * @return array 获取贷款类型配置
     */
    public function getLoanType()
    {
        return $this->confLoanType;
    }
    
    
    /**
     * 获取案例状态字符串映射配置
     * @param none
     * @return array 案例状态字符串映射配置
     */
    public function getConfCaseStatusStr()
    {
        return $this->confCaseStatusStr;
    }
    
    
    /**
     * 获取学区房配置
     * @param none
     * @return array 学区房配置数组
     */
    public function getConfSchoolHouse()
    {
        return $this->confSchoolHouse;
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
