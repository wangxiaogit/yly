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
        $organize_type = I('get.type', 1, 'intval');//机构类型
        if ($organize_type) {
            $where['organize.type'] = $organize_type;
        }
        
        $where['organize.status'] = 1;
        
        $model = $this->organizeModel->join('user on organize.create_uid=user.id', 'LEFT');
        
        $organize_lists = $this->lists($model, $where, 'id desc', 'organize.*, user.user_name create_name');
        
        $this->assign("type", $organize_type);
        $this->assign("organize_lists", $organize_lists);
        $this->assign("organize_types", $this->organizeModel->ORGANIZE_TYPE);
        $this->assign("meta-title", "机构列表");
        $this->display();
    }
    
    /**
     * 添加
     */
    public function add ()
    {
        $this->assign("type", I('get.type'));
        $this->assign("organize_types", $this->organizeModel->ORGANIZE_TYPE);
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
                    $this->success('添加成功！', U('Organize/index',array('type'=>I('get.type'))));
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
    public function edit ()
    {
        $organize_id = I('get.id', 0, 'intval');
        
        $organize = $this->organizeModel->find($organize_id);
        
        $this->assign("type", I('get.type'));
        $this->assign("organize", $organize);
        $this->assign("organize_types", $this->organizeModel->ORGANIZE_TYPE);
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
                    $this->success('编辑成功！', U('Organize/index',array('type'=>I('get.type'))));
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
    
    /**
     * ajax 机构
     */
    public function ajax_get_organize () 
    {
        $org_type = I('get.org_type', 1, 'intval');
        
        if (!$org_type) {
            $this->error("参数错误！");
        }
        
        $organize_lists = $this->organizeModel->field("id, name")->where(array("type"=> $org_type, 'status'=>1))->select();
        
        $this->ajaxReturn(['data'=>$organize_lists, 'status'=>1]);
    }
}

