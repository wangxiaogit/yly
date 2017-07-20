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
    public function ajax_get_user ($dept_id = 0, $org_type = 1)
    {   
        $where['org_type'] = $org_type;
        $where['status'] = 1;
        
        if ($dept_id > 0){
            $where['dept_id'] = $dept_id; 
        }
        
        $user_lists = $this->userModel->field("id, true_name as name")->where($where)->select();       
        $this->ajaxReturn(['data'=>$user_lists, 'status'=>1]);
    }
    
    /**
     * 登录
     */
    public function login () 
    {
        $this->assign('meta_title', "登录");
        $this->display();
    }
    
    /**
     * 提交登录
     */
    public function do_login () 
    {
        $verify = I('post.verify_code', '', 'trim');
        if(!check_verify($verify)){
            $this->error('验证码输入错误！');
        }
        
        $rules = array(
                    array('user_name', 'require', '用户名\手机号码不能为空！', 1 ),
                    array('password','require','密码不能为空！',1),
                );
        if ($this->userModel->validate($rules)->create()===false) {
            $this->error($this->userModel->getError());
        }
        
        $user_name = I('post.user_name', '', 'trim');
        
        $userInfo = $this->userModel
                        ->field('id, org_id, org_type, dept_id, user_name, password, true_name, phone, sex, card_no, avatar_url, is_admin')
                        ->where(array('user_name'=>$user_name))
                        ->find();
        
        if (!$userInfo) {
            $this->error('帐号不存在或被禁用！');
        }
        
        $password = I('post.password', '', 'md5');
        
        if ($userInfo['password'] == $password) {
            $this->userModel->afterLogin($userInfo);
            
            $this->success('登录成功！', U('Index/index'));
        } else {
            $this->error('密码错误！');
        }
                
    }
    
    /**
     * 退出
     */
    public function loginout() 
    {
        if(is_login()){
            session_destroy();
        } 
        
        $this->redirect('User/login');  
    }
    
    /**
     * 修改密码
     */
    public function password() 
    {
        $this->assign('meta_title', "修改密码");
        $this->display();
    }
    
    /**
     * 提交修改
     */
    public function do_password() 
    {
        $rules = array(
            //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
            array('old_password', 'require', '原密码不能为空！', 1 ),
            array('new_password','require','新密码不能为空！',1),
            array('confirm_password','require','确认密码不能为空！',1),
            array('verify','require','验证码不能为空！',1),
            
            array('verify','check_verify','验证码错误！', 0, 'function'),
            array('old_password', session('userInfo.password'), '原密码错误！',0,'equal'),
            array('comfirm_password','new_password','确认密码不正确！',0,'confirm')
        );
        
        if ($this->userModel->validate($rules)->create()===false) {
            $this->error($this->userModel->getError());
        }
               
        if ($this->userModel->where(array('id'=>session('userInfo.id')))->setField("password", md5(I('new_password', '', 'trim')))) {
            $this->success("修改成功！", U('User/loginout'));
        } else {
            $this->error("修改失败！");
        }
    }
    
    /**
     * 验证码
     */
    public function verify() 
    {   
        $config =    array(
            'codeSet'     => '0123456789', 
            'useImgBg'    => FALSE,
            'useCurve'    => FALSE,
            'fontSize'    =>    80,    // 验证码字体大小
            'length'      =>    4     // 验证码位数
        );
        ob_clean();

        $verify = new \Think\Verify($config);
        $verify->entry(1);
    }
}
