<?php
/**
 * 费用类型
 */
namespace Admin\Controller;
use Common\Controller\AdminController;

class FeeStandardController extends AdminController
{   
    protected  $feeStandardModel;
    
    public function _initialize()
    {
        parent::_initialize();
        $this->feeStandardModel = D('Common/FeeStandard');
    }
    
    /**
     * 费用类型定义视图
     */
    public function index()
    {
        $where = array();
        $where['org_id'] = session('userInfo.org_id')?session('userInfo.org_id'):1;
        $feeType = $this->feeStandardModel->getFeeType();
        $fees = $this->lists($this->feeStandardModel, $where, 'fee_type ASC');
        $this->assign('fees', $fees);
        $this->assign('meta_title', '费用标准');
        $this->assign('feeType', $feeType);
        $this->display('index');
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
