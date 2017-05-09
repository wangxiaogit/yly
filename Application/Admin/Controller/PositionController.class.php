<?php
namespace Admin\Controller;
use Common\Controller\AdminController;

class PositionController extends AdminController
{
    protected $positionModel;
    
    public function _initialize() {
        parent::_initialize();
        
        $this->positionModel = D('Common/Position');
    }
    
     /**
     * 列表
     */
    public function index() 
    {
        $position_lists = $this->lists($this->positionModel, array('status'=>1), 'id desc');
        
        $this->assign("position_lists", $position_lists);
        $this->assign("meta-title", "职位列表");
        $this->display();
    }
    
    /**
     * 添加
     */
    public function add ()
    {
        $this->assign("meta-title", "职位添加");
        $this->display();
    }  
    
    /**
     * 添加提交
     */
    public function do_add ()
    {
        if (IS_POST) {
            if ($this->positionModel->create()) {
                if (false !== $this->positionModel->add()) {
                    $this->success('添加成功！', U('Position/index'));
                } else {
                    $this->error('添加失败！');
                }
            } else {
                $this->error($this->positionModel->getError());
            }
        } 
    }   
    
    /**
     * 编辑
     */
    public function edit ()
    {
        $position_id = I('get.id', 0, 'intval');
        
        $position = $this->positionModel->find($organize_id);
        
        $this->assign("position", $position);
        $this->assign("meta-title", "职位编辑");
        $this->display();
    }   
    
    /**
     * 编辑提交
     */
    public function do_edit ()
    {
        if (IS_POST) {
            if ($this->positionModel->create()) {
                if (false !== $this->positionModel->save()) {
                    $this->success('编辑成功！');
                } else {
                    $this->error('编辑失败！');
                }
            } else {
                $this->error($this->positionModel->getError());
            }
        } 
    }  
    
    
    /**
     * 删除
     */
    public function del ()
    {
        $position_id = I('get.id', 0, 'intval');
        
        if ($this->positionModel->where(array("id"=>$position_id))->setField("status", 2)) {
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        } 
    } 
}
