<?php
/**
 * 交易人列表控制器
 */
namespace Admin\Controller;
use Common\Controller\AdminController;

class CaseHouseController extends AdminController
{
    
    /**
     * 交易人列表
     */
    public function index($case_id)
    {   
        $model = D('Common/CaseHouse');
        if ($case_id) {
        $info = $model->where('case_id = '.$case_id)->find();
        }
        $this->assign('tab_arr', get_case_info_tabs());
        $this->assign('tab','CaseHouse/index');
        $this->assign('case_id', $case_id);
        $this->assign('loan_flow_type',$model->getConfLoanFlowType());
        $this->assign('district_arr', $model->getConfDistrict());
        $this->assign('house_set', $model->getHouseSet());
        $this->assign('house_type', $model->getHouseType());
        $this->assign('land_type', $model->getLandType());
        $this->assign('loan_config', $model->getConfLoan());
        $this->assign('loan_type', $model->getLoanType());
        $this->assign('house_use', $model->getHouseUse());
        $this->assign('loan_flow',$case_data['loan_flow_type']);
        $this->assign('goodschool', $model->getConfSchoolHouse());
        $this->assign('info',$info);
        $this->display();
    }
    
        
         
    
    /*
     * 增加
     */
    public function add($case_id)
    {   
        $this->assign('case_id', $case_id);
        $this->assign('company_type', D('Common/CaseHouse')->getCompanyType());
        $this->display();
    }
    
    

    /**
     * 提交编辑
     */
    public function do_edit()
    {
        $model = D('Common/CaseHouse');
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
