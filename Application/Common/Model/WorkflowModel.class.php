<?php
namespace Common\Model;
use Common\Model\AdminModel;

class WorkflowModel extends AdminModel 
{
    protected  $commonModel; 
    protected  $user; //当前用户
    protected  $time; //当前时间
    
    public function __construct() 
    {    
        parent::__construct();
        
        $this->commonModel = M();
        
        $this->user = session('userInfo.id');
        $this->time = time();
    }
    
    /**
     * 检查流程版本.流程配置
     */
    public function flowCreateAuth ($flowType_id) 
    {
        //流程版本
        $flowVersion = D('Common/WorkflowVersion')->where(array('status'=>1, 'is_active'=>1, 'workflow_type_id'=>$flowType_id))->select();
        
        if (count($flowVersion) != 1) {
            return array('status'=>0, 'msg'=>'流程版本出错, 请联系管理员！');
        }
        
        $flowVersion_id = $flowVersion[0]['id'];
        
        //流程配置
        $flowConf = D('Common/WorkflowConf')->where(array('status'=>1, 'workflow_type_id'=>$flowType_id, 'workflow_version_id'=>$flowVersion_id))->count();
        if (!$flowConf) {
            return array('status'=>0, 'msg'=>'流程配置出错, 请联系管理员！');           
        }
        
        return array('status'=>1, 'flowVersion_id'=>$flowVersion_id);;
    }
    
    /**
     * 流程INFO 
     */
    public function flowInfoBulid ($flowType_id, $case_id, $case_type_id, $record_id) 
    {
        $info = '';
        
        $caseInfo = D('Common/CaseList')->field("id, case_no, debtor")->find($case_id);

        if ($caseInfo) {
            $info .= "编号".$caseInfo['case_no'].".主贷人".$caseInfo['debtor'];
        }
        
        $flowType_name = D('Common/WorkflowType')->where(array("id"=>$flowType_id))->getField("name");
        
        if (strpos($flowType_name, '工作流') || strpos($flowType_name, '流程')) {
            $info .= "的".$flowType_name;
        } else {
            $info .= "的".$flowType_name."流程";
        }
        
        return $info;
    }
    
    /**
     * 流程节点限制
     */
    public function flowNodeLimit ($action, $flowType_id, $case_id, $case_type_id, $record_id) 
    {
        return array('status'=>1, 'msg'=>'Success！');
    }
    
    /**
     * 流程新建
     */
    public function createWorkflow ($data) 
    {
        $workflow_conf_first = D('Common/WorkflowConf')->getFlowConf($data['flow_type_id'], $data['flow_version_id'], 1, $data['case_id']);
        
        $workflow_conf_second = D('Common/WorkflowConf')->getFlowConf($data['flow_type_id'], $data['flow_version_id'], 2, $data['case_id']);
        
        if (!$workflow_conf_first || !$workflow_conf_second || !$workflow_conf_second['handle_str']) {
            return array('state'=> 0, 'msg'=> '流程配置出错,请联系管理员！');
        }
        
        $this->commonModel->startTrans();
        
        $workflow = array(
            'case_id' => isset($data['case_id']) ? intval($data['case_id']) : 0,
            'case_type_id' => isset($data['case_type_id']) ? intval($data['case_type_id']) : 0,
            'record_id' => isset($data['record_id']) ? intval($data['record_id']) : 0,
            'type_id' => $data['flow_type_id'],
            'version_id' => $data['flow_version_id'],
            'info' => $data['info'],
            'create_uid' => $this->user,
            'create_time' => $this->time,
            'org_id' => session('userInfo.org_id'),
            'step' => 2,
            'category' => isset($data['category']) ? $data['category'] : 0  
        );
        $flow_id = $this->add($workflow);

        $workflow_step_first = array(
            'flow_id' => $flow_id,
            'node_id' => $workflow_conf_first['workflow_node_id'],
            'conf_id' => $workflow_conf_first['id'],
            'handle_str' => $this->user,
            'take_uid' => $this->user,
            'submit_time' => $this->time,
            'opinion' => trim($data['opinion']),
            'status' => 3
        );
        $workflow_step_first_id = M('WorkflowStep')->add($workflow_step_first);
        
        $workflow_step_second = array(
            'flow_id' => $flow_id,
            'node_id' => $workflow_conf_second['workflow_node_id'],
            'conf_id' => $workflow_conf_second['id'],
            'handle_str' => $workflow_conf_second['handle_str'],
            'handle_group_id' => ($workflow_conf_second['handle_type'] == 2) ? $workflow_conf_second['handle_id'] : 0,
            'take_uid' => $workflow_conf_second['handle_id'] ? $workflow_conf_second['handle_id'] : 0,
            'accept_time' => $this->time,
            'opinion' => trim($data['opinion']),
            'status' => 1
        );
        $workflow_step_second_id = M('WorkflowStep')->add($workflow_step_second);
        
        $updateData = array(
            'wfid' => $flow_id,
            'wftype' => $data['flow_type_id'],
            'wfnode' => $workflow_conf_second['workflow_node_id'],
            'step_id' => $workflow_step_second_id,
            'handle_str' => $workflow_conf_second['handle_str'],
            'handle_group_id' => ($workflow_conf_second['handle_type'] == 2) ? $workflow_conf_second['handle_id'] : 0,
            'handle_uid' => $workflow_conf_second['handle_id'] ? $workflow_conf_second['handle_id'] : 0,
            'dept_id' => $workflow_conf_second['handle_id'] ? get_user_dept($workflow_conf_second['handle_id']): 0,
            'handle_time' => $this->time
        );
        $update = $this->updatedRelatedRecords('create', $flow_id, $updateData);
        
        if (!$flow_id || !$workflow_step_first_id || !$workflow_step_second_id || !$update) {
            
            $this->commonModel->rollback();
            
            return array('status'=>0 , 'msg'=> '创建失败！');
        } else {
            
            $this->commonModel->commit();
            
            $reponse = $this->flowResponse('create', $workflow_step_second['handle_str']);
            
            return array('status'=>1, 'msg'=> $reponse);
        }
        
    }
    
