<?php
namespace Admin\Controller;
use Common\Controller\AdminController;

class GroupController extends AdminController
{
    protected $groupModel;
    
    public function _initialize() {
        parent::_initialize();
        
        $this->groupModel = D('Common/AuthGroup');
    }
    
     /**
     * 列表
     */
    public function index() 
    {
        $group_lists = $this->lists($this->groupModel, array('status'=>1), 'id desc');
        
        $this->assign("group_lists", $group_lists);
        $this->assign("meta-title", "权限组列表");
        $this->display();
    }
    
    /**
     * 添加
     */
    public function add ()
    {
        $this->assign("meta-title", "权限组添加");
        $this->display();
    }  
    
    /**
     * 添加提交
     */
    public function do_add ()
    {
        if (IS_POST) {
            if ($this->groupModel->create()) {
                if (false !== $this->groupModel->add()) {
                    $this->success('添加成功！', U('Group/index'));
                } else {
                    $this->error('添加失败！');
                }
            } else {
                $this->error($this->groupModel->getError());
            }
        } 
    }   
    
    /**
     * 编辑
     */
    public function edit ()
    {
        $group_id = I('get.id', 0, 'intval');
        
        $group = $this->groupModel->find($group_id);
        
        $this->assign("group", $group);
        $this->assign("meta-title", "权限组编辑");
        $this->display();
    }   
    
    /**
     * 编辑提交
     */
    public function do_edit ()
    {
        if (IS_POST) {
            if ($this->groupModel->create()) {
                if (false !== $this->groupModel->save()) {
                    $this->success('编辑成功！');
                } else {
                    $this->error('编辑失败！');
                }
            } else {
                $this->error($this->groupModel->getError());
            }
        } 
    }  
    
    
    /**
     * 删除
     */
    public function del ()
    {
        $group_id = I('get.id', 0, 'intval');
        
        if ($this->groupModel->where(array("id"=>$group_id))->setField("status", 2)) {
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        } 
    } 
}
