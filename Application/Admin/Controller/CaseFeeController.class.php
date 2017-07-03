<?php
/**
 * 费用列表控制器
 */
namespace Admin\Controller;
use Common\Controller\AdminController;

class CaseFeeController extends AdminController
{
    
    /**
     * f费用明细列表
     */
    public function index($case_id)
    {   

        $model = D('Common/CaseFee');
        $this->assign('tab_arr', get_case_info_tabs());
        $this->assign('tab','CaseFee/index');
        $this->assign('case_id', $case_id);
        $case_fee_list = M('CaseFee')->where('status = 0 and case_id = '.$case_id)->select();
        $case_pay_list = M('CasePay')->where('status >= 0 and case_id = '.$case_id)->select();
        $case_payment_list = M('CasePayment')->where('case_id = '.$case_id)->select();
        
        $this->assign('case_pay_list',$case_pay_list);
        $this->assign('pay_mode',D('Common/FeeStandard')->getPayMode());
        $this->assign('payment_type',D('Common/FeeStandard')->getPaymentType());
        $this->assign('case_payment_list',$case_payment_list);
        $this->assign('case_fee_list',$case_fee_list);
        $this->display();
    }
    

    /*
     * 增加收费项
     */
    public function add_fee($case_id)
    {   

        $fee_list = M('FeeStandard')->where('isvalid = 1')->select();
        foreach($fee_list as $value) {
            $res[$value['fee_type']][] = $value;
        }
        $this->assign('case_id', $case_id);
        $this->assign('type_arr', D('Common/FeeStandard')->getFeeType());
        $this->display();
    }
    
    
    /*
     * 增加缴费
     */
    public function add_pay($case_id)
    {
        $this->assign('case_id', $case_id);
        $this->assign('pay_mode', D('Common/FeeStandard')->getPayMode());
        $this->display();
    }    
    
    
    /*
     * 增加支出
     */
    public function add_payment($case_id)
    {
        $this->assign('case_id', $case_id);
        $this->assign('payment_type', D('Common/FeeStandard')->getPaymentType());
        $this->display();
    }   
    
    
    /*
     * 增加收费项
     */
    public function do_edit_fee($id)
    {   
        $id = I('get.id', 0, 'intval');
        $actual_fee = I('get.actual_fee', 0, 'doubleval');
        if (D('Common/CaseFee')->where('id = '.$id)->setField('actual_fee', $actual_fee)) {
            $this->success('修改成功！');
        } else {
            $this->error('修改失败！');
        };
    }
    
    
    /*
     * 增加缴费
     */
    public function edit_pay($id)
    {
        if ($id) {
            $pay_info = D('Common/CasePay')->find($id);
        }
        $this->assign('id', $id);
        $this->assign('pay_info', $pay_info);
        $this->assign('pay_mode', D('Common/FeeStandard')->getPayMode());
        $this->display();
    }    
    
    
    /*
     * 增加支出
     */
    public function edit_payment($id)
    {
        if ($id) {
            $payment_info = D('Common/CasePayment')->find($id);
        }
        $this->assign('id', $id);
        $this->assign('payment_info', $payment_info);
        $this->assign('payment_type', D('Common/FeeStandard')->getPaymentType());
        $this->display();
    } 
    
    
    /*
     * 新增提交
     */
    public function do_add()
    {   
        $model = D('Common/CaseFee');
        if (IS_POST) {
            $fee_list = I('post.fee_list');
            $case_id = I('post.case_id');
            if (empty($fee_list)) {
                $this->error('您未选择收费项目，请重新选择');
            }
            if (!$case_id) {
                $this->error('参数异常！');
            }
            
            $fee_arr = M('FeeStandard')->where('isvalid=1')->getField('id, fee_name, fee');
            foreach ($fee_list as $va) {
                $result['fee_id'] = $va;
                $result['fee_name'] = $fee_arr[$va]['fee_name'];
                $result['fee'] = $fee_arr[$va]['fee'];
                $result['actual_fee'] = $fee_arr[$va]['fee'];
                $result['case_id'] = $case_id;
                $result['status'] = 0;
                $data[] = $result;
            }
            if ($model->addAll($data)) {
                $this->success('添加成功！');
            } else {
                $this->error('添加失败！');
            }
        }
    }
    

    /**
     * 提交编辑
     */
    public function do_edit_pay()
    {
        if (IS_POST) {
             $model = D('Common/CasePay');
            if ($model->create()) {
                if ($model->save() !== false) {
                    $this->success('修改成功！');
                } else {
                    $this->error('修改失败！');
                }
            } else {
                $this->error($model->getError());
            }
        }        
    }
    
    
    /**
     * 提交编辑
     */
    public function do_edit_payment()
    {
        if (IS_POST) {
             $model = D('Common/CasePayment');
            if ($model->create()) {
                if ($model->save() !== false) {
                    $this->success('修改成功！');
                } else {
                    $this->error('修改失败！');
                }
            } else {
                $this->error($model->getError());
            }
        } 
    }
    
    /*提交缴费
     * 
     */
    public function do_add_pay()
    {   
        $model = D('Common/CasePay');
        
        if (IS_POST) {
            if ($model->create()) {
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
    
    
    /*提交支出
     * 
     */
    public function do_add_payment()
    {   
        $model = D('Common/CasePayment');
        
        if (IS_POST) {
            if ($model->create()) {
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
    
    
    /**
     * 删除
     */
    public function del ()
    {
        $id = I('get.id', 0, 'intval');
        $action = I('get.action');
        if (!$action) {
            $this->error("未获取到参数删除失败！");
        }
        $model = '';
        switch($action)
        {
            case 'fee':$model = D('Common/CaseFee'); break;
            case 'pay':$model = D('Common/CasePay'); break;
            case 'payment':$model = D('Common/CasePayment'); break;
        }
        if ($model->where(array("id"=>$id))->delete()) {
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        } 
    } 
         
}
