<?php
namespace Admin\Controller;
use Common\Controller\AdminController;

class FlowBankController extends AdminController
{
    protected $flowBankModel;
    protected $organizeModel;
    protected $deptModel;

    public function _initialize() {
        parent::_initialize();
        
        $this->flowBankModel = D('Common/WorkflowBank');
        $this->organizeModel = D('Common/Organize');
        $this->deptModel = D('Common/Dept');
    }
    
    /**
     * 列表
     */
    public function index() 
    {
        //总行
        $organize_id = I('post.org_id', 0, 'intval');
        if ($organize_id) {
            $where['a.organize_id'] = $organize_id;
            
            $this->assign('dept_lists', $this->deptModel->where(array("org_id"=>$organize_id, "status"=>1))->select());
        }
        
        //支行
        $dept_id = I('post.dept_id', 0, 'intval');
        if ($dept_id) {
            $where['a.dept_id'] = $dept_id;
        }
        
        $where['a.status'] = 1;
        
        $model = $this->flowBankModel->alias('a')->join('organize b on a.organize_id = b.id')->join('dept c on a.dept_id = c.id');
        
        $flowBank_lists = $this->lists($model, $where, 'organize_id desc,dept_id asc', 'a.*,b.name organize_name, c.name dept_name');
        
        $organize_lists = $this->organizeModel->where(array("type"=>2, 'status'=>1))->select();
        
        $this->assign("flowBank_lists", $flowBank_lists);
        $this->assign("organize_lists", $organize_lists);
        $this->assign("search", I('post.'));
        $this->assign("meta-title", "流程银行列表");
        $this->display();
    }
    
    /**
     * 添加
     */
    public function add ()
    {
        $organize_lists = $this->organizeModel->where(array("type"=>2, 'status'=>1))->select();
        
        $this->assign("organize_lists", $organize_lists);
        $this->assign("meta-title", "流程银行添加");
        $this->display();
    }  
    
    /**
     * 添加提交
     */
    public function do_add ()
    {
        if (IS_POST) {
            if ($this->flowBankModel->create()) {
                if (false !== $this->flowBankModel->add()) {
                    $this->success('添加成功！', U('FlowBank/index'));
                } else {
                    $this->error('添加失败！');
                }
            } else {
                $this->error($this->flowBankModel->getError());
            }
        } 
    }   
    
    /**
     * 编辑
     */
    public function edit ()
    {
        $id = I('get.id', 0, 'intval');
        
        $flowBank = $this->flowBankModel->find($id);
        
        $organize_lists = $this->organizeModel->where(array("type"=>2, 'status'=>1))->select();
        
        $dept_lists = $this->deptModel->where(array("org_id"=>$flowBank['organize_id'], "status"=>1))->select();
        
        if ($flowBank['handle_type'] == 1) {
            
            $hanle_lists = D('Common/User')->field("id, true_name as name")->where(array("org_id"=>1, 'status'=>1))->select(); 
            
        } elseif ($flowBank['handle_type'] == 2) {
            
            $hanle_lists = D('Common/WorkflowGroup')->field("id, name")->where(array('status'=>1))->select();;
        }
        
        $this->assign("organize_lists", $organize_lists);
        $this->assign("dept_lists", $dept_lists);
        $this->assign("hanle_lists", $hanle_lists);
        $this->assign("flowBank", $flowBank);
        $this->assign("meta-title", "流程银行编辑");
        $this->display();
    }   
    
    /**
     * 编辑提交
     */
    public function do_edit ()
    {
        if (IS_POST) {
            if ($this->flowBankModel->create()) {
                if (false !== $this->flowBankModel->save()) {
                    $this->success('编辑成功！');
                } else {
                    $this->error('编辑失败！');
                }
            } else {
                $this->error($this->flowBankModel->getError());
            }
        } 
    }  
    
    
    /**
     * 删除
     */
    public function del ()
    {
        $id = I('get.id', 0, 'intval');
        
        if ($this->flowBankModel->where(array("id"=>$id))->setField("status", 2)) {
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        } 
    } 
}
