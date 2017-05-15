<?php
namespace Admin\Controller;
use Common\Controller\AdminController;

class FlowGroupController extends AdminController
{
    protected $flowGroupModel;

    public function _initialize() {
        parent::_initialize();
        
        $this->flowGroupModel = D('Common/WorkflowGroup');
    }
    
    /**
     * 列表
     */
    public function index() 
    {
        $flowGroup_lists = $this->lists($this->flowGroupModel, array('status'=>1), 'id desc');
        
        $this->assign("flowGroup_lists", $flowGroup_lists);
        $this->assign("meta-title", "流程组列表");
        $this->display();
    }
    
    /**
     * 添加
     */
    public function add ()
    {
        $this->assign("meta-title", "流程组添加");
        $this->display();
    }  
    
    /**
     * 添加提交
     */
    public function do_add ()
    {
        if (IS_POST) {
            if ($this->flowGroupModel->create()) {
                if (false !== $this->flowGroupModel->add()) {
                    $this->success('添加成功！', U('FlowGroup/index'));
                } else {
                    $this->error('添加失败！');
                }
            } else {
                $this->error($this->flowGroupModel->getError());
            }
        } 
    }   
    
    /**
     * 编辑
     */
    public function edit ()
    {
        $id = I('get.id', 0, 'intval');
        
        $flowGroup = $this->flowGroupModel->find($id);
        
        $this->assign("flowGroup", $flowGroup);
        $this->assign("meta-title", "流程组编辑");
        $this->display();
    }   
    
    /**
     * 编辑提交
     */
    public function do_edit ()
    {
        if (IS_POST) {
            if ($this->flowGroupModel->create()) {
                if (false !== $this->flowGroupModel->save()) {
                    $this->success('编辑成功！');
                } else {
                    $this->error('编辑失败！');
                }
            } else {
                $this->error($this->flowGroupModel->getError());
            }
        } 
    }  
    
    
    /**
     * 删除
     */
    public function del ()
    {
        $id = I('get.id', 0, 'intval');
        
        if ($this->flowGroupModel->where(array("id"=>$id))->setField("status", 2)) {
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        } 
    } 
}
