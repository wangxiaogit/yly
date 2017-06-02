<?php
namespace Admin\Controller;
use Common\Controller\AdminController;

class FlowConfController extends AdminController
{
    protected $flowConfModel;
    protected $flowTypeModel;
    protected $flowVersionModel;
    protected $flowNodeModel;

    public function _initialize() {
        parent::_initialize();
        
        $this->flowConfModel = D('Common/WorkflowConf');
        $this->flowTypeModel = D('Common/WorkflowType');
        $this->flowVersionModel = D('Common/WorkflowVersion');
        $this->flowNodeModel = D('Common/WorkflowNode');
    }
    
    /**
     * 列表
     */
    public function index() 
    {
        $flowType_id = I('post.type_id', 0, 'intval');
        if ($flowType_id) {
            $where['a.workflow_type_id'] = $flowType_id;
        }
        
        $where['a.status'] = 1;    
        
        $model = $this->flowConfModel
                      ->alias('a')
                      ->join('workflow_type b on a.workflow_type_id = b.id')
                      ->join('workflow_version c on a.workflow_version_id = c.id')
                      ->join('workflow_node_id d on a.workflow_node_id = d.id');
        
        $flowConf_lists = $this->lists($model, $where, "a.*, b.name workflow_type_name, b.version workflow_version, c.name workflow_node_name");
        
        $this->assign("flowConf_lists", $flowConf_lists);
        $this->assign("flowType_lists", $this->flowTypeModel->where(array("status"=>1))->select());
        $this->assign("search", I('request.'));
        $this->assign("meta-title", "流程配置列表");
        $this->display();
    }
    
    /**
     * 添加
     */
    public function add ()
    {
        $this->assign("flowType_lists", $this->flowTypeModel->where(array("status"=>1))->select());
        $this->assign("meta-title", "流程配置添加");
        $this->display();
    }  
    
    /**
     * 添加提交
     */
    public function do_add ()
    {
        if (IS_POST) {
            if ($this->flowConfModel->create()) {
                if (false !== $this->flowConfModel->add()) {
                    $this->success('添加成功！', U('FlowConf/index'));
                } else {
                    $this->error('添加失败！');
                }
            } else {
                $this->error($this->flowConfModel->getError());
            }
        } 
    }   
    
    /**
     * 编辑
     */
    public function edit ()
    {
        $id = I('get.id', 0, 'intval');
        
        $flowConf = $this->flowConfModel->find($id);
        
        $this->assign("flowConf", $flowConf);
        $this->assign("meta-title", "流程配置编辑");
        $this->display();
    }   
    
    /**
     * 编辑提交
     */
    public function do_edit ()
    {
        if (IS_POST) {
            if ($this->flowConfModel->create()) {
                if (false !== $this->flowConfModel->save()) {
                    $this->success('编辑成功！');
                } else {
                    $this->error('编辑失败！');
                }
            } else {
                $this->error($this->flowConfModel->getError());
            }
        } 
    }  
    
    
    /**
     * 删除
     */
    public function del ()
    {
        $id = I('get.id', 0, 'intval');
        
        if ($this->flowConfModel->where(array("id"=>$id))->setField("status", 2)) {
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        } 
    } 
}
