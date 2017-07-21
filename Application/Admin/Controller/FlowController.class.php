<?php
namespace Admin\Controller;
use Common\Controller\AdminController;

class FlowController extends AdminController
{
    protected  $flowModel;
    
    public function _initialize() {
        parent::_initialize();
        
        $this->flowModel = D('Common/Workflow');
    }
    
    /**
     * 流程类型
     */
    public function type () 
    {
        $case_type_id = I('get.case_type', 1, 'intval'); //业务类型
        
        $flowType_lists = D('Common/WorkflowType')->where(array('category'=>1, 'status'=>1, 'case_type_id'=>$case_type_id))->select();
        
        $this->assign("data", array_merge(I('get.'), array('category'=>1)));
        $this->assign("flowType_lists", $flowType_lists);
        $this->display();
    }
    
    /**
     * 流程新建
     */
    public function create () 
    {
        $data = I('request.');
        
        $case_id = $data['case_id'];
        $case_type_id = $data['case_type_id'];
        $record_id = $data['record_id'];

        $flowType_id = $data['flow_type_id']; //流程类型
        if (!$flowType_id) {
            $this->error('请选择流程类型！');
        }
        
        //检查流程版本. 流程配置
        $auth = $this->flowModel->flowCreateAuth($flowType_id); 
        if (!$auth['status']) {
            $this->error($auth['msg']);
        }
        
        $data['flow_version_id'] = $auth['flowVersion_id'];//流程版本
        
        //流程INFO
        $flowInfo = $this->flowModel->flowInfoBulid($flowType_id, $case_id, $case_type_id, $record_id);
        $data['info'] = $flowInfo;
               
        //节点限制
        $flowLimit =  $this->flowModel->flowNodeLimit('create', $flowType_id, $case_id, $case_type_id, $record_id); 
        if (!$flowLimit['status']) {
            $this->error($flowLimit['msg']);
        }
        
        //流程CREATE
        $create = $this->flowModel->createWorkflow($data);
        
        if ($create['status']) {
            $this->success($create['msg']);
        } else {
            $this->error($create['msg']);
        }
    }
    
    /**
     * 流程办理
     */
    public function handle () 
    {
        $flowId = I('request.flow_id', 0, 'intval');
       
        $flowInfo = $this->flowModel->find($flowId);
        
        $flowType_id = $flowInfo['type_id']; //流程类型
        $flowVersion_id = $flowInfo['version_id']; //流程版本
        $category = $flowInfo['category']; //流程类别
        
        $case_id = $flowInfo['case_id'];  //基础案例
        $case_type_id = $flowInfo['case_type_id']; //案例类型
        $record_id = $flowInfo['record_id'];
        
        $data = array(
            'opinion' => I('post.opinion', 0, 'trim'),
            'flow_id' => $flowId
        );
        
        $action = I('get.action', '', 'trim');
        
        if ($action == 'pass') {
            
            //节点限制
            $flowLimit =  $this->flowModel->flowNodeLimit('pass', $flowId, $case_id, $case_type_id, $record_id); 
            if (!$flowLimit['status']) {
                $this->error($flowLimit);
            }
            
            //流程PASS
            $pass = $this->flowModel->passWorkflow($data);
            
            if ($pass['status']) {
                $this->success($pass['msg']);
            } else {
                $this->error($pass['msg']);
            }
            
        } else if ($action == 'back') {
            
            $data['flow_conf_id'] = I('post.flow_conf_id', 0, 'intval');
            if (!$data['flow_conf_id']) {
                $this->error('请选择回退节点！');
            }
            
            //节点限制
            $flowLimit =  $this->flowModel->flowNodeLimit('back', $flowId, $case_id, $case_type_id, $record_id); 
            if (!$flowLimit['status']) {
                $this->error($flowLimit);
            }
            
            //流程BACK
            $back = $this->flowModel->backWorkflow($data);
            
            if ($back['status']) {
                $this->success($back['msg']);
            } else {
                $this->error($back['msg']);
            }
            
        } else if ($action == 'not') {
            
            //节点限制
            $flowLimit =  $this->flowModel->flowNodeLimit('not', $flowId, $case_id, $case_type_id, $record_id); 
            if (!$flowLimit['status']) {
                $this->error($flowLimit);
            }
            
            //流程BACK
            $not = $this->flowModel->notWorkflow($data);
            
            if ($not['status']) {
                $this->success($not['msg']);
            } else {
                $this->error($not['msg']);
            }
            
        } else if ($action == 'finish') {
            
            //节点限制
            $flowLimit =  $this->flowModel->flowNodeLimit('finish', $flowId, $case_id, $case_type_id, $record_id); 
            if (!$flowLimit['status']) {
                $this->error($flowLimit);
            }
            
            //流程BACK
            $finish = $this->flowModel->finishWorkflow($data);
            
            if ($finish['status']) {
                $this->success($finish['msg']);
            } else {
                $this->error($finish['msg']);
            }
            
        } else {
            
            $this->flowModel->handleWorkflow($data); //流程状态更新
            
            $workflow_buttons = $this->flowModel->WorkflowButton($flowId);
            
            if ($case_id) 
            {    
                $case_info = D('Common/CaseList')->alias('a')->join("case_type b on a.id=b.case_id");
                
                $this->assign("case_info", $case_info);
            }
            
            if ($category) 
            {    
                $workflow_back_lists = M('WorkflowStep')
                                       ->alias('a')
                                       ->field("a.*, b.name node_name")
                                       ->join("workflow_node b on a.node_id = b.id") 
                                       ->where(array("a.flow_id"=>$flowId, "a.isvalid"=>1, "a.status"=> array('in', "3,4")))
                                       ->select();
                
                if ($workflow_back_lists) {
                    array_unshift($workflow_buttons, array('text'=>'回退', 'prop'=> 'back', 'display'=>'none'));
                }
                
                $this->assign("workflow_back_lists", $workflow_back_lists);
            }
            
            $this->assign("flowInfo", $flowInfo);
            $this->assign("workflow_buttons", $workflow_buttons);
            $this->display();
        }
        
    }
    
