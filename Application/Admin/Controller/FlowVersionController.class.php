<?php
namespace Admin\Controller;
use Common\Controller\AdminController;

class FlowVersionController extends AdminController
{
    protected $flowVersionModel;

    public function _initialize() {
        parent::_initialize();
        
        $this->flowVersionModel = D('Common/WorkflowVersion');
    }
    
    /**
     * 列表
     */
    public function index() 
    {
        $model = $this->flowVersionModel->alias('a')->join("workflow_type b on a.workflow_type_id = b.id");
        
        $flowVersion_lists = $this->lists($model, array('a.status'=>1), 'workflow_type_id desc, is_active desc', "a.*, b.name flowType_name");
        
        $this->assign("flowVersion_lists", $flowVersion_lists);
        $this->assign("meta-title", "流程版本列表");
        $this->display();
    }
    
    /**
     * 添加
     */
    public function add ()
    {
        $flowType_lists = D('Common/WorkflowType')->where(array("status"=>1))->select();
        
        $this->assign("flowType_lists", $flowType_lists);
        $this->assign("meta-title", "流程版本添加");
        $this->display();
    }  
    
    /**
     * 添加提交
     */
    public function do_add ()
    {
        if (IS_POST) {
            if ($this->flowVersionModel->create()) {
                if (false !== $this->flowVersionModel->add()) {
                    $this->success('添加成功！', U('FlowVersion/index'));
                } else {
                    $this->error('添加失败！');
                }
            } else {
                $this->error($this->flowVersionModel->getError());
            }
        } 
    }   
    
    /**
     * 编辑
     */
    public function edit ()
    {
        $id = I('get.id', 0, 'intval');
        
        $flowVersion = $this->flowVersionModel->find($id);
        
        $flowType_lists = D('Common/WorkflowType')->where(array("status"=>1))->select();
        
        $this->assign("flowType_lists", $flowType_lists);
        $this->assign("flowVersion", $flowVersion);
        $this->assign("meta-title", "流程版本编辑");
        $this->display();
    }   
    
    /**
     * 编辑提交
     */
    public function do_edit ()
    {
        if (IS_POST) {
            if ($this->flowVersionModel->create()) {
                if (false !== $this->flowVersionModel->save()) {
                    $this->success('编辑成功！');
                } else {
                    $this->error('编辑失败！');
                }
            } else {
                $this->error($this->flowVersionModel->getError());
            }
        } 
    }  
    
    /**
     * 删除
     */
    public function del ()
    {
        $id = I('get.id', 0, 'intval');
        
        $flowVersions = D('Common/WorkflowConf')->where(array("workflow_version_id"=>$id))->count();
        if ($flowVersions) {
            $this->error("该流程版本下还有流程配置！暂不能删除");
        }
        
        if ($this->flowVersionModel->where(array("id"=>$id))->setField("status", 2)) {
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        } 
    }
    
    public function ajax_get_version () 
    {
        $type_id = I('request.type_id', 0, 'intval');
        
        if (!$type_id) {
            $this->error("参数错误！");
        }
        
        $flowVersion_lists = $this->flowVersionModel->where(array("workflow_type_id"=> $type_id, 'status'=>1))->select();
        
        $this->ajaxReturn(['data'=>$flowVersion_lists, 'status'=>1]);
    }
}