    /**
     * 流程PASS
     */
    public function passWorkflow ($data) 
    {
        $flow_id = $data['flow_id'];
        
        $flowInfo = $this->find($flow_id);
        if (!$flowInfo) {
            return array('status'=> 0, 'msg'=> '流程不存在,请联系管理员！');
        }
        
        $workflow_conf_next = D('Common/WorkflowConf')->getFlowConf($flowInfo['type_id'], $flowInfo['version_id'], $flowInfo['step']+1, $flowInfo['case_id'], $flow_id);
        if (!$workflow_conf_next || !$workflow_conf_next['handle_str']) {
            return array('status'=> 0, 'msg'=> '流程配置出错,请联系管理员！');
        }
        
        $this->commonModel->startTrans();
        
        $workflow_update = array(
            'step' => $workflow_conf_next['step']
        );
        $workflow_is_update = $this->where(array("id"=>$flow_id))->save($workflow_update);
        
        $workflow_step_update = array(
            'submit_time' => $this->time,
            'opinion' => trim($data['opinion']),
            'status' => 3
        );
        $workflow_step_is_update = M('WorkflowStep')->where(array("flow_id"=>$flow_id, 'status'=>2))->save($workflow_step_update);
        
        $workflow_step_insert = array(
            'flow_id' => $flow_id,
            'node_id' => $workflow_conf_next['workflow_node_id'],
            'conf_id' => $workflow_conf_next['id'],
            'handle_str' => $workflow_conf_next['handle_str'],
            'handle_group_id' => ($workflow_conf_next['handle_type'] == 2) ? $workflow_conf_next['handle_id'] : 0,
            'take_uid' => $workflow_conf_next['handle_id'] ? $workflow_conf_next['handle_id'] : 0,
            'accept_time' => $this->time,
            'status' => 1
        );
        $workflow_step_insert_id = M('WorkflowStep')->add($workflow_step_insert);
        
        $updateData = array(
            'wfnode' => $workflow_conf_next['workflow_node_id'],
            'step_id' => $workflow_step_insert_id,
            'handle_str' => $workflow_conf_next['handle_str'],
            'handle_group_id' => ($workflow_conf_next['handle_type'] == 2) ? $workflow_conf_next['handle_id'] : 0,
            'handle_uid' => $workflow_conf_next['handle_id'] ? $workflow_conf_next['handle_id'] : 0,
            'dept_id' => $workflow_conf_next['handle_id'] ? get_user_dept($workflow_conf_next['handle_id']): 0,
            'handle_time' => $this->time
        );
        $update = $this->updatedRelatedRecords('pass', $flow_id, $updateData);
        
        if (!$workflow_is_update || !$workflow_step_is_update || !$workflow_step_insert_id || !$update) {
            
            $this->commonModel->rollback();
            
            return array('status'=>0 , 'msg'=> '办理失败！');
        } else {
            $this->commonModel->commit();
            
            $reponse = $this->flowResponse('pass', $workflow_conf_next['handle_str']);
            
            return array('status'=>1, 'msg'=> $reponse);
        }
    }
    
