<?php
namespace Admin\Controller;
use Common\Controller\AdminController;

class FlowTypeController extends AdminController
{
    protected $flowTypeModel;
    protected $caseTypeModel;

    public function _initialize() {
        parent::_initialize();
        
        $this->flowTypeModel = D('Common/WorkflowType');
        $this->caseTypeModel = M('TypeList');
    }
    
    /**
     * 列表
     */
    public function index() 
    {
        $model = $this->flowTypeModel->alias('a')->join("type_list b on a.case_type_id = b.id");
        
        $flowType_lists = $this->lists($model, array('a.status'=>1), 'case_type_id asc', "a.*,b.type_name case_type_name");
        
        $this->assign("flowType_lists", $flowType_lists);
        $this->assign("meta-title", "流程类型列表");
        $this->display();
    }
    
    /**
     * 添加
     */
    public function add ()
    {
        $caseType_lists = $this->caseTypeModel->where(array("isvalid"=>1))->select();
        
        $this->assign("caseType_lists", $caseType_lists);
        $this->assign("meta-title", "流程类型添加");
        $this->display();
    }  
    
    /**
     * 添加提交
     */
    public function do_add ()
    {
        if (IS_POST) {
            if ($this->flowTypeModel->create()) {
                if (false !== $this->flowTypeModel->add()) {
                    $this->success('添加成功！', U('FlowType/index'));
                } else {
                    $this->error('添加失败！');
                }
            } else {
                $this->error($this->flowTypeModel->getError());
            }
        } 
    }   
    
    /**
     * 编辑
     */
    public function edit ()
    {
        $id = I('get.id', 0, 'intval');
        
        $flowType = $this->flowTypeModel->find($id);
        
        $caseType_lists = $this->caseTypeModel->where(array("isvalid"=>1))->select();
        
        $this->assign("caseType_lists", $caseType_lists);
        $this->assign("flowType", $flowType);
        $this->assign("meta-title", "流程类型编辑");
        $this->display();
    }   
    
    /**
     * 编辑提交
     */
    public function do_edit ()
    {
        if (IS_POST) {
            if ($this->flowTypeModel->create()) {
                if (false !== $this->flowTypeModel->save()) {
                    $this->success('编辑成功！');
                } else {
                    $this->error('编辑失败！');
                }
            } else {
                $this->error($this->flowTypeModel->getError());
            }
        } 
    }  
    
    /**
     * 删除
     */
    public function del ()
    {
        $id = I('get.id', 0, 'intval');
        
        $flowVersions = D('Common/WorkflowVersion')->where(array("workflow_type_id"=>$id))->count();
        if ($flowVersions) {
            $this->error("该流程类型下还有流程版本！暂不能删除");
        } 
        
        if ($this->flowTypeModel->where(array("id"=>$id))->setField("status", 2)) {
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        } 
    } 
   
}
