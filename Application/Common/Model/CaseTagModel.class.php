<?php
namespace Common\Model;
use Common\Model\AdminModel;

class CaseTagModel extends AdminModel
{
    private  $colorType = array(
        'red1' => '红色1',
        'red2' => '红色2',
        'orange1' => '橙色1',
        'orange2' => '橙色2',
        'blue1' => '蓝色1',
        'blue2' => '蓝色2',
        'purple' => '紫色',
        'gray' => '青色'
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
    public function getColorType()
    {
        return $this->colorType;
    }        
    
     //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('tag_name', 'require', '标签名称不能为空！', AdminModel::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH ),
        array('tag_show', 'require', '标签展示字样不能为空！', AdminModel::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH ) 
    );
    
    function  getCaseTag()
    {
        $list = $this->where('isvaild=1')->getField('id,tag_name');
        return $list;
    }
}
