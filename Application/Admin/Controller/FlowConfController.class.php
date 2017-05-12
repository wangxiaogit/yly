<?php
namespace Admin\Controller;
use Common\Controller\AdminController;

class FlowConfController extends AdminController
{
    protected $flowConfModel;

    public function _initialize() {
        parent::_initialize();
        
        $this->flowConfModel = D('Common/WorkflowConf');
    }
    
    /**
     * 列表
     */
    public function index() 
    {
        $flowConf_lists = $this->lists($this->flowConfModel, array('status'=>1), 'id desc');
        
        $this->assign("flowConf_lists", $flowConf_lists);
        $this->assign("meta-title", "流程配置列表");
        $this->display();
    }
    
    /**
     * 添加
     */
    public function add ()
    {
        $this->assign("meta-title", "流程配置添加");
        $this->display();
    }  
    
    /**
     * 添加提交
     */
    public function do_add ()
    {
        if (IS_POST) {
            if ($this->flowConfModel->create()) {
                if (false !== $this->flowConfModel->add()) {
                    $this->success('添加成功！', U('FlowConf/index'));
                } else {
                    $this->error('添加失败！');
                }
            } else {
                $this->error($this->flowConfModel->getError());
            }
        } 
    }   
    
    /**
     * 编辑
     */
    public function edit ()
    {
        $id = I('get.id', 0, 'intval');
        
        $flowConf = $this->flowConfModel->find($id);
        
        $this->assign("flowConf", $flowConf);
        $this->assign("meta-title", "流程配置编辑");
        $this->display();
    }   
    
    /**
     * 编辑提交
     */
    public function do_edit ()
    {
        if (IS_POST) {
            if ($this->flowConfModel->create()) {
                if (false !== $this->flowConfModel->save()) {
                    $this->success('编辑成功！');
                } else {
                    $this->error('编辑失败！');
                }
            } else {
                $this->error($this->flowConfModel->getError());
            }
        } 
    }  
    
    
    /**
     * 删除
     */
    public function del ()
    {
        $id = I('get.id', 0, 'intval');
        
        if ($this->flowConfModel->where(array("id"=>$id))->setField("status", 2)) {
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        } 
    } 
}
