<?php
namespace Admin\Controller;
use Common\Controller\AdminController;

class UserController extends AdminController
{
    protected $userModel;
    protected $organizeModel;
    
    public function _initialize() 
    {
        parent::_initialize();
        
        $this->userModel = D('Common/User');
        $this->organizeModel = D('Common/Organize');
    } 
    
    /**
     * 用户列表
     */
    public function index () 
    {
        $keywords = I('post.keywords', '', 'trim');
        if ($keywords) {
            $where['a.user_name|a.true_name|a.name_py|a.phone'] = array('like', '%'.$keywords); 
        }
        
        $org_type_id = I('get.org_type', 1);    
        if ($org_type_id) {
            $where['a.org_type'] = $org_type_id;
        }
        
        $model = $this->userModel->alias('a')
                      ->join("organize b on a.org_id = b.id", 'LEFT')
                      ->join('dept c on a.dept_id = c.id', 'LEFT')
                      ->join('position d on a.position_id = d.id', 'LEFT');
        
        $user_lists = $this->lists($model, $where, "a.org_id asc, a.dept_id asc", 'a.*, b.name organize_name, c.name dept_name, d.name position_name');
        
        $this->assign("organize_types", $this->organizeModel->ORGANIZE_TYPE);
        $this->assign("user_lists", $user_lists);
        $this->assign("type", $org_type_id);
        $this->assign("search", I('post.'));
        $this->assign("meta-title", "用户列表");
        $this->display();
    }
    
    /**
     * 添加
     */
    public function add () 
    {
        $position_lists = D('Common/Position')->where(array("status"=>1))->select();
        
        $authGroup_lists = D('Common/AuthGroup')->where(array('status'=>1))->select();
        
        $this->assign("organize_types", $this->organizeModel->ORGANIZE_TYPE);
        $this->assign("position_lists", $position_lists);
        $this->assign("authGroup_lists", $authGroup_lists);
        $this->assign("type", I('get.org_type'));
        $this->assign("meta-title", "用户添加");
        $this->display();
    }
    
   /**
     * 添加提交
     */
    public function do_add ()
    {
        if (IS_POST) {
            if ($this->userModel->create()) {
                if (false !== $this->userModel->add()) {
                    $this->success('添加成功！', U('User/index', array('org_type'=>I('get.org_type'))));
                } else {
                    $this->error('添加失败！');
                }
            } else {
                $this->error($this->userModel->getError());
            }
        } 
    }
    
    /**
     * 编辑
     */
    public function edit ()
    {
        $user_id = I('get.id', 0, 'intval');
        
        $user = $this->userModel->find($user_id);
        
        $authGroup_lists = D('Common/AuthGroup')->where(array("status"=>1))->select();
        
        $authUser_lists = M('AuthGroupUser')->where(array("status"=>1, "user_id"=>$user_id))->getField("group_id", true);
        
        $position_lists = D('Common/Position')->where(array("status"=>1))->select();
        
        $organize_lists = $this->organizeModel->where(array('status'=>1, 'type'=>$user['org_type']))->select();
        
        $dept_lists = D('Common/Dept')->where(array('status'=>1,'org_id'=>$user['org_id']))->select();
        
        $this->assign("user", $user);
        $this->assign("position_lists", $position_lists);
        $this->assign("organize_lists", $organize_lists);
        $this->assign("authGroup_lists", $authGroup_lists);
        $this->assign("authUser_lists", $authUser_lists);
        $this->assign("dept_lists", $dept_lists);
        $this->assign("organize_types", $this->organizeModel->ORGANIZE_TYPE);
        $this->assign("type", I('get.org_type'));
        $this->assign("meta-title", "用户编辑");
        $this->display();
    }
    
    /**
     * 编辑提交
     */
    public function do_edit ()
    {
        if (IS_POST) {
            if ($this->userModel->create()) {
                if (false !== $this->userModel->save()) {
                    $this->success('编辑成功！', U('User/index',array('org_type'=>I('get.org_type'))));
                } else {
                    $this->error('编辑失败！');
                }
            } else {
                $this->error($this->userModel->getError());
            }
        } 
    }        
    
    /**
     * 删除
     */
    public function del () 
    {
        $user_id = I('get.id', 0, 'intval');
        
        if ($this->userModel->where(array("id"=>$user_id))->setField("status", 2)) {
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        } 
    }
    
    /**
     * ajax 用户
     */
    public function ajax_get_user () 
    {
        $user_lists = $this->userModel->field("id, true_name as name")->where(array("org_id"=>1, 'status'=>1))->select(); 
        
        $this->ajaxReturn(['data'=>$user_lists, 'status'=>1]);
    }
    
}
