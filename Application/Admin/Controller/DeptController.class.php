<?php
namespace Admin\Controller;
use Common\Controller\AdminController;

class DeptController extends AdminController
{
    protected $deptModel;
    protected $treeModel;
    protected $organize_id;

    public function _initialize() {
        parent::_initialize();
        
        $this->deptModel = D('Common/Dept');
        $this->treeModel = new \Org\Util\Tree();
        
        $this->organize_id = I('get.org_id', 0, 'intval');
    }
    
    /**
     * 列表
     */
    public function index ()
    { 
        $dept_lists = $this->deptModel->where(array('status'=>1, 'org_id'=>$this->organize_id))->order("sort asc")->select();
        
        foreach ($dept_lists as $list) {
            $depts[$list['id']] = $list;
        }
        
        foreach ($dept_lists as $n => $r) {
            $dept_lists[$n]['level'] = _get_level($r['id'], $menus);
            $dept_lists[$n]['parentid_node'] = ($r['parentid']) ? ' class="child-of-node-' . $r['parentid'] . '"' : '';

            $dept_lists[$n]['str_manage'] = '<a href="' . U("Dept/add", array("parentid" => $r['id'], "org_id" => $organize_id)) . '">添加子部门</a> | <a href="' . U("Dept/edit", array("id" => $r['id'], "org_id" => $organize_id)) . '">编辑</a> | <a class="js-ajax-delete" href="' . U("Dept/del", array("id" => $r['id'], "org_id" => $organize_id) ). '">删除</a> ';
        }
        
        $this->treeModel->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
        $this->treeModel->nbsp = '&nbsp;&nbsp;&nbsp;';
        
        $this->treeModel->init($menu_lists);
        $str = "<tr id='node-\$id' \$parentid_node>
                    <td style='padding-left:20px;'><input name='listorders[\$id]' type='text' size='3' value='\$sort' class='input input-order'></td>
                    <td>\$id</td>
                    <td>\$spacer\$name</td>
                    <td>\$str_manage</td>
		</tr>";
        $categorys = $this->treeModel->get_tree(0, $str);
        
        $this->assign('categorys', $categorys);
        $this->assign('meta-title', '部门列表');
        $this->display();
    }
    
    /**
     * 添加
     */
    public function add ()
    {
        $parentid = I('get.parentid', 0, 'intval');
        
        $dept_lists = $this->menuModel->where(array('status'=>1, 'org_id'=>$this->organize_id))->order("sort asc")->select();
        
        foreach ($dept_lists as &$list) {
            $list['selected'] = (($list['id'] == $parentid) && $parentid) ? 'selected':'';
        }
        
        $str = "<option value='\$id' \$selected>\$spacer \$name</option>";
        $this->treeModel->init($dept_lists);
        $select_categorys = $this->treeModel->get_tree(0, $str);
        
        $this->assign("select_categorys", $select_categorys);
        $this->assign("meta-title", "部门添加");
        $this->display();
    }  
    
    /**
     * 添加提交
     */
    public function do_add ()
    {
        if (IS_POST) {
            if ($this->deptModel->create()) {
                if (false !== $this->deptModel->add()) {
                    $this->success('添加成功！', U('Dept/index',array('org_id'=>$this->organize_id)));
                } else {
                    $this->error('添加失败！');
                }
            } else {
                $this->error($this->deptModel->getError());
            }
        } 
    }   
    
    /**
     * 编辑
     */
    public function edit ($id)
    {   
        $dept = $this->deptModel->find($id);
        if (empty($dept)) {
            $this->error('未获取部门信息！');
        }
        
        $dept_lists = $this->deptModel->where(array('status'=>1, 'org_id'=>$this->organize_id))->order("sort asc")->select();
        
        foreach ($dept_lists as &$list) {
            $list['selected'] = (($list['id'] == $parentid) && $parentid) ? 'selected':'';
        }
        
        $str = "<option value='\$id' \$selected>\$spacer \$name</option>";
        $this->treeModel->init($dept_lists);
        $select_categorys = $this->treeModel->get_tree(0, $str);
        
        $this->assign("dept", $dept);
        $this->assign("select_categorys", $select_categorys);
        $this->assign("meta-title", "部门编辑");
        $this->display();
    }   
    
    /**
     * 编辑提交
     */
    public function do_edit ()
    {
        if (IS_POST) {
            if ($this->deptModel->create()) {
                if (false !== $this->deptModel->save()) {
                    $this->success('编辑成功！');
                } else {
                    $this->error('编辑失败！');
                }
            } else {
                $this->error($this->deptModel->getError());
            }
        } 
    }  
    
    /**
     * 删除
     */
    public function del ()
    {
        $dept_id = I('get.id', 0, 'intval');
        
        if ($this->deptModel->where(array("id"=>$dept_id))->setField("status", 2)) {
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        } 
    }
}
