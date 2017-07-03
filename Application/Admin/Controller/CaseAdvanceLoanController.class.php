<?php
/**
 * 交易人列表控制器
 */
namespace Admin\Controller;
use Common\Controller\AdminController;

class CaseAdvanceLoanController extends AdminController
{
    
    /**
     * 交易人列表
     */
    public function index($case_id)
    {   
        $model = D('Common/CaseAdvanceLoan');
        if ($case_id) {
        $info = D('Common/CaseList')->find($case_id);
        $advance_info = $model->where('case_id = '.$case_id)->find();
        $housecode = D('Common/CaseHouse')->where('case_id = '.$case_id)->getField('housecode');
        }
        
        $this->assign('tab_arr', get_case_info_tabs());
        $this->assign('cooperative_type', $model->getCooperativeType());
        $this->assign('tab','CaseAdvanceLoan/index');
        $this->assign('case_id', $case_id);
        $this->assign('info',$info);
        $this->assign('housecode',$housecode);
        $this->assign('advance_info',$advance_info);
        $this->display();
    }

    
    /**
     * 提交编辑
     */
    public function do_edit()
    {
        $model = D('Common/CaseAdvanceLoan');
        if (IS_POST) {
            if ($model->create()) {
                $num = $model->where('case_id = '.I('case_id'))->count();
                if ($num) {
                    $res = $model->where('case_id = '.I('case_id'))->save();
                } else {
                    $res = $model->add();
                }
                if ($res !==false) {
                    $this->success('保存成功');
                } else {
                    $this->error('保存失败');
                }
            } else {
                $this->error($model->getError());
            }
        }
    }

    
         
}
