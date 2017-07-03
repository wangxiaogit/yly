<?php
namespace Common\Model;
use Common\Model\AdminModel;

class CaseAdvanceLoanModel extends AdminModel
{   
    
    private $cooperative_type = array(
        1 => '自有资金',
        2 => '银行',
        3 => '合作单位',
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
    
    function getCooperativeType() {
        return $this->cooperative_type;
    }


    //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('case_id', 'require', '案件编号不存在，请稍后再试！', AdminModel::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH ),
        array('house_total_price', 'number', '房屋总价填写不正确！', 1, 'regex', AdminModel:: MODEL_BOTH ),
        array('house_down_payment', 'number', '尾款填写不正确！', 1, 'regex', AdminModel:: MODEL_BOTH ),
        array('house_balance_due', 'number', '首付金额填写不正确！', 1, 'regex', AdminModel:: MODEL_BOTH ),
        array('advance_money', 'number', '垫资金额填写不正确！', 1, 'regex', AdminModel:: MODEL_BOTH ),
    );
    
    
}
