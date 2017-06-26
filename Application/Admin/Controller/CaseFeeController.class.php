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
        $case_pay_list = M('CasePay')->where('case_id = '.$case_id)->select();
        $case_payment_list = M('CasePaymentList')->where('case_id = '.$case_id)->select();
        
        $this->assign('case_pay_list',$case_pay_list);
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
        $this->assign('fee_arr', $res);
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
    public function do_edit()
    {
        $model = D('Common/CaseFee');
        $ids = I('get.ids');
        $id_arr = array_filter(explode(',', $ids));
        if (empty($id_arr)) {
            $this->error('未获取到收件资料'); 
        } else {
            $where['id'] = array('IN', $id_arr);
            $data['getit'] = 1;
            $data['accept_uid'] = session(userInfo.uid);
            $data['accept_name'] = session(userInfo.name);
            $data['accept_time'] = time();
            $res = $model->where($where)->setField($data);
            if ($res) {
                $this->success('收件成功'); 
            } else {
                $this->error('收件失败');
            }
        }
    }

    
    /**
     * 删除
     */
    public function del ()
    {
        $id = I('get.id', 0, 'intval');
        if (D('Common/CaseFee')->where(array("id"=>$id))->delete()) {
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        } 
    } 
         
}
