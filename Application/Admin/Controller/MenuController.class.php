<?php
namespace Admin\Controller;
use Common\Controller\AdminController;

class MenuController extends AdminController
{
    protected $menuModel;
    protected $treeModel;

    public function _initialize() {
        parent::_initialize();
        
        $this->menuModel = D('Common/Menu');
        $this->treeModel = new \Org\Util\Tree();
    }
    
    /**
     * 列表
     */
    public function index() 
    {
        $menu_lists = $this->menuModel->where(array('status'=>1))->order("sort asc")->select();
        
        foreach ($menu_lists as $list) {
            $menus[$list['id']] = $list;
        }
        
        foreach ($menu_lists as $n => $r) {
            $menu_lists[$n]['level'] = _get_level($r['id'], $menus);
            $menu_lists[$n]['parentid_node'] = ($r['parentid']) ? ' class="child-of-node-' . $r['parentid'] . '"' : '';

            $menu_lists[$n]['str_manage'] = '<a href="' . U("Menu/add", array("parentid" => $r['id'], "menuid" => I("get.menuid"))) . '">添加子菜单</a> | <a href="' . U("Menu/edit", array("id" => $r['id'], "menuid" => I("get.menuid"))) . '">编辑</a> | <a class="js-ajax-delete" href="' . U("Menu/delete", array("id" => $r['id'], "menuid" => I("get.menuid")) ). '">删除</a> ';
            $menu_lists[$n]['is_display'] = $r['is_display'] ? '显示' : '隐藏';
        }
        
        $this->treeModel->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
        $this->treeModel->nbsp = '&nbsp;&nbsp;&nbsp;';
        
        $this->treeModel->init($menu_lists);
        $str = "<tr id='node-\$id' \$parentid_node>
                    <td style='padding-left:20px;'><input name='listorders[\$id]' type='text' size='3' value='\$sort' class='input input-order'></td>
                    <td>\$id</td>
                    <td>\$url</td>
                    <td>\$spacer\$title</td>
                    <td>\$is_display</td>
                    <td>\$str_manage</td>
		</tr>";
        $categorys = $this->treeModel->get_tree(0, $str);
        
        $this->assign('categorys', $categorys);
        $this->assign('meta-title', '菜单列表');
        $this->display();
    }
    
    /**
     * 添加
     */
    public function add ()
    {
        $parentid = I('get.parentid', 0, 'intval');
        
        $menu_lists = $this->menuModel->where(array('status'=>1))->order("sort asc")->select();
        
        foreach ($menu_lists as &$list) {
            $list['selected'] = ($list['parentid'] == $parentid) ? 'selected':'';
        }
        
        $str = "<option value='\$id' \$selected>\$spacer \$title</option>";
        $this->treeModel->init($menu_lists);
        $select_categorys = $this->treeModel->get_tree(0, $str);
        
        $this->assign("select_categorys", $select_categorys);
        $this->assign("meta-title", "菜单添加");
        $this->display();
    }  
    
    /**
     * 添加提交
     */
    public function do_add ()
    {
         if (IS_POST) {
            if ($this->menuModel->create()) {
                if (false !== $this->menuModel->add()) {
                    $this->success('添加成功！', U('Menu/index'));
                } else {
                    $this->error('添加失败！');
                }
            } else {
                $this->error($this->menuModel->getError());
            }
        } 
    }   
    
    /**
     * 编辑
     */
    public function edit ($id)
    {   
        $menu = $this->menuModel->find($id);
        if (empty($menu)) {
            $this->error('未获取菜单信息！');
        }
        
        $menu_lists = $this->menuModel->where(array('status'=>1))->order("sort asc")->select();
        
        foreach ($menu_lists as &$list) {
            $list['selected'] = ($list['parentid'] == $menu['parentid']) ? 'selected':'';
        }
        
        $str = "<option value='\$id' \$selected>\$spacer \$title</option>";
        $this->treeModel->init($menu_lists);
        $select_categorys = $this->treeModel->get_tree(0, $str);
        
        $this->assign("menu", $menu);
        $this->assign("select_categorys", $select_categorys);
        $this->assign("meta-title", "菜单编辑");
        $this->display();
    }   
    
    /**
     * 编辑提交
     */
    public function do_edit ()
    {
        if (IS_POST) {
           if ($this->menuModel->create()) {
                if (false !== $this->menuModel->save()) {
                    $this->success('编辑成功！', U('Menu/index'));
                } else {
                    $this->error('编辑失败！');
                }
            } else {
                $this->error($this->menuModel->getError());
            }
        } 
    }  
    
    
    /**
     * 删除
     */
    public function del ()
    {
        $menu_id = I('get.id', 0, 'intval');
        
        $sub_menu = $this->menuModel->where(array("parentid"=>$menu_id, 'status'=>1))->count();
        if ($sub_menu) {
            $this->error("该菜单下还有子菜单,无法删除！");
        }
        
        if ($this->menuModel->where(array("id"=>$menu_id))->setField("status", 2)) {
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
        if (parent::_listorders($this->menuModel, 'listorder')) {
           $this->success('排序成功！');
        }
    }
}