    /**
     * 流程查看
     */
    public function view ()
    {
        $flowId = I('get.flow_id', 0, 'intval');
        
        $flowInfo = $this->flowModel->find($flowId);
        
        $flowType_id = $flowInfo['type_id']; //流程类型
        $flowVersion_id = $flowInfo['version_id']; //流程版本
        $category = $flowInfo['category']; //流程类别
        
        $case_id = $flowInfo['case_id'];  //基础案例
        $case_type_id = $flowInfo['case_type_id']; //案例类型
        $record_id = $flowInfo['record_id'];
        
        if ($case_id)
        {
            $case_info = D('Common/CaseList')->find($case_id);
            
            $this->assign("case_info", $case_info);
        }
        
        $workflow_step = D('Common/WorkflowStep')->alias('a')
                                                 ->field('a.*, b.name node_name')   
                                                 ->join('workflow_node b on a.node_id = b.id')
                                                 ->where(array('a.flow_id'=>$flowId))
                                                 ->select();   
        //print_r($workflow_step);exit;
        $this->assign("workflow_step", $workflow_step);
        $this->display();
    }
    
    /**
     * 更换流程
     */
    public function change () 
    {
        $flow_id = I('get.flow_id', 0, 'intval');
        if (!$flow_id) {
            $this->error("该流程未走流程,暂无法更换！");
        }
        
        $case_type_id = I('get.case_type', 1, 'intval'); //业务类型
        $flow_type_id = I('get.flow_type_id', 0, 'intval'); //业务类型
        
        $flowType_lists = D('Common/WorkflowType')->where(array('category'=>1, 'status'=>1, 'case_type_id'=>$case_type_id, 'id'=>array('neq', $flow_type_id)))->select();
        
        $this->assign("flow_id", $flow_id);
        $this->assign("flowType_lists", $flowType_lists);
        $this->display();
    }
    
    public function do_change ()
    {
        $data = I('post.');
        
        $flow_type_id = $data['flow_type_id'];
        if (!$flow_type_id) {
            $this->error('请选择流程类型！');
        }
        
        //检查流程版本. 流程配置
        $auth = $this->flowModel->flowCreateAuth($flowType_id); 
        if (!$auth['status']) {
            $this->error($auth['msg']);
        }
        
        $data['flow_version_id'] = $auth['flowVersion_id'];//流程版本
        
        //流程CHANGE
        $change = $this->flowModel->changeWorkflow($data);
        
        if ($change['status']) {
            $this->success($change['msg']);
        } else {
            $this->error($change['msg']);
        }
    }        
}
