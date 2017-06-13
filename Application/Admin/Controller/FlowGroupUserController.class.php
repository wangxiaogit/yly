<?php
namespace Admin\Controller;
use Common\Controller\AdminController;

class FlowGroupUserController extends AdminController 
{
    protected $flowGroupUserModel;
    protected $flowGroupId;

    public function _initialize() {
        parent::_initialize();
        
        $this->flowGroupUserModel = D('Common/WorkflowGroupUser');
        
        $this->flowGroupId = I('request.group_id');
    }
    
    /**
     * 列表
     */
    public function index () 
    {
        $model = $this->flowGroupUserModel->alias('a')->join("workflow_group b on a.group_id=b.id")->join("user c on a.user_id=c.id");
        
        $flowGroupUser_Lists = $this->lists($model, array("a.group_id"=>$this->flowGroupId, 'a.status'=>1), "id desc", "a.*, b.name group_name,c.true_name user_name");
        
        $this->assign("flowGroupUser_Lists", $flowGroupUser_Lists);
        $this->assign("group_id", $this->flowGroupId);
        $this->assign("meta-title", "流程组人员列表");
        $this->display();
    }
    
    /**
     * 添加
     */
    public function add () 
    {
        $user_lists = D('Common/User')->where(array("status"=>1,'org_id'=>1))->select();
        
        $group_lists = D("Common/WorkflowGroup")->where(array("status"=>1))->select();
        
        $this->assign("user_lists", $user_lists);
        $this->assign("group_lists", $group_lists);
        $this->assign("group_id", $this->flowGroupId);
        $this->assign("meta-title", "流程组人员添加");
        
        $this->display();
    }
    
    /**
     * 提交添加
     */
    public function do_add ()
    {
        if (IS_POST) {
            if ($this->flowGroupUserModel->create()) {
                if (false !== $this->flowGroupUserModel->add()) {
                    $this->success('添加成功！', U('FlowGroupUser/index', array("group_id"=>$this->flowGroupId)));
                } else {
                    $this->error('添加失败！');
                }
            } else {
                $this->error($this->flowGroupUserModel->getError());
            }
        } 
    }
    
    /**
     * 编辑
     */
    public function edit ()
    {
        $id = I('get.id', 0, 'intval');
        
        $flowGroupUser = $this->flowGroupUserModel->find($id);
        
        $user_lists = D('Common/User')->where(array("status"=>1,'org_id'=>1))->select();
        
        $group_lists = D("Common/WorkflowGroup")->where(array("status"=>1))->select();
        
        $this->assign("user_lists", $user_lists);
        $this->assign("group_lists", $group_lists);
        $this->assign("flowGroupUser", $flowGroupUser);
        $this->assign("group_id", $this->flowGroupId);
        $this->assign("meta-title", "流程组人员编辑");

        $this->display();
    }
    
    /**
     * 提交编辑
     */
    public function do_edit ()
    {
        if (IS_POST) {
            if ($this->flowGroupUserModel->create()) {
                if (false !== $this->flowGroupUserModel->save()) {
                    $this->success('编辑成功！', U('FlowGroupUser/index', array("group_id"=>$this->flowGroupId)));
                } else {
                    $this->error('编辑失败！');
                }
            } else {
                $this->error($this->flowGroupUserModel->getError());
            }
        } 
    }
    
    /**
     * 删除
     */
    public function del ()
    {
        $id = I('get.id', 0, 'intval');
        
        if ($this->flowGroupUserModel->where(array("id"=>$id))->setField("status", 2)) {
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        } 
    }        
}
