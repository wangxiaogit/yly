<?php
namespace Admin\Controller;
use Common\Controller\AdminController;

class RuleController extends AdminController
{
    protected $ruleModel;
    protected $treeModel;

    public function _initialize() {
        parent::_initialize();
        
        $this->ruleModel = D('Common/AuthRule');
        $this->treeModel = new \Org\Util\Tree();
    }
    
    /**
     * 列表
     */
    public function index() 
    {
        $rule_lists = $this->ruleModel->where(array('status'=>1))->order("sort asc, id asc")->select();
        
        foreach ($rule_lists as $list) {
            $rules[$list['id']] = $list;
        }
        
        foreach ($rule_lists as $n => $r) {
            $level = _get_level($r['id'], $rules);
            
            $rule_lists[$n]['level'] = $level;
            $rule_lists[$n]['parentid_node'] = ($r['parentid']) ? ' class="child-of-node-' . $r['parentid'] . '"' : '';
            
            if ($level) {
                $str_manage = '<a href="' . U("Rule/edit", array("id" => $r['id'])) . '">编辑</a> | <a class="js-ajax-delete" href="' . U("Rule/del", array("id" => $r['id']) ). '">删除</a> ';
            } else {
                $str_manage = '<a href="' . U("Rule/add", array("parentid" => $r['id'])) . '">添加子节点</a> | <a href="' . U("Rule/edit", array("id" => $r['id'])) . '">编辑</a> | <a class="js-ajax-delete" href="' . U("Rule/del", array("id" => $r['id']) ). '">删除</a> ';
            }
            
            $rule_lists[$n]['str_manage'] = $str_manage;
        }
        
        $this->treeModel->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
        $this->treeModel->nbsp = '&nbsp;&nbsp;&nbsp;';
        
        $this->treeModel->init($rule_lists);
        $str = "<tr id='node-\$id' \$parentid_node>
                    <td style='padding-left:20px;'><input name='listorders[\$id]' type='text' size='3' value='\$sort' class='input input-order'></td>
                    <td>\$id</td>
                    <td>\$spacer\$title</td>
                    <td>\$url</td>
                    <td>\$str_manage</td>
		</tr>";
        $categorys = $this->treeModel->get_tree(0, $str);
        
        $this->assign('categorys', $categorys);
        $this->assign('meta-title', '权限节点列表');
        $this->display();
    }
    
    /**
     * 添加
     */
    public function add ()
    {
        $parentid = I('get.parentid', 0, 'intval');
        
        $rule_lists = $this->ruleModel->where(array('status'=>1))->order("sort asc")->select();
        
        foreach ($rule_lists as &$list) {
            
            $list['selected'] = (($list['id'] == $parentid) && $parentid) ? 'selected':'';
        }
        
        $str = "<option value='\$id' \$selected>\$spacer \$title</option>";
        $this->treeModel->init($rule_lists);
        $select_categorys = $this->treeModel->get_tree(0, $str);
        
        $this->assign("select_categorys", $select_categorys);
        $this->assign("meta-title", "权限节点添加");
        $this->display();
    }  
    
    /**
     * 添加提交
     */
    public function do_add ()
    {
         if (IS_POST) {
            if ($this->ruleModel->create()) {
                if (false !== $this->ruleModel->add()) {
                    $this->success('添加成功！', U('Rule/index'));
                } else {
                    $this->error('添加失败！');
                }
            } else {
                $this->error($this->ruleModel->getError());
            }
        } 
    }   
    
    /**
     * 编辑
     */
    public function edit ($id)
    {   
        $rule = $this->ruleModel->find($id);
        if (empty($rule)) {
            $this->error('未获取菜单信息！');
        }
        
        $rule_lists = $this->ruleModel->where(array('status'=>1))->order("sort asc")->select();
        
        foreach ($rule_lists as &$list) {
            $list['selected'] = (($list['id'] == $rule['parentid']) && $rule['parentid']) ? 'selected':'';
        }
        
        $str = "<option value='\$id' \$selected>\$spacer \$title</option>";
        $this->treeModel->init($rule_lists);
        $select_categorys = $this->treeModel->get_tree(0, $str);
        
        $this->assign("rule", $rule);
        $this->assign("select_categorys", $select_categorys);
        $this->assign("meta-title", "权限节点编辑");
        $this->display();
    }   
    
    /**
     * 编辑提交
     */
    public function do_edit ()
    {
        if (IS_POST) {
           if ($this->ruleModel->create()) {
                if (false !== $this->ruleModel->save()) {
                    $this->success('编辑成功！', U('Rule/index'));
                } else {
                    $this->error('编辑失败！');
                }
            } else {
                $this->error($this->ruleModel->getError());
            }
        } 
    }  
    
    
    /**
     * 删除
     */
    public function del ()
    {
        $rule_id = I('get.id', 0, 'intval');
        
        $sub_rule = $this->ruleModel->where(array("parentid"=>$rule_id, 'status'=>1))->count();
        if ($sub_rule) {
            $this->error("该菜单下还有子节点,无法删除！");
        }
        
        if ($this->ruleModel->where(array("id"=>$rule_id))->setField("status", 2)) {
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        } 
    }
    
    /**
     * 排序
     */
    public function sort () 
    {
        if (parent::_listorders($this->ruleModel, 'sort')) {
           $this->success('排序成功！');
        }
    }
}
