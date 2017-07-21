<?php
namespace Common\Model;
use Common\Model\AdminModel;

class WorkflowConfModel extends AdminModel
{
    //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('workflow_type_id', 'require', '流程类型不能为空！', self::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH), 
        array('workflow_version_id', 'require', '流程版本不能为空！', self::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH), 
        array('workflow_node_id', 'require', '流程节点不能为空！', self::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH), 
        array('step', 'require', '步骤不能为空！', self::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH), 
        array('handle_type', 'require', '对接类型不能为空！', self::MUST_VALIDATE, 'regex', AdminModel:: MODEL_BOTH), 
        array('id,workflow_type_id,workflow_version_id,workflow_node_id', 'checkAction', '该节点流程配置已存在！', self::MUST_VALIDATE, 'callback', AdminModel:: MODEL_BOTH), 
    );
    
    public function checkAction($data) 
    {
        if ($data['id']) {
            $data['id'] = array('neq', $data['id']);
        } else {
            unset($data['id']);
        }
        $data['status'] = 1;
        
        $result = $this->where($data)->find();
        
        return $result ? false : true;
    }
    
    /**
     * 流程配置
     */
    public function getFlowConf ($flowType_id, $flowVersion_id, $step, $case_id=0, $flow_id = 0) 
    {
        $map = array(
            'workflow_type_id' => $flowType_id,
            'workflow_version_id' => $flowVersion_id,
            'step' => $step,
            'status' => 1
        );
        
        $workflow_conf  = $this->where($map)->find();
        
        if (!$workflow_conf) return;
        
        $workflow_config = $this->getFlowConfHandle($workflow_conf, $case_id, $flow_id);
        
        return $workflow_config;
    }
    
    /**
     * 配置人员
     */
    public function getFlowConfHandle ($data, $case_id, $flow_id)
    {
        $handle_type = $data['handle_type'];
        
        if ($handle_type == 1) {
            
            $handle_str = $data['handle_id'];  
        } 
        elseif ($handle_type ==  2) {
            
            $flowGroup_id = $data['handle_id'];
            
            if ($flowGroup_id) {
                
                $hanlde_array = D('Common/WorkflowGroupUser')->where(array('status'=>1, 'group_id'=>$flowGroup_id))->gerField("user_id", true);
            
                if ($hanlde_array) {

                    $data['handle_id'] = 0;

                    $handle_str = implode(",", $hanlde_array);    
                }   
            }
             
        }
        elseif ($handle_type ==3) {
            
            $handle_sql = $data['handle_sql'];
            
            if (strstr($handle_sql, "%dept_id%")) {
                $handle_sql = str_replace("%dept_id%", session('userInfo.dept_id'), $handle_sql);
            }
            
            if (strstr($handle_sql, "%user_id%")) {
                $handle_sql = str_replace("%user_id%", session('userInfo.id'), $handle_sql);
            }
            
            if (strstr($handle_sql, "%case_id%")) {
                $handle_sql = str_replace("%case_id%", $case_id, $handle_sql);
            }
            
            if (strstr($handle_sql, "%flow_id%")) {
                $handle_sql = str_replace("%flow_id%", $flow_id, $handle_sql);
            }
            
            if ($handle_sql) {
                
                $handle_array = M()->query($handle_sql);
            
                if (count($handle_array) == 1) {

                    $data['handle_id'] = $handle_str = $handle_array[0]['handle_id'];   
                } 
                elseif (count($handle_array > 1)) {

                    $handle_str = impode(",", array_column($handle_array, 'handle_id'));  
                } 
            }    
        }
        
        $data['handle_str'] = isset($handle_str) ? $handle_str : '';
        
        return $data;
    }        
}
