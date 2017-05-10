<?php
namespace Admin\Controller;
use Common\Controller\AdminController;

class GroupController extends AdminController
{
    protected $groupModel;
    protected $treeModel;
    protected $ruleModel;
    protected $groupRuleModel;

    public function _initialize() {
        parent::_initialize();
        
        $this->groupModel = D('Common/AuthGroup');
        $this->ruleModel  = D('Common/AuthRule');
        $this->treeModel  = new \Org\Util\Tree();
        $this->groupRuleModel = M('AuthGroupRule');
    }
    
     /**
     * 列表
     */
    public function index() 
    {
        $group_lists = $this->lists($this->groupModel, array('status'=>1), 'id desc');
        
        $this->assign("group_lists", $group_lists);
        $this->assign("meta-title", "权限组列表");
        $this->display();
    }
    
    /**
     * 添加
     */
    public function add ()
    {
        $this->assign("meta-title", "权限组添加");
        $this->display();
    }  
    
    /**
     * 添加提交
     */
    public function do_add ()
    {
        if (IS_POST) {
            if ($this->groupModel->create()) {
                if (false !== $this->groupModel->add()) {
                    $this->success('添加成功！', U('Group/index'));
                } else {
                    $this->error('添加失败！');
                }
            } else {
                $this->error($this->groupModel->getError());
            }
        } 
    }   
    
    /**
     * 编辑
     */
    public function edit ()
    {
        $group_id = I('get.id', 0, 'intval');
        
        $group = $this->groupModel->find($group_id);
        
        $this->assign("group", $group);
        $this->assign("meta-title", "权限组编辑");
        $this->display();
    }   
    
    /**
     * 编辑提交
     */
    public function do_edit ()
    {
        if (IS_POST) {
            if ($this->groupModel->create()) {
                if (false !== $this->groupModel->save()) {
                    $this->success('编辑成功！');
                } else {
                    $this->error('编辑失败！');
                }
            } else {
                $this->error($this->groupModel->getError());
            }
        } 
    }  
    
    
    /**
     * 删除
     */
    public function del ()
    {
        $group_id = I('get.id', 0, 'intval');
        
        if ($this->groupModel->where(array("id"=>$group_id))->setField("status", 2)) {
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        } 
    } 
    
    /**
     * 访问授权
     */
    public function visited () 
    {
        $group_id = I('get.id', 0, 'intval');
        
        //组权限
        $group_rules = $this->groupRuleModel->where(array("status"=>1, 'group_id'=>$group_id))->getField("rule_id", TRUE);
        
        //全部
        $rule_lists = $this->ruleModel->where(array("status"=>1))->order("sort asc,id asc")->select();
        
        foreach ($rule_lists as &$list) {
            $list['checked'] = in_array($list['id'], $group_rules) ? "checked" : ""; 
        }
        
        $this->treeModel->init($rule_lists);
        $rules = $this->treeModel->get_tree_array(0, '');
        
        $this->assign("rules", $rules);
        $this->assign('group_id', $group_id);
        $this->assign('meta-title', "访问授权");
        $this->display();
    }
    
    /**
     * 访问提交
     */
    public function do_visited () 
    {  
        if (IS_POST) {
            $group_id = I('post.id', 0, 'intval');
            
            $ids = I('post.rule_id');
            
            $sign = true;
            
            $this->authGroupRuleModel->where(array("group_id"=>$group_id))->delete();
            
            if (is_array($ids) && !empty($ids)) {
                
                $ruleData = array();
                
                foreach ($ids as $rule_id) {
                    array_push($ruleData, array("group_id"=>$group_id, "rule_id"=>$rule_id));
                }
                
                if (false == $this->groupRuleModel->addAll($ruleData)) {
                    $sign = false;
                }
            }
            
            if ($sign) {
                $this->success("授权成功！");
            } else {
                $this->error("授权失败！");
            }
        }    
    }
}
