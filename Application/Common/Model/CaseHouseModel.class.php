<?php
namespace Common\Model;
use Common\Model\AdminModel;

class CaseHouseModel extends AdminModel
{   
    
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

    
     /***贷款类型配置***/
    private $confLoanType = array(
                                1 => '纯商',
                                2 => '市公',
                                3 => '省公',
                                4 => '市组',
                                5 => '省组',
                                6 => '铁公'
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
     /***贷款参数配置***/
    private $_conf_loan = array(
                                'gjj_year' =>  20,
                                'gjj_limit' => 60,
                                'loan_year'=> 30                       
                            );
     /***区属配置***/
    private $_conf_district = array(
                                1 =>  '鼓楼区',
                                2 =>  '白下区',
                                3 =>  '秦淮区',
                                4 =>  '玄武区',
                                5 => '建邺区',
                                6 =>  '雨花台区',
                                7 =>  '栖霞区',
                                8 =>  '浦口区',
                                9 =>  '江宁区',
                                10 =>  '高淳区',
                                11 =>  '溧水区',
                                12 =>  '下关区'
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
     * 获取贷款类型配置
     * @param none
     * @return array 获取贷款类型配置
     */
    public function getLoanType()
    {
        return $this->confLoanType;
    }
   
    
    /**
     * 获取贷款参数配置
     * @param none
     * @return array 贷款参数数组
     */
    public function getConfLoan()
    {
        return $this->_conf_loan;
    }
    
    
    /**
     * 获取区属参数配置
     * @param none
     * @return array 区属参数数组
     */
    public function getConfDistrict()
    {
        return $this->_conf_district;
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
     * 获取学区房配置
     * @param none
     * @return array 学区房配置数组
     */
    public function getConfSchoolHouse()
    {
        return $this->confSchoolHouse;
    }
    
     //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('case_id', 'require', '案件编号不存在，请稍后再试！', AdminModel::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH ),
        array('district', 'require', '房屋区属必须填写！', AdminModel::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH ),
        array('address', 'require', '地址必须填写', AdminModel::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH ),
        array('buildyear', 'number', '建筑年代格式不正确！', AdminModel::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH ),
        array('houseuse', 'require', '房屋用途必须选择！', AdminModel::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH ),
        array('housetype', 'require', '房屋性质必须选择！', AdminModel::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH ),
        array('landtype', 'require', '土地性质必须选择！', AdminModel::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH ),
        array('buildarea', 'currency', '建筑面积格式不正确！', AdminModel::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH ),
        array('total_price', 'currency', '房屋总价格式不正确！', AdminModel::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH ),
    );
    
    
    function _after_insert($data, $options) {
        $this->update_house_info($data);
    }
    
    function _after_update($data, $options) {
        $this->update_house_info($data);
    }
    
    function update_house_info($data)
    {
        $res['district'] = $data['district'];
        $res['location'] = $data['district'].$data['address'];
        $res['loan_type'] = $data['loan_type'];
        $res['sets'] = $data['housenum'];
        $res['loan_amount'] = $data['loan_amount'];
        $res['loan_flow_type'] = $data['loan_flow_type'];
        $res['id'] = $data['case_id'];
        D('Commmon/CaseList')->save($res);
        
    }
}
