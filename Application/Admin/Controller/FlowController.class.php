<?php
namespace Admin\Controller;
use Common\Controller\AdminController;

class FlowController extends AdminController
{
    protected  $flowModel;
    
    public function _initialize() {
        parent::_initialize();
        
        $this->flowModel = D('Common/Workflow');
    }
    
    /**
     * 流程类型
     */
    public function type () 
    {
        
    }
    
    /**
     * 提交类型
     */
    public function do_type () 
    {
        
    }
    
    /**
     * 流程新建
     */
    public function create () 
    {
        
    }
    
    /**
     * 流程办理
     */
    public function handle () 
    {
        
    }
    
    /**
     * 流程查看
     */
    public function view ()
    {
        
    }        
}
