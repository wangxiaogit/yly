<?php
namespace Admin\Controller;
use Common\Controller\AdminController;

class MainController extends AdminController
{
    public function _initialize() {
        parent::_initialize();
    }
    
    /**
     * 首页
     */
    public function index() 
    {
        $this->display();
    }
}
