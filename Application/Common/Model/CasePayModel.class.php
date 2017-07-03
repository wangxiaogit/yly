<?php
namespace Common\Model;
use Common\Model\AdminModel;

class CasePayModel extends AdminModel
{   
  

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
    
    
     //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('fee', 'require', '缴费金额必须填写！', 1, 'regex', AdminModel:: MODEL_BOTH ),
    );
    
    function _before_insert(&$data, $options) {
        $data['pay_date'] = time();
    }
}
