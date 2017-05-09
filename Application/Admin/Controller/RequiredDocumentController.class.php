<?php
/**
 * 资料类型
 */
namespace Admin\Controller;
use Common\Controller\AdminController;

class RequiredDocumentController extends AdminController
{   
    protected  $documentModel;
    
    public function _initialize()
    {
        parent::_initialize();
        $this->documentModel = D('Common/RequiredDocument');
    }
    
    /**
     * 资料类型定义视图
     */
    public function index()
    {
        $documentType = $this->documentModel->getDocumentType();
        $documents = $this->lists($this->documentModel, $where, 'document_type ASC');
        $this->assign('documents', $documents);
        $this->assign('meta_title', '收件资料');
        $this->assign('documentType', $documentType);
        $this->display('index');
    }
    
    
    /*
     * 增加
     */
    public function add()
    {   
        $documentType = $this->documentModel->getDocumentType();
        $this->assign('meta_title', '资料标准');
        $this->assign('documentType', $documentType);
        $this->display();
    }
    
    
    /*
     * 新增提交
     */
    public function do_add()
    {
        if (IS_POST) {
            if ($this->documentModel->create()) {
                if ($this->documentModel->add()!==false) {
                    $this->success('添加成功', U('RequiredDocument/index'));
                } else {
                    $this->error('添加失败');
                }
            } else {
                $this->error($this->documentModel->getError());
            }
        }
    }
    
    
    /**
     * 编辑资料
     */
    public function edit($id)
    {
        $documentInfo = $this->documentModel->find($id);
        $documentType = $this->documentModel->getDocumentType();
        
        if (false === $documentInfo) {
            $this->error('未查询到资料项目');
        }
        $this->assign('id', $id);
        $this->assign('documentInfo', $documentInfo);
        $this->assign('documentType', $documentType);
        $this->assign('meta_title', '修改资料');
        $this->display();
    }        
    
    /**
     * 提交编辑
     */
    public function do_edit()
    {
        if (IS_POST) {
            if ($this->documentModel->create()) {
                if ($this->documentModel->save() !== false) {
                    $this->success('修改成功！', U('RequiredDocument/index'));
                } else {
                    $this->error('修改失败！');
                }
            } else {
                $this->error($this->documentModel->getError());
            }
        } 
    }
}
