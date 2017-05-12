<?php
namespace Admin\Controller;
use Common\Controller\AdminController;

class FlowVeisionController extends AdminController
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
        $flowVersion_lists = $this->lists($this->flowVersionModel, array('status'=>1), 'id desc');
        
        $this->assign("flowVersion_lists", $flowVersion_lists);
        $this->assign("meta-title", "流程版本列表");
        $this->display();
    }
    
    /**
     * 添加
     */
    public function add ()
    {
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
        
        if ($this->flowVersionModel->where(array("id"=>$id))->setField("status", 2)) {
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        } 
    } 
}
