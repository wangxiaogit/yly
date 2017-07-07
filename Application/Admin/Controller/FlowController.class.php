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
        $case_type_id = I('get.case_type_id', 0, 'intval'); //业务类型
        $category = I('get.category', 1, 'intval'); //流程类别
        
        $flowType_lists = D('FlowType')->where(array('category'=>$category, 'status'=>1, 'case_type_id'=>$case_type_id))->select();
        
        $this->assign("data", I('.get'));
        $this->assign("flowType_lists", $flowType_lists);
        $this->display();
    }
    
    /**
     * 提交类型
     */
    public function do_type () 
    {
        $flowType_id = I('request.flow_type_id', 0, 'intval');
        
        $flowVersion = D('FlowVersion')->where(array('status'=>1, 'is_active'=>1, 'workflow_type_id'=>$flowType_id))->select();
        
        if (count($flowVersion) == 1) {
            
            $flowVersion_id = $flowVersion[0]['id']; 
            $data = I('.get');
            
            $data['flowType_id'] = $flowType_id;
            $data['flowVersion_id'] = $flowVersion_id;
            
            $this->create($data);
        } else {
            $this->error('流程版本出错, 请联系管理员！');
        }
    }
    
    /**
     * 流程新建
     */
    public function create ($data) 
    {
        $flowType_id = $data['flowType_id'];
        $flowVersion_id = $data['flowVersion_id'];
        
        $flowConf = D('FlowConf')->where(array('status'=>1, 'workflow_type_id'=>$flowType_id, 'workflow_version_id'=>$flowVersion_id))->count();
        if (!$flowConf) {
            $this->error('流程配置出错, 请联系管理员！');           
        }
        
        $this->assign("data", $data);
        $this->display();
    }
    
    /**
     * 提交新建
     */
    public function do_create () 
    {
        $data = I('post.data');
        
        $flowType_id = $data['flowType_id'];
        $flowVersion_id = $data['flowVersion_id'];
        
        $case_id = $data['case_id'];
        $case_type_id = $data['case_type_id'];
        $record_id = $data['record_id'];
        
        $opinion = I('post.opinion', '', 'trim');
        
        $data['opinion'] = $opinion;
        
        //流程INFO
        $flowInfo = $this->flowModel->flowInfoBulid($flowType_id, $case_id, $case_type_id, $record_id);
        $data['info'] = $flowInfo;
               
        //节点限制
        $flowLimit =  $this->flowModel->flowNodeLimit('create', $flowType_id, $case_id, $case_type_id, $record_id); 
        if (!$flowLimit['status']) {
            $this->error($flowLimit);
        }
        
        //流程CREATE
        $create = $this->flowModel->create($data);
        if ($create['status']) {
            $this->success('新建成功！');
        } else {
            $this->error('新建失败！');
        }
        
    }
    
    /**
     * 流程办理
     */
    public function handle () 
    {
        $flowId = I('get.flowId', 0, 'intval');
        
        $flowInfo = $this->flowModel->find($flowId);
        
        $flowType_id = $flowInfo['type_id']; //流程类型
        $flowVersion_id = $flowInfo['version_id']; //流程版本
        $category = $flowInfo['category']; //流程类别
        
        $case_id = $flowInfo['case_id'];  //基础案例
        $case_type_id = $flowInfo['case_type_id']; //案例类型
        $record_id = $flowInfo['record_id'];
        
        $data = array(
            'opinion' => I('post.opinion', 0, 'trim'),
            'flowId' => $flowId
        );
        
        $action = I('get.action', 0, 'trim');
        
        if ($action == 'pass') {
            
            //节点限制
            $flowLimit =  $this->flowModel->flowNodeLimit('pass', $flowId, $case_id, $case_type_id, $record_id); 
            if (!$flowLimit['status']) {
                $this->error($flowLimit);
            }
            
            //流程PASS
            $pass = $this->flowModel->passWorkflow($data);
            
            if ($pass['status']) {
                $this->success('办理成功！');
            } else {
                $this->error('办理失败！');
            }
            
        } elseif ($action == 'back') {
            
            $data['flowConf_id'] = I('post.flowConf_id', 0, 'intval');
            
            //节点限制
            $flowLimit =  $this->flowModel->flowNodeLimit('back', $flowId, $case_id, $case_type_id, $record_id); 
            if (!$flowLimit['status']) {
                $this->error($flowLimit);
            }
            
            //流程BACK
            $back = $this->flowModel->backWorkflow($data);
            
            if ($back['status']) {
                $this->success('回退成功！');
            } else {
                $this->error('回退失败！');
            }
            
        } elseif ($action == 'not') {
            
            //节点限制
            $flowLimit =  $this->flowModel->flowNodeLimit('not', $flowId, $case_id, $case_type_id, $record_id); 
            if (!$flowLimit['status']) {
                $this->error($flowLimit);
            }
            
            //流程BACK
            $not = $this->flowModel->notWorkflow($data);
            
            if ($not['status']) {
                $this->success('否决成功！');
            } else {
                $this->error('否决失败！');
            }
            
        } elseif ($action == 'finish') {
            
            //节点限制
            $flowLimit =  $this->flowModel->flowNodeLimit('finish', $flowId, $case_id, $case_type_id, $record_id); 
            if (!$flowLimit['status']) {
                $this->error($flowLimit);
            }
            
            //流程BACK
            $finish = $this->flowModel->finishWorkflow($data);
            
            if ($finish['status']) {
                $this->success('办理成功！');
            } else {
                $this->error('办理失败！');
            }
            
        } else {
            
            $this->assign("flowInfo", $flowInfo);
            $this->display();
        }
        
    }
    
    /**
     * 流程查看
     */
    public function view ()
    {
        $flowId = I('get.flowId', 0, 'intval');
        
        $flowInfo = $this->flowModel->find($flowId);
        
        $flowType_id = $flowInfo['type_id']; //流程类型
        $flowVersion_id = $flowInfo['version_id']; //流程版本
        $category = $flowInfo['category']; //流程类别
        
        $case_id = $flowInfo['case_id'];  //基础案例
        $case_type_id = $flowInfo['case_type_id']; //案例类型
        $record_id = $flowInfo['record_id'];
        
        $flowChart = $this->flowModel->chartWorkflow($flowId);
        
        $this->assign("flowChart", $flowChart);
        $this->display();
    }        
}
