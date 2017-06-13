<?php
namespace Common\Model;
use Common\Model\AdminModel;

class CasePersonModel extends AdminModel
{   

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
     * 获取工作单位类型配置
     * @param none
     * @return array 工作单位类型数组
     */
    public function getCompanyType()
    {
        return $this->confCompanyType;
    }
    
    
    
     //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('case_id', 'require', '案件编号不存在，请稍后再试！', AdminModel::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH ),
        array('name', 'require', '姓名必须填写！', AdminModel::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH ),
        array('telno', 'number', '电话号码格式不正确！', AdminModel::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH ),
        array('card_no', 'require', '证件号码必须填写！', AdminModel::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH ),
        array('id', 'require', '交易人员不存在，请重试！', AdminModel::MUST_VALIDATE, 'regex', AdminModel:: MODEL_UPDATE ),
    );
    
    function _before_insert(&$data,$options){
        $res = get_sex_birthday($data['card_no']);
        $data['sex'] = $res['sex'];
        $data['birthday'] = substr($res['birth'], 0, 4).'-'.substr($res['birth'], 4, 2).'-'.substr($res['birth'], 6, 2);
    }
    
    function _before_update(&$data,$options){
        $res = get_sex_birthday($data['card_no']);
        $data['sex'] = $res['sex'];
        $data['birthday'] = substr($res['birth'], 0, 4).'-'.substr($res['birth'], 4, 2).'-'.substr($res['birth'], 6, 2);
    }
    
    function _after_insert($data, $options) {
        $this->update_debtor_seller($data['case_id'], $data);
    }
    
    function _after_update($data, $options) {
        $this->update_debtor_seller($data['case_id'], $data);
    }
    
    function  update_debtor_seller($case_id ,$data)
    {   
        $model = M('CaseList');
        $case_info = $model->find($case_id);
        //买方
        if ($data['is_main_loan'] && $data['name']!=$case_info['debtor'] && $data['right_type'] == 1) {
           $model -> where('id='.$case_id) ->setField('debtor',$data['name']);
        }
        //卖方
        if ($data['right_type']== 2 && strpos($data['name'], $case_info['seller'])=== false)
        {
            $model -> where('id='.$case_id) ->setField('seller',','.$data['name']);
        }        
        
    }
    
}
