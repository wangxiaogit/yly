<?php
/**
 * 案件列表控制器
 */
namespace Admin\Controller;
use Common\Controller\AdminController;

class CaseListController extends AdminController
{   
    protected  $caseListModel;
    
    public function _initialize()
    {
        parent::_initialize();
        $this->caseListModel = D('Common/CaseList');
    }
    
    /**
     * 待办
     */
    public function needToDoList()
    {   
        //model
        $action = I('get.action_type');
        if($action) {
            dump(I());die;
        }
        $this->assign('meta_title','待办案件');
        $this->display();
    }
    
    /*
     * 已办
     */
    public function doneList()
    {
        
    }
    
    
    /*
     *办结 
     */
    public function endList()
    {
        
    }        
         
    
    /*
     * 增加
     */
    public function add()
    {   
        $feeType = $this->feeStandardModel->getFeeType();
        $this->assign('meta_title', '费用标准');
        $this->assign('feeType', $feeType);
        $this->display();
    }
    
    
    /*
     * 新增提交
     */
    public function do_add()
    {
        if (IS_POST) {
            if ($this->feeStandardModel->create()) {
                if ($this->feeStandardModel->add()!==false) {
                    $this->success('添加成功', U('FeeStandard/index'));
                } else {
                    $this->error('添加失败');
                }
            } else {
                $this->error($this->feeStandardModel->getError());
            }
        }
    }
    
    
    /**
     * 编辑费用
     */
    public function edit($id)
    {
        $feeInfo = $this->feeStandardModel->find($id);
        $feeType = $this->feeStandardModel->getFeeType();
        
        if (false === $feeInfo) {
            $this->error('未查询到收费项目');
        }
        $this->assign('id', $id);
        $this->assign('feeInfo', $feeInfo);
        $this->assign('feeType', $feeType);
        $this->assign('meta_title', '修改费用');
        $this->display();
    }        
    
    /**
     * 提交编辑
     */
    public function do_edit()
    {
        if (IS_POST) {
            if ($this->feeStandardModel->create()) {
                if ($this->feeStandardModel->save() !== false) {
                    $this->success('修改成功！', U('FeeStandard/index'));
                } else {
                    $this->error('修改失败！');
                }
            } else {
                $this->error($this->feeStandardModel->getError());
            }
        } 
    }
}
