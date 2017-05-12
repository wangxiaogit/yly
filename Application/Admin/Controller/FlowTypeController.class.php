<?php
namespace Admin\Controller;
use Common\Controller\AdminController;

class FlowTypeController extends AdminController
{
    protected $flowTypeModel;

    public function _initialize() {
        parent::_initialize();
        
        $this->flowTypeModel = D('Common/WorkflowType');
    }
    
    /**
     * 列表
     */
    public function index() 
    {
        $flowType_lists = $this->lists($this->flowTypeModel, array('status'=>1), 'id desc');
        
        $this->assign("flowType_lists", $flowType_lists);
        $this->assign("meta-title", "流程类型列表");
        $this->display();
    }
    
    /**
     * 添加
     */
    public function add ()
    {
        $this->assign("meta-title", "流程类型添加");
        $this->display();
    }  
    
    /**
     * 添加提交
     */
    public function do_add ()
    {
        if (IS_POST) {
            if ($this->flowTypeModel->create()) {
                if (false !== $this->flowTypeModel->add()) {
                    $this->success('添加成功！', U('FlowType/index'));
                } else {
                    $this->error('添加失败！');
                }
            } else {
                $this->error($this->flowTypeModel->getError());
            }
        } 
    }   
    
    /**
     * 编辑
     */
    public function edit ()
    {
        $id = I('get.id', 0, 'intval');
        
        $flowType = $this->flowTypeModel->find($id);
        
        $this->assign("flowType", $flowType);
        $this->assign("meta-title", "流程类型编辑");
        $this->display();
    }   
    
    /**
     * 编辑提交
     */
    public function do_edit ()
    {
        if (IS_POST) {
            if ($this->flowTypeModel->create()) {
                if (false !== $this->flowTypeModel->save()) {
                    $this->success('编辑成功！');
                } else {
                    $this->error('编辑失败！');
                }
            } else {
                $this->error($this->flowTypeModel->getError());
            }
        } 
    }  
    
    
    /**
     * 删除
     */
    public function del ()
    {
        $id = I('get.id', 0, 'intval');
        
        if ($this->flowTypeModel->where(array("id"=>$id))->setField("status", 2)) {
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        } 
    } 
}