    /**
     * 流程BACK
     */
    public function backWorkflow ($data) 
    {
        $flow_id = $data['flow_id'];
        
        $flowInfo = $this->find($flow_id);
        if (!$flowInfo) {
            return array('status'=> 0, 'msg'=> '流程不存在,请联系管理员！');
        }
        
        $conf_id = $data['flow_conf_id'];
        
        $workflow_step = D('Common/WorkflowConf')->where(array("id"=>$conf_id))->getField("step");
        
        $workflow_update = array(
            'step' => $workflow_step
        ); 
        $workflow_is_update = $this->where(array("id"=>$flow_id))->save($workflow_update);
        
        $workflow_step_back = M('WorkflowStep')->where(array('flow_id'=>$flow_id, 'conf_id'=>$conf_id, 'isvalid'=>1))->find();
        
        $workflow_step_current_id = M('WorkflowStep')->where(array('flow_id'=>$flow_id, 'status'=>2))->getField('id');
        
        $workflow_step_isvalid = array(
            'isvalid' => -1
        );
        $workflow_step_update_isvalid = M('WorkflowStep')->where(array('flow_id'=>$flow_id, 'id'=>array('egt', $workflow_step_back['id']), 'id'=>array('elt', $workflow_step_current_id), 'isvalid'=>1))->save($workflow_step_isvalid);
        
        $workflow_step_update = array(
            'isback' => 1,
            'status' => 3,
            'opinion' => trim($data['opinion']),
            'submit_time' => $this->time
        );
        $workflow_step_is_update =  M('WorkflowStep')->where(array('flow_id'=>$flow_id, 'status'=>2))->save($workflow_step_update);
        
        $workflow_step_insert = array(
            'flow_id' => $flow_id,
            'node_id' => $workflow_step_back['node_id'],
            'conf_id' => $workflow_step_back['conf_id'],
            'handle_str' => $workflow_step_back['take_uid'],
            'take_uid' => $workflow_step_back['take_uid'],
            'accept_time' => $this->time,
            'status' => 1
        );
        $workflow_step_insert_id = M('WorkflowStep')->add($workflow_step_insert);
        
        $updateData = array(
            'wfnode' => $workflow_step_back['node_id'],
            'step_id' => $workflow_step_insert_id,
            'handle_str' => $workflow_step_back['take_uid'],
            'handle_group_id' => 0,
            'handle_uid' => $workflow_step_back['take_uid'],
            'dept_id' => get_user_dept($workflow_step_back['take_uid']),
            'handle_time' => $this->time
        );
        $update = $this->updatedRelatedRecords('back', $flow_id, $updateData);
        
        if (!$workflow_is_update ||!$workflow_step_update_isvalid || !$workflow_step_is_update || !$workflow_step_insert_id || !$update) {
            
            $this->commonModel->rollback();
            
            return array('status'=>0 , 'msg'=> '回退失败！');
        } else {
            $this->commonModel->commit();
            
            $reponse = $this->flowResponse('back', $workflow_step_back['take_uid']);
            
            return array('status'=>1, 'msg'=> $reponse);
        }
    }
    
