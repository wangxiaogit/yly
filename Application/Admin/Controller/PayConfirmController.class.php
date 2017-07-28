<?php
/**
 * 交易人列表控制器
 */
namespace Admin\Controller;
use Common\Controller\AdminController;

class PayConfirmController extends AdminController
{
    
    /**
     * 收件资料列表
     */
    public function index()
    {   
        /***shearch***/
        $group = trim(I('post.group'));
        $keywords = trim(I('post.keywords'));
        $action = I('action',1,'intval');
        $map = array();
        if ($group && $keywords) {
            switch($group)
            {
                case 'accept': {
                   $map['b.accept_name'] = array('LIKE','%'.$keywords.'%' );
                } break;
                case 'debtor': {
                   $map['b.debtor'] = array('LIKE','%'.$keywords.'%' );
                } break;
                case 'receive_no': {
                   $map['a.receipt_no'] = array('LIKE','%'.$keywords.'%' );
                } break;
            }
        }
        if ($action ==1) {
            $map['a.status'] = 0;
        } else {
            $map['a.status'] = array('neq',0);
        }
        /***page***/
        $page_show_num = I('request.r');//自定义每页显示行数
        $limit = !empty($page_show_num) ? $page_show_num : C('LIST_ROWS');
        $total_num = M('case_pay')->alias('a')->join('case_list b ON b.id = a.case_id')->where($map)->count();
        $Page = new \Think\Page($total_num, $limit);
        if($total_num>$limit){
            $Page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        }
        
        $field = 'a.case_id,a.id,a.fee,a.receipt_no,a.pay_date,a.status,b.case_no,b.accept_name,b.debtor';
        $lists = M('case_pay')->alias('a')->join('case_list b ON b.id = a.case_id')->field($field)->where($map)
                ->limit($Page->firstRow, $Page->listRows)->select();
        $show = $Page->show();
        
        $this->assign('action',$action);
        $this->assign('lists',$lists);
        $this->assign('_Total',$total_num);
        $this->assign('_Page', $show ? $show : '');
        $this->display();
    }
    

    /*
     * 增加
     */
    public function confirm()
    {   
        $ids = I('ids');
        $ids_arr = array_filter(explode(',', $ids));
        $status = I('status','','intval');
        
        if (!empty($ids_arr) && $status) {
            $where['id'] = array('IN', $ids_arr);
            $data['status'] = $status;
            $data['confirm_time'] = time();
            $data['confirm_uid'] = session('userInfo.id');
            $res = M('CasePay')->where($where)->save($data);
            if($res) {
                $this->success('操作成功！');
            } else {
                $this->success('操作失败！');
            }
        } else {
            $this->error('缺少参数，操作失败！');
        }
    }
    
}
