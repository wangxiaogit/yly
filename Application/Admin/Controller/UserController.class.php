<?php
namespace Admin\Controller;
use Common\Controller\AdminController;

class UserController extends AdminController
{
    protected $userModel;
    
    public function _initialize() 
    {
        parent::_initialize();
        
        $this->userModel = D('Common/User');
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