    /**
     * 流程NOT
     */
    public function notWorkflow ($data) 
    {
        $flow_id = $data['flow_id'];
        
        $flowInfo = $this->find($flow_id);
        if (!$flowInfo) {
            return array('status'=> 0, 'msg'=> '流程不存在,请联系管理员！');
        }
        
        $workflow_update = array(
            'status' => 2
        ); 
        $workflow_is_update = $this->where(array("id"=>$flow_id))->save($workflow_update);
        
        $workflow_step_update = array(
            'status' => 4,
            'opinion' => trim($data['opinion']) 
        );
        $workflow_step_is_update = M('WorkflowStep')->where(array('flow_id'=>$flow_id, 'status'=>2))->save($workflow_step_update);
        
        $updateData = array(
            'status' => 5,
            'handle_time' => $this->time
        );
        $update = $this->updatedRelatedRecords('back', $flow_id, $updateData);
        
        if (!$workflow_is_update ||!$workflow_step_is_update || !$update) {
            
            $this->commonModel->rollback();
            
            return array('status'=>0 , 'msg'=> '否决失败！');
        } else {
            $this->commonModel->commit();
            
            $reponse = $this->flowResponse('not');
            
            return array('status'=>1, 'msg'=> $reponse);
        }
    }
    
    
    /**
     * 流程FINISH
     */
    public function finishWorkflow ($data) 
    {
        $flow_id = $data['flow_id'];
        
        $flowInfo = $this->find($flow_id);
        if (!$flowInfo) {
            return array('status'=> 0, 'msg'=> '流程不存在,请联系管理员！');
        }
        
        $this->commonModel->startTrans();
        
        $workflow_update = array(
            'status' => 3
        ); 
        $workflow_is_update = $this->where(array("id"=>$flow_id))->save($workflow_update);
        
        $workflow_step_update = array(
            'status' => 4,
            'opinion' => trim($data['opinion']) 
        );
        $workflow_step_is_update = M('WorkflowStep')->where(array('flow_id'=>$flow_id, 'status'=>2))->save($workflow_step_update);
        
        $updateData = array(
            'status' => '6',
            'handle_time' => $this->time
        );
        $update = $this->updatedRelatedRecords('back', $flow_id, $updateData);
        
        if (!$workflow_is_update ||!$workflow_step_is_update || !$update) {
            
            $this->commonModel->rollback();
            
            return array('status'=>0 , 'msg'=> '办理失败！');
            
        } else {
            
            $this->commonModel->commit();
            
            $reponse = $this->flowResponse('finish');
            
            return array('status'=>1, 'msg'=> $reponse);
        }
    }
    
    /**
     * 流程CHANGE
     */
    public function changeWorkflow ($data)
    {
        $flow_id = $data['flow_id'];
        
        $workflow_first_conf = D('Common/WorkflowConf')->getFlowConf($data['flow_type_id'], $data['flow_version_id'], 1);
        if (!$workflow_first_conf) {
            return array('status'=> 0, 'msg'=> '流程配置出错,请联系管理员！');
        }
        
        $flowInfo = $this->find($flow_id);
        
        $this->commonModel->startTrans();
        
        $workflow_update = array(
            'type_id' => $data['flow_type_id'],
            'version_id' => $data['flow_version_id'],
            'step' => 1
        );
        $workflow_is_update = $this->where(array("id"=>$flow_id))->save($workflow_update);
        
        $workflow_step_update = array(
            'status' => 3, 
            'opinion' => $data['opinion'] ? $data['opinion'] : '更换流程', 
            'submit_time' => $this->time,
        );
        $workflow_step_is_update = M('WorkflowStep')->where(array("flow_id"=>$flow_id, "status"=>2))->save($workflow_step_update);
        
        $workflow_step_isvalid = array(
            'isvalid' => -1
        );
        $workflow_step_is_isvalid = M('WorkflowStep')->where(array("flow_id"=>$flow_id, 'isvalid'=> 1))->save($workflow_step_isvalid);
    
        $workflow_step_insert = array(
            'flow_id' => $flow_id,
            'node_id' => $workflow_first_conf['node_id'],
            'conf_id' => $workflow_first_conf['id'],
            'handle_str' => $flowInfo['create_uid'],
            'take_uid' => $flowInfo['create_uid'],
            'accept_time' => $this->time,
            'status' => 1
        );
        $workflow_step_insert_id = M('WorkflowStep')->add($workflow_step_insert);
        
        $updateData = array(
            'wftype' => $data['flow_type_id'],
            'wfnode' => $workflow_first_conf['workflow_node_id'],
            'step_id' => $workflow_step_insert_id,
            'dept_id' => get_user_dept($flowInfo['create_uid']),
            'handle_str' => $flowInfo['create_uid'],
            'handle_group_id' => 0,
            'handle_uid' =>  $flowInfo['create_uid'],
            'handle_time' => $this->time 
        );
        $update = $this->updatedRelatedRecords('change', $flow_id, $updateData);
        
        if (!$workflow_is_update || !$workflow_step_is_update || !$workflow_step_is_isvalid || !$workflow_step_insert_id || !$update) {
            
            $this->commonModel->rollback();
            
            return array('status'=>0 , 'msg'=> '更换失败！');
        } else {
            
            $this->commonModel->commit();
            
            $reponse = $this->flowResponse('change', $flowInfo['create_uid']);
            
            return array('status'=>0 , 'msg'=> $reponse);
        }
    }        
    
