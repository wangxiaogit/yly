<?php
namespace Admin\Controller;
use Common\Controller\AdminController;

class FlowBankController extends AdminController
{
    protected $flowBankModel;

    public function _initialize() {
        parent::_initialize();
        
        $this->flowBankModel = D('Common/WorkflowBank');
    }
    
    /**
     * 列表
     */
    public function index() 
    {
        $flowBank_lists = $this->lists($this->flowBankModel, array('status'=>1), 'id desc');
        
        $this->assign("flowBank_lists", $flowBank_lists);
        $this->assign("meta-title", "流程银行列表");
        $this->display();
    }
    
    /**
     * 添加
     */
    public function add ()
    {
        $this->assign("meta-title", "流程银行添加");
        $this->display();
    }  
    
    /**
     * 添加提交
     */
    public function do_add ()
    {
        if (IS_POST) {
            if ($this->flowBankModel->create()) {
                if (false !== $this->flowBankModel->add()) {
                    $this->success('添加成功！', U('FlowBank/index'));
                } else {
                    $this->error('添加失败！');
                }
            } else {
                $this->error($this->flowBankModel->getError());
            }
        } 
    }   
    
    /**
     * 编辑
     */
    public function edit ()
    {
        $id = I('get.id', 0, 'intval');
        
        $flowBank = $this->flowBankModel->find($id);
        
        $this->assign("flowBank", $flowBank);
        $this->assign("meta-title", "流程银行编辑");
        $this->display();
    }   
    
    /**
     * 编辑提交
     */
    public function do_edit ()
    {
        if (IS_POST) {
            if ($this->flowBankModel->create()) {
                if (false !== $this->flowBankModel->save()) {
                    $this->success('编辑成功！');
                } else {
                    $this->error('编辑失败！');
                }
            } else {
                $this->error($this->flowBankModel->getError());
            }
        } 
    }  
    
    
    /**
     * 删除
     */
    public function del ()
    {
        $id = I('get.id', 0, 'intval');
        
        if ($this->flowBankModel->where(array("id"=>$id))->setField("status", 2)) {
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        } 
    } 
}
