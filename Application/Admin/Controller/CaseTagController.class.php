<?php
/**
 * 标签类型
 */
namespace Admin\Controller;
use Common\Controller\AdminController;

class CaseTagController extends AdminController
{   
    protected  $feeStandardModel;
    
    public function _initialize()
    {
        parent::_initialize();
        $this->caseTagModel = D('Common/CaseTag');
    }
    
    /**
     * 案例标签定义视图
     */
    public function index()
    {
        $where = array();
        $colorType = $this->caseTagModel->getColorType();
        $tags = $this->lists($this->caseTagModel, $where, 'id ASC');
        $this->assign('tags', $tags);
        $this->assign('meta_title', '案例标签');
        $this->assign('colorType', $colorType);
        $this->display('index');
    }
    
    
    /*
     * 增加
     */
    public function add()
    {   
        $colorType = $this->caseTagModel->getColorType();
        $this->assign('meta_title', '新增标签');
        $this->assign('colorType', $colorType);
        $this->display();
    }
    
    
    /*
     * 新增提交
     */
    public function do_add()
    {
        if (IS_POST) {
            if ($this->caseTagModel->create()) {
                if ($this->caseTagModel->add()!==false) {
                    $this->success('添加成功', U('CaseTag/index'));
                } else {
                    $this->error('添加失败');
                }
            } else {
                $this->error($this->caseTagModel->getError());
            }
        }
    }
    
    
    /**
     * 编辑标签
     */
    public function edit($id)
    {
        $tagInfo = $this->caseTagModel->find($id);
        $colorType = $this->caseTagModel->getColorType();
        
        if (false === $tagInfo) {
            $this->error('未查询到此条标签');
        }
        $this->assign('id', $id);
        $this->assign('tagInfo', $tagInfo);
        $this->assign('colorType', $colorType);
        $this->assign('meta_title', '修改标签');
        $this->display();
    }        
    
    /**
     * 提交编辑
     */
    public function do_edit()
    {
        if (IS_POST) {
            if ($this->caseTagModel->create()) {
                if ($this->caseTagModel->save() !== false) {
                    $this->success('修改成功！', U('CaseTag/index'));
                } else {
                    $this->error('修改失败！');
                }
            } else {
                $this->error($this->caseTagModel->getError());
            }
        } 
    }
}