    /**
     * 流程办理时 状态更新
     */
    public function handleWorkflow ($data) 
    {
        $flow_id = $data['flow_id'];
        
        $update = M('WorkflowStep')->where(array('flow_id'=>$flow_id, 'status'=>2))->count();
        
        if ($update)  return true;
        
        $this->commonModel->startTrans();
        
        $workflow_step_last_update = array(
            'status' => 4
        );
        $workflow_step_last_is_update = M('WorkflowStep')->where(array('flow_id'=>$flow_id, 'status'=>3))->save($workflow_step_last_update);
        
        $workflow_step_current_update = array(
            'status' => 2
        );
        $workflow_step_current_is_update = M('WorkflowStep')->where(array('flow_id'=>$flow_id, 'status'=>1))->save($workflow_step_current_update);
        
        if ($workflow_step_last_is_update && $workflow_step_current_is_update) {
            
            $this->commonModel->commit();
            return true;
        } else {
            
            $this->commonModel->rollback();
            return false;
        }
        
    }
    
    /**
     * 流程回写
     */
    public function updatedRelatedRecords ($method, $flow_id, $updateData) 
    {
        $flow_id = $data['flow_id'];
        
        $flowInfo = $this->find($flow_id);
        
        if (!$flowInfo)  return false;
        
        $category = $flowInfo['category']; //流程类别
        $case_id = $flowInfo['case_id'];  //基础案例
        $case_type_id = $flowInfo['case_type_id']; //案例类型
        $record_id = $flowInfo['record_id'];
        
        if ($category) {
            $update = D('Common/CaseType')->where(array('id'=>$case_type_id))->save($updateData);
        } else {
            $update = true;
        }
        
        return $update;
    }
    
    /**
     * 流程响应
     */
    public function flowResponse ($method, $handle_str='')
    {
        if ($handle_str) {
            
            $handle_array = D('Common/User')->where(array('id'=> array('in', $handle_str)))->getField('true_name', true);
            
            if ($handle_array) {
                $handle_info = "受理人(".implode(',', $handle_array).").";
            }
        }
        
        if ($method == 'create') 
        {
            $response = '创建成功！'.$handle_info;
        } 
        elseif ($method == 'pass') 
        {
            $response = '办理成功！'.$handle_info;;
        }
        elseif ($method == 'back')
        {
            $response = '回退成功！'.$handle_info;; 
        }    
        elseif ($method == 'not')
        {
            $response = '否决成功！';
        }
        elseif ($method == 'finish')
        {
            $response = '流程完成！';
        } 
        elseif ($method == 'change')
        {
            $response = '更换成功！'.$handle_info;; 
        }    
        
        return $response;
    } 
    
    /**
     * 流程办理按钮
     */
    public function WorkflowButton($flow_id) 
    {
        $flowInfo = $this->find($flow_id);
        
        $flowType_id = $flowInfo['type_id'];
        $flowVersion_id = $flowInfo['version_id'];
        $step = $flowInfo['step'];
        
        $workflow_next_step = D('Common/WorkflowConf')->where(array("status"=>1, "workflow_version_id"=>$flowVersion_id, "workflow_type_id"=>$flowType_id, "step"=>$step+1))->count();
        
        if ($workflow_next_step) {
            $isLast = false;
        } else {
            $isLast = true;
        }
        
        $btnArray = array(
            array('text'=> "通过", 'prop'=> $isLast ? 'finish' : 'pass'),
            array('text'=> "否决", 'prop'=> 'not')
        );
        
        return $btnArray;
    }
}
