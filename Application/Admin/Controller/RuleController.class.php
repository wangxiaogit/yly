<?php
namespace Admin\Controller;
use Common\Controller\AdminController;

class RuleController extends AdminController
{
    protected $ruleModel;
    
    public function _initialize() {
        parent::_initialize();
        
        $this->ruleModel = D('Common/AuthRule');
    }
}
