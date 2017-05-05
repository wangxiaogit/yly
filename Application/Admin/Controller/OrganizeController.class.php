<?php
namespace Admin\Controller;
use Common\Controller\AdminController;

class OrganizeController extends AdminController
{
    public $organizeModel;
    
    public function _initialize() {
        parent::_initialize();
        
        $this->organizeModel = D('Common/Organize');
    }
    
    /**
     * 列表
     */
    public function index() 
    {
        $organize_type = I('get.type', 1, 'intval');
        
        $organize_lists = $this->lists($this->organizeModel, array('type'=>$organize_type, 'status'=>1), 'id desc');
        
        $this->assign("organize_lists", $organize_lists);
        $this->assign("organize_type", $this->organizeModel->ORGANIZE_TYPE);
        $this->assign("meta-title", "机构列表");
        $this->display();
    }
    
    /**
     * 添加
     */
    public function add ()
    {
        $this->assign("organize_type", $this->organizeModel->ORGANIZE_TYPE);
        $this->assign("meta-title", "机构添加");
        $this->display();
    }  
    
    /**
     * 添加提交
     */
    public function do_add ()
    {
        if (IS_POST) {
            if ($this->organizeModel->create()) {
                if (false !== $this->organizeModel->add()) {
                    $this->success('添加成功！', U('Organize/index'));
                } else {
                    $this->error('添加失败！');
                }
            } else {
                $this->error($this->organizeModel->getError());
            }
        } 
    }   
    
    /**
     * 编辑
     */
    public function edit ($id)
    {
        $organize = $this->organizeModel->find($id);
        
        $this->assign("$organize", $organize);
        $this->assign("organize_type", $this->organizeModel->ORGANIZE_TYPE);
        $this->assign("meta-title", "机构编辑");
        $this->display();
    }   
    
    /**
     * 编辑提交
     */
    public function do_edit ()
    {
        if (IS_POST) {
            if ($this->organizeModel->create()) {
                if (false !== $this->organizeModel->save()) {
                    $this->success('编辑成功！', U('Organize/index'));
                } else {
                    $this->error('编辑失败！');
                }
            } else {
                $this->error($this->organizeModel->getError());
            }
        } 
    }  
    
    
    /**
     * 删除
     */
    public function del ()
    {
        $organize_id = I('get.id', 0, 'intval');
        
        if ($this->organizeModel->where(array("id"=>$organize_id))->setField("status", 2)) {
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        } 
    }
}

