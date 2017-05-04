<?php
namespace Admin\Controller;
use Common\Controller\AdminController;

class IndexController extends AdminController 
{
    public function _initialize() {
        parent::_initialize();
    }
    
    public function index ()
    {

        $this->display();
    }
}