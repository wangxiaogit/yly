<?php
/**
 * 案件列表控制器
 */
namespace Admin\Controller;
use Common\Controller\AdminController;

class CaseListController extends AdminController
{   
    protected  $caseListModel;
    protected  $caseHouseModel;
    protected  $deptUserIds;
    
    public function _initialize()
    {
        parent::_initialize();
        $this->caseListModel = D('Common/CaseList');
        $this->caseHouseModel = D('Common/CaseHouse');
        $this->deptUserIds = D('Common/CaseList')->getUserListByDeptId();
    }
    
    public function _search()
    {
        $map = array();
        //搜索
        $action = I('post.action_type');
        if ($action){
            $group = I('post.group');
            $keywords = I('post.keywords');
            if ($keywords && $group) {
                $map['case_list'.$group] = array('like', '%'.trim($keywords).'%');
            }
            $case_type = I('post.case_type');
            if ( $case_type > 0 ) {
                $map['type.case_type_id'] = $case_type;
            }
            $node_step = I('post.node_step', 0);
            $fromdate = I('post.fromdate', '');
            $enddate = I('post.enddate', '');
            //时间、节点搜索
            if($node_step > 0 ) {
                $map['type.wfnode'] = $node_step;
                if ( !empty($fromdate) && !empty($enddate) ) {
                    $start_time = !empty($fromdate) ? strtotime($fromdate) : 0;
                    $end_time = !empty($enddate) ? strtotime($enddate) + 86399 : 0;
                } else if (!empty($fromdate)) {
                    $start_time = !empty($fromdate) ? strtotime($fromdate) : 0;
                    $end_time = $start_time + 86399;
                } else {
                    $end_time = !empty($enddate) ? strtotime($enddate) + 86399 : 0;
                    $start_time = strtotime($enddate);
                }
                $map['type.handle_time'] = array('between',array($start_time, $end_time));
            }
        }
        return $map;
    }        

    
    /**
     * 案件列表
     */
    public function index()
    {   
        $map = $this->_search();
        $tabType = I('get.tab_type','needToDo');
        $userList = $this->deptUserIds;
        $userIds = implode(',', $userList);
        switch ($tabType)
        {
            case 'needToDo':{
                $map['type.status'] = array('IN','1,2');
                $map['_string'] = "INTE_ARRAY(type.handle_str, '".$userIds."') = 1";
            } break;
            case 'done':{
                $map['type.status'] = array('IN','1,2');
                $map['_string'] = "(INTE_ARRAY(type.handle_already_str, '".$userIds."') = 1 and INTE_ARRAY(type.handle_str, '".$userIds."') = 0 )";
                } break;
            case 'end':{
                $map['type.status'] = array('IN','3,4');
                $map['_string'] = "(INTE_ARRAY(type.handle_already_str, '".$userIds."') = 1 or INTE_ARRAY(type.handle_str, '".$userIds."') = 1 )";
                } break;
        }
        //每页显示条数
        $limit =  C('LIST_ROWS')?C('LIST_ROWS'):10;
        $total_num = $this->caseListModel->join('case_type AS type ON case_list.id = type.case_id')->where($map)->count();
        $Page = new \Think\Page($total_num, $limit);
        //查询列表数据
        $search_field_arr = array('case_list.*','type.id AS type_id','type.case_id','type.case_type_id','type.wfid','type.wftype', 'type.wfnode','type.step_id','type.dept_id','type.handle_str',
            'type.handle_group_id', 'type.handle_uid', 'type.handle_time','type.creat_time','type.status',);
        $cond_order = array('case_list.id'=>'desc','type.handle_time' => 'desc');
        $case_list_info = $this->caseListModel->field($search_field_arr)->join('case_type AS type ON case_list.id = type.case_id')
        ->where($map)->order($cond_order)->limit($Page->firstRow, $Page->listRows)->select();

        //业务类型
        $confBusinessType = $this->caseListModel->getCaseType();
        //贷款类型
        $confLoanType = $this->caseHouseModel->getLoanType();
        //案例状态
        $confCaseStatus = $this->caseListModel->getConfCaseStatus();
        //部门
        $deptList = M('dept')->where('org_id = 1')->getField('id, name');
        /***流程类型数组***/
        $confFlowType = M('workflow_type')->getField('id,name');
        /***流程节点数组***/
        $confNodeList = M('workflow_node')->getField('id,name');
        if($total_num>$limit){
            $Page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        }
        $show = $Page->show();
        $this->assign('_Page', $show ? $show : '');
        $this->assign('caseList', $case_list_info);
        $this->assign('caseType', $this->caseListModel->getCaseType());
        $this->assign('_Total',$total_num);
        $this->assign('confLoanType',$confLoanType);
        $this->assign('deptList',$deptList);
        $this->assign('confCaseStatus',$confCaseStatus);
        $this->assign('confFlowType',$confFlowType);
        $this->assign('confNodeList',$confNodeList);
        $this->assign('tab_type', $tabType);
        $this->display();
    }
  
    
    /*
     * 增加
     */
    public function add()
    {   
        $dept_list = get_dept_list(3);
        $case_tag = D('CaseTag')->where('isvalid=1')->getField('id, tag_name');
        $this->assign('dept_list', json_encode($dept_list));
        $this->assign('case_tag',$case_tag);
        $this->display();
    }
    
    
    /*
     * 新增提交
     */
    public function do_add()
    {   
        $model = M('CaseType');
        if (IS_POST) {
            if ($this->caseListModel->create()) {
                $res = $this->caseListModel->add();
                if ($res !==false) {
                    $case_type = I('case_type');
                    $case_type[] = '1';
                    foreach ($case_type as $val) {
                        $data['case_id'] = $res;
                        $data['handle_str'] = session('userInfo.id');
                        $data['handle_uid'] = $res;
                        $data['case_type_id'] = $val;
                        $data['creat_time'] = time();
                        $data['status'] = 1;
                        $model->add($data);
                    }
                    $this->ajaxReturn(array('status'=>1, 'info'=>'添加成功','url'=>U('CaseList/index',array('tab_type'=>'needToDo'))));
                } else {
                    $this->ajaxReturn(array('status'=>0, 'info'=>'添加失败'));
                }
            } else {
                $this->ajaxReturn(array('status'=>0,'info'=>$this->caseListModel->getError()));
            }
        }
    }
    
