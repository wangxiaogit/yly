<?php
namespace Admin\Controller;
use Common\Controller\AdminController;

class FlowNodeController extends AdminController
{
    protected $flowNodeModel;

    public function _initialize() {
        parent::_initialize();
        
        $this->flowNodeModel = D('Common/WorkflowNode');
    }
    
    /**
     * 列表
     */
    public function index() 
    {
        $flowNode_lists = $this->lists($this->flowNodeModel, array('status'=>1), 'id asc');
        
        $this->assign("flowNode_lists", $flowNode_lists);
        $this->assign("meta-title", "流程节点列表");
        $this->display();
    }
    
    /**
     * 添加
     */
    public function add ()
    {
        $this->assign("meta-title", "流程节点添加");
        $this->display();
    }  
    
    /**
     * 添加提交
     */
    public function do_add ()
    {
        if (IS_POST) {
            if ($this->flowNodeModel->create()) {
                if (false !== $this->flowNodeModel->add()) {
                    $this->success('添加成功！', U('FlowNode/index'));
                } else {
                    $this->error('添加失败！');
                }
            } else {
                $this->error($this->flowNodeModel->getError());
            }
        } 
    }   
    
    /**
     * 编辑
     */
    public function edit ()
    {
        $id = I('get.id', 0, 'intval');
        
        $flowNode = $this->flowNodeModel->find($id);
        
        $this->assign("flowNode", $flowNode);
        $this->assign("meta-title", "流程节点编辑");
        $this->display();
    }   
    
    /**
     * 编辑提交
     */
    public function do_edit ()
    {
        if (IS_POST) {
            if ($this->flowNodeModel->create()) {
                if (false !== $this->flowNodeModel->save()) {
                    $this->success('编辑成功！');
                } else {
                    $this->error('编辑失败！');
                }
            } else {
                $this->error($this->flowNodeModel->getError());
            }
        } 
    }  
    
    /**
     * 删除
     */
    public function del ()
    {
        $id = I('get.id', 0, 'intval');
        
        $flowConf = D('Common/WorkflowConf')->where(array("workflow_node_id"=>$id))->count();
        if ($flowConf) {
            $this->error("该流程节点下还有流程配置！暂不能删除");
        } 
        
        if ($this->flowNodeModel->where(array("id"=>$id))->setField("status", 2)) {
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        } 
    } 
}
