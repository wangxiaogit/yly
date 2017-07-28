<?php
namespace Common\Model;
use Common\Model\AdminModel;

class DeptModel extends AdminModel
{
    //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('name', 'require', '部门名称不能为空！', self::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH),
        array('id,org_id,name', 'checkDept', '部门名称已存在！', self::MUST_VALIDATE, 'callback', AdminModel::MODEL_BOTH)
    );
    
    public function checkDept ($data) 
    {
        if ($data['id']) {
            $data['id'] = array('neq', $data['id']);
        } else {
            unset($data['id']);
        }
        $data['status'] = 1;
        
        $dept = $this->where($data)->find();
        
        return $dept ? false : true;
    }
    
    private $dept_id = array();
    
     /*
     * 根据部门编号获取最小层级部门信息
     * @param int $deptId 部门编号
     * @return array 部门数组列表
     */
    public function getChildDeptIdsByDeptid($deptId)
    {
        $childDeptIdarr = array();
        $dept_info = $this->find($deptId);
        $this->dept_id = array();
        if(is_array($dept_info) && !empty($dept_info))
        {
            $allDeptList = M('dept')->where('org_id='.$dept_info['org_id'])->field('id,parentid')->select();
            $childDeptIdarr = self::getChildNode($allDeptList, $dept_info['id']);
        }
        return $childDeptIdarr;
    }
    
    
    private function getChildNode(&$data, $pid)
    {    
        foreach ($data as $key => $value) {
            if($value['parentid'] == $pid) {
                $this->dept_id[] = $value['id'];
                //unset($data[$key]);
                self::getChildNode($data, $value['id']);
            }
        }
        return $this->dept_id;
    }
}
