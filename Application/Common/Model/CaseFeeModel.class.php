<?php
namespace Common\Model;
use Common\Model\AdminModel;

class CaseFeeModel extends AdminModel
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
//        array('case_id', 'require', '参数异常，请稍后再试！', 0, 'regex', 1),
//        array('transpart', 'require', '参数异常，请稍后再试！', 0, 'regex', 1 ),
//        array('doc', 'empty', '收件资料必须勾选', 0, 'function', 1 )
    );
}
