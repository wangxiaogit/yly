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
        $result = $this->treeModel->get_tree(0, $str);
        
        $this->display();
    }
    
    /**
     * 添加
     */
    public function add ()
    {
        
    }  
    
    /**
     * 添加提交
     */
    public function do_add ()
    {
        
    }   
    
    /**
     * 编辑
     */
    public function edit ()
    {
        
    }   
    
    /**
     * 编辑提交
     */
    public function do_edit ()
    {
        
    }  
    
    
    /**
     * 删除
     */
    public function del ()
    {
        
    }
    
    /**
     * 排序
     */
    public function sort () 
    {
        
    }
}