    /*
     * 修改
     */
    public function edit($id)
    {   
        if($id) {
            $case_info = D('Common/CaseList')->find($id);
            $case_info['broker_id'] = 'org_'.$case_info['broker_org_id'].'|'.'dept_'.$case_info['broker_dept_id'];
            $case_type_arr = M('case_type')->where('status <>3 and case_id = '.$id)->getField('case_type_id', TRUE);
        }
        $dept_list = get_dept_list(3);
        $case_tag = D('CaseTag')->where('isvalid=1')->getField('id, tag_name');
        $this->assign('dept_list', json_encode($dept_list));
        $this->assign('case_tag',$case_tag);
        $this->assign('case_type_arr', $case_type_arr);
        $this->assign('case_info', $case_info);
        $this->display();
    }

    /**
     * 提交编辑
     */
    public function do_edit()
    {
        $model = M('CaseType');
        if (IS_POST) {
            if ($this->caseListModel->create()) {
                $res = $this->caseListModel->save();
                    $case_type = I('case_type');
                    if(!empty($case_type)){
                        foreach ($case_type as $val) {
                            $data['case_id'] = I('id');
                            $data['case_type_id'] = $val;
                            $data['creat_time'] = time();
                            $data['status'] = 1;
                            $model->add($data);
                        }
                    }
                    $this->ajaxReturn(array('status'=>1, 'info'=>'保存成功'));
            } else {
                $this->ajaxReturn(array('status'=>0,'info'=>$this->caseListModel->getError()));
            }
        }
    }
    
    /*
     * 银行信息
     */
    public function bankInfo($case_id)
    {   
        $bank_info = D('Common/CaseList')->find($case_id);
        if ($bank_info['bank_uid']) {
            $bank_info['bank_uname'] = M('user')->where('id='.$bank_info['bank_uid'])->getField('true_name');
        }
        $bank_list = M('Organize')->where('type = 2  and status = 1 ')->select();
        
        $this->assign('tab_arr', get_case_info_tabs());
        $this->assign('tab','CaseList/bankInfo');
        $this->assign('case_id',$case_id);
        $this->assign('bank_info',$bank_info);
        $this->assign('organize_lists', $bank_list);
        $this->display();
    }        
     
    
    /*
     * 设置银行
     */
    public function bankSet()
    {
        $model = D('Common/CaseList');
        if (IS_POST) {
            if ($model->create()) {
                $res = $model->save();
                    if ($res) {
                        $this->success('保存成功！');
                    } else {
                        $this->error('保存失败！');
                    }
            } else {
                $this->error($model->getError());
            }
        }
    }
}
