<?php
namespace Admin\Controller;
use Common\Controller\BaseController;

class LoginController extends BaseController
{
    protected $userModel;
    
    public function _initialize() 
    {
        parent::_initialize();
        
        $this->userModel = D('Common/User');
    } 
    
    /**
     * 登录
     */
    public function index () 
    {
        $this->assign('meta_title', "登录");
        $this->display();
    }
    
    /**
     * 提交登录
     */
    public function do_index () 
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
        
        $this->redirect('Login/index');  
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
