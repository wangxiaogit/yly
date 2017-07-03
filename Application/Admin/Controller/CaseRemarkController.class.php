<?php
/**
 * 交易人列表控制器
 */
namespace Admin\Controller;
use Common\Controller\AdminController;

class CaseRemarkController extends AdminController
{
    
    /**
     * 收件资料列表
     */
    public function index($case_id)
    {   
        $model = D('Common/CaseRemark');
        if ($case_id) {
        $remark_list = $model->where('case_id = '.$case_id)->select();
        }
        $this->assign('tab_arr', get_case_info_tabs());
        $this->assign('tab', 'CaseRemark/index');
        $this->assign('case_id', $case_id);
        $this->assign('remark_list', $remark_list);
        $this->display();
    }
    
    
    /*
     * 新增提交
     */
    public function do_add()
    {   
        $model = D('Common/CaseRemark');
        if (IS_GET) {
            if ($model->create(I('get.'))) {
                if ($model->add()!==false) {
                    $this->success('添加成功');
                } else {
                    $this->error('添加失败');
                }
            } else {
                $this->error($model->getError());
            }
        }
    }
         
}
