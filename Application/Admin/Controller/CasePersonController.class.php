<?php
/**
 * 交易人列表控制器
 */
namespace Admin\Controller;
use Common\Controller\AdminController;

class CasePersonController extends AdminController
{
    public function _initialize()
    {
        parent::_initialize();
    }
    
    /**
     * 交易人列表
     */
    public function index($case_id)
    {   
        $model = D('Common/CasePerson');
        if ($case_id) {
        $person_list = $model->where('case_id = '.$case_id)->select();
        }
        $this->assign('tab_arr', get_case_info_tabs());
        $this->assign('company_type', $model->getCompanyType());
        $this->assign('tab','CasePerson/index');
        $this->assign('case_id', $case_id);
        $this->assign('person_list',$person_list);
        $this->display();
    }
    
        
         
    
    /*
     * 增加
     */
    public function add($case_id)
    {   
        $this->assign('case_id', $case_id);
        $this->assign('company_type', D('Common/CasePerson')->getCompanyType());
        $this->display();
    }
    
    
    /*
     * 新增提交
     */
    public function do_add()
    {   
        $model = D('Common/CasePerson');
        if (IS_POST) {
            if ($model->create()) {
                $main_num = $model->where('is_main_loan = 1 and case_id = '.I('case_id'))->count();
                $right_type = I('right_type');
                if ($main_num>0 && $right_type==1)
                {
                    $this->ajaxReturn(array('status'=>0, 'info'=>'主贷人已经存在,添加失败！'));
                    exit;
                }
                $res = $model->add();
                if ($res !==false) {
                    $this->ajaxReturn(array('status'=>1, 'info'=>'添加成功'));
                } else {
                    $this->ajaxReturn(array('status'=>0, 'info'=>'添加失败'));
                }
            } else {
                $this->ajaxReturn(array('status'=>0,'info'=>$model->getError()));
            }
        }
    }
    
    /*
     * 修改
     */
    public function edit($id)
    {   
        $model = D('Common/CasePerson');
        if($id) {
            $info = $model->find($id);
        }
        $this->assign('info', $info);
        $this->assign('company_type', $model->getCompanyType());
        $this->display();
    }

    /**
     * 提交编辑
     */
    public function do_edit()
    {
        $model = D('Common/CasePerson');
        if (IS_POST) {
            if ($model->create()) {
                $main_num = $model->where('is_main_loan = 1 and case_id = '.I('case_id'))->count();
                $right_type = I('right_type');
                if ($main_num>0 && $right_type==1)
                {
                    $this->ajaxReturn(array('status'=>0, 'info'=>'主贷人已经存在,保存失败！'));
                    exit;
                }
                $res = $model->save();
                if ($res !==false) {
                    $this->ajaxReturn(array('status'=>1, 'info'=>'保存成功'));
                } else {
                    $this->ajaxReturn(array('status'=>0, 'info'=>'保存失败'));
                }
            } else {
                $this->ajaxReturn(array('status'=>0,'info'=>$model->getError()));
            }
        }
    }

    
    /**
     * 删除
     */
    public function del ()
    {
        $id = I('get.id', 0, 'intval');
        if (D('Common/CasePerson')->where(array("id"=>$id))->delete()) {
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        } 
    } 
         
}
