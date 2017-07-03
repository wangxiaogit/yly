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
    
    public function _initialize()
    {
        parent::_initialize();
        $this->caseListModel = D('Common/CaseList');
        $this->caseHouseModel = D('Common/CaseHouse');
    }
    
    /**
     * 案件列表
     */
    public function index()
    {   
        //model
        $dept_model = D('Common/Dept');
        $uid = session('uid')?session('uid'):1;
        
        //搜索
        $action = I('post.action_type');
        //查询
        $cond_where = array();
        if($action) {
            //dump(I());die;
            $group = I('post.group');
            $keywords = I('post.keywords');
            if ($keywords && $group) {
                $cond_where['caselist'.$group] = array('like', '%'.trim($keywords).'%');
            }
            $case_type = I('post.case_type');
            if ( $case_type > 0 ) {
                $cond_where['type.case_type_id'] = $case_type;
            }
            $search_node_time_type = I('post.search_node_time_type', '');
            $node_step = I('post.node_step', 0);
            $fromdate = I('post.fromdate', '');
            $enddate = I('post.enddate', '');
            $case_status = I('post.case_status');
        }

        //实例化分页类 传入总记录数和每页显示的记录数
        $page_show_num = I('request.r');//自定义每页显示行数
        //每页显示条数
        $limit = !empty($page_show_num) ? $page_show_num : C('LIST_ROWS');
        
        $tabType = I('tab_type','needToDo');
        $dept_id = session('user_info.dept_id')?session('user_info.dept_id'):1;
        $manage_privilege = session('user_info.manage_privilege')?session('user_info.manage_privilege'):0;
        switch ($tabType)
        {   
            //待办
            case 'needToDo':{
                $cond_where['type.status'] = array('IN', '1,2');
                //搜索部门，并且是部门管理员
                if ($manage_privilege == 1 && $dept_id > 0) {
                    $childDeptIds = $dept_model->getChildDeptIdsByDeptid($dept_id);
                    $childDeptIds = array_merge($childDeptIds, array(0 => $dept_id));
                    $childDeptIds_str = !empty($childDeptIds) ? implode(',', $childDeptIds) : '-1';
                    $cond_where['_string'] = " (case_list.accept_deptid IN (".$childDeptIds_str.") AND "
                            . " ( type.wfid is NULL OR type.wfid = 0) ) OR type.dept_id IN (".$childDeptIds_str.")";
                } else {
                    $cond_where['_string'] = " (case_list.accept_uid = '".$uid."' AND (type.wfid is NULL OR type.wfid = 0)) OR type.handle_uid = '".$uid."' ";
                    $cond_wf_child = " WHERE take_uid = '".$uid."' ";
                }
                        $cond_where_child = "";
                //时间、节点搜索
                if((!empty($fromdate) || !empty($enddate)) && $node_step > 0 && $search_node_time_type != "") {
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

                    $cond_where_child .= $cond_wf_child . " AND type = '0' AND isback = '0' AND isvalid = 1 ";
                    if ($search_node_time_type == 'submit_time') {
                        $cond_where_child .= ' AND  submit_time between '. $start_time.' AND '.$end_time;
                        $cond_where_child .= ' AND status IN (3,4)';
                    } else if ($search_node_time_type == 'start_time') {
                        $cond_where_child .= '  AND accept_time between '. $start_time.' AND '.$end_time;
                    }

                    $cond_where_child .= ' AND node_id = '. $node_step;
                } else if ( (empty($fromdate) && empty($enddate)) && $node_step > 0) {
                    $cond_where['type.wfnode'] = $node_step;
                }
                        //查询符合条件的总数
                if($cond_where_child == "") {
                    $total_num = $this->caseListModel->join('case_type AS type ON case_list.id = type.case_id')
                        ->where($cond_where)
                        ->count();
                } else {
                    $total_num = $this->caseListModel->join('case_type AS type ON case_list.id = type.case_id')
                        ->join("(SELECT distinct(flow_id) FROM workflow_step ".$cond_where_child." ) AS step ON type.wfid = step.flow_id")
                        ->where($cond_where)
                        ->count();
                }

                $Page = new \Think\Page($total_num, $limit);

                //查询列表数据
                $search_field_arr = array(
                                'case_list.*',
                                'type.id AS type_id',
                                'type.case_id',
                                'type.case_type_id',
                                'type.wfid',
                                'type.wftype',
                                'type.wfnode',
                                'type.step_id',
                                'type.dept_id',
                                'type.handle_user_type',
                                'type.handle_roleid',
                                'type.handle_uid',
                                'type.handle_time',
                                'type.creat_time',
                                'type.status',
                            );
                $cond_order = array('type.handle_time' => 'desc');
                if($cond_where_child == "") {
                    $case_list_info = $this->caseListModel->field($search_field_arr)->join('case_type AS type ON case_list.id = type.case_id')
                    ->where($cond_where)
                    ->order($cond_order)
                    ->limit($Page->firstRow, $Page->listRows)
                    ->select();
                } else {
                    $case_list_info = $this->caseListModel->field($search_field_arr)->join('case_type AS type ON case_list.id = type.case_id')
                    ->join("(SELECT distinct(flow_id) FROM org_workflow_step ".$cond_where_child." ) AS step ON type.wfid = step.flow_id")
                    ->where($cond_where)
                    ->order($cond_order)
                    ->limit($Page->firstRow, $Page->listRows)
                    ->select();
                }
//                dump($this->caseListModel->_sql());
                $metaTitle = '待办案件';
            } break;
        
            //已办
            case 'done':{
                $cond_where['type.status'] = array('IN', '1,2');
                //时间、节点搜索
                if((!empty($fromdate) || !empty($enddate)) && $node_step > 0 && $search_node_time_type != "") {
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

                    if ($search_node_time_type == 'submit_time') {
                        $cond_where_node_time = '  AND submit_time between '. $start_time.' AND '.$end_time;
                        $cond_where_node_time .= ' AND status IN (3,4)';
                    } else if ($search_node_time_type == 'start_time') {
                        $cond_where_node_time = '  AND accept_time between '. $start_time.' AND '.$end_time;
                    }
                    $cond_where_node_time .= ' AND node_id = '. $node_step;
                    $cond_where_node_time .= ' AND isback = 0';
                }else if ( (empty($fromdate) && empty($enddate)) && $node_step > 0) {
                    $cond_where['type.wfnode'] = $node_step;
                }
            //没有搜索条件，并且是部门管理权限
            if ($manage_privilege == 1 && $dept_id > 0) {
                $uid_str = '';
                $childDeptIds = $dept_model->getChildDeptIdsByDeptid($dept_id);
                $childDeptIds = array_merge($childDeptIds, array(0 => $dept_id));
                $cond_user_where['dept_id'] = array('in', $childDeptIds);
                $user_ids = M('user')->field('id')->where($cond_user_where)->select();
                if (is_array($user_ids) && !empty($user_ids)) {
                    $user_ids_arr = array();
                    foreach ($user_ids as $key => $value) {
                        $user_ids_arr[] = $value['id'];
                    }
                    $uid_str = implode(',', $user_ids_arr);
                }

                $cond_where_child = "WHERE take_uid IN (".$uid_str.") AND handle_type = 0 AND status IN (3,4) AND isvalid = 1";
            } else {
                $cond_where_child = "WHERE take_uid = '".$uid."' AND handle_type = 0 AND status IN (3,4) AND isvalid = 1";
            }

            //查询符合条件的总数
            $total_num = $this->caseListModel->join('case_type AS type ON case_list.id = type.case_id')
                            ->join("(SELECT DISTINCT(flow_id) FROM workflow_step WHERE "
                                    . "flow_id IN (SELECT flow_id FROM workflow_step ".$cond_where_child." ) ". $cond_where_node_time." ) AS step ON "
                                    . "type.wfid = step.flow_id")
                            ->where($cond_where)
                            ->count();
            $Page = new \Think\Page($total_num, $limit);
            $show = $Page->show();//分页显示输出

            //查询列表数据
            $search_field_arr = array(
                            'case_list.*',
                            'type.id AS type_id',
                            'type.case_id',
                            'type.case_type_id',
                            'type.wfid',
                            'type.wftype',
                            'type.wfnode',
                            'type.step_id',
                            'type.dept_id',
                            'type.handle_user_type',
                            'type.handle_roleid',
                            'type.handle_uid',
                            'type.handle_time',
                            'type.creattime',
                            'type.status',
                        );
            $cond_order = array( 'type.handle_time' => 'desc' , 'type.creattime' => 'desc');
            //查询列表数据
            $case_list_info = $this->caseListModel->field($search_field_arr)->join('case_type AS type ON case_list.id = type.case_id')
                            ->join("(SELECT DISTINCT(flow_id) FROM workflow_step WHERE "
                                    . "flow_id IN (SELECT flow_id FROM workflow_step ".$cond_where_child." ) ". $cond_where_node_time." ) AS step ON "
                                    . "type.wfid = step.flow_id")
                            ->where($cond_where)
                            ->order($cond_order)
                            ->limit($Page->firstRow, $Page->listRows)
                            ->select();
                $metaTitle = '已办案件';
            } break;
            
            //办结
            case 'end':{
                $cond_where['type.status'] = empty($case_status) ? array('IN', '3,4') : $case_status;
                    //时间、节点搜索
                if((!empty($fromdate) || !empty($enddate)) && $node_step > 0 && $search_node_time_type != "") {
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

                    if ($search_node_time_type == 'submit_time') {
                        $cond_where_node_time = '  AND submit_time BETWEEN '. $start_time.' AND '.$end_time;
                        $cond_where_node_time .= ' AND status IN (3,4)';
                    } else if ($search_node_time_type == 'start_time') {
                        $cond_where_node_time = '  AND accept_time BETWEEN '. $start_time.' AND '.$end_time;
                    }

                    $cond_where_node_time .= ' AND node_id = '. $node_step;
                    $cond_where_node_time .= ' AND isback = 0';
                } else if ( (empty($fromdate) && empty($enddate)) && $node_step > 0) {
                    $cond_where['type.wfnode'] = $node_step;
                    $cond_where['string'] = " AND case_list.accept_uid = '".$uid."'";
                }

            //没有搜索条件，并且是部门管理权限
            if ($manage_privilege == 1 && $dept_id > 0) {
                $uid_str = '';
                $childDeptIds = $dept_model->getChildDeptIdsByDeptid($dept_id);
                $childDeptIds = array_merge($childDeptIds, array(0 => $dept_id));
                $cond_user_where['dept_id'] = array('in', $childDeptIds);
                $user_ids = M('user')->field('id')->where($cond_user_where)->select();
                if (is_array($user_ids) && !empty($user_ids)) {
                    $user_ids_arr = array();
                    foreach ($user_ids as $key => $value) {
                        $user_ids_arr[] = $value['id'];
                    }
                    $uid_str = implode(',', $user_ids_arr);
                }

                $cond_where_child = "WHERE take_uid IN (".$uid_str.") AND handle_type = 0  AND isvalid = 1";
            } else {
                $cond_where_child = "WHERE take_uid = '".$uid."' AND handle_type = 0  AND isvalid = 1";
            }
            //查询符合条件的总数
            $total_num = $this->caseListModel->join('case_type AS type ON case_list.id = type.case_id')
                            ->join("(SELECT DISTINCT(flow_id) FROM workflow_step WHERE "
                                    . "flow_id IN (SELECT flow_id FROM workflow_step ".$cond_where_child." ) ". $cond_where_node_time." ) AS step ON "
                                    . "type.wfid = step.flow_id")
                            ->where($cond_where)
                            ->count();
            $Page = new \Think\Page($total_num, $limit);
            //查询列表数据
            $search_field_arr = array(
                            'case_list.*',
                            'type.id AS type_id',
                            'type.case_id',
                            'type.case_type_id',
                            'type.wfid',
                            'type.wftype',
                            'type.wfnode',
                            'type.step_id',
                            'type.dept_id',
                            'type.handle_user_type',
                            'type.handle_roleid',
                            'type.handle_uid',
                            'type.handle_time',
                            'type.creattime',
                            'type.status',
                        );
            $cond_order = array( 'type.handle_time' => 'desc' , 'type.creattime' => 'desc');
            //查询列表数据
            $case_list_info = $this->caseListModel->field($search_field_arr)->join('case_type AS type ON case_list.id = type.case_id')
                            ->join("(SELECT DISTINCT(flow_id) FROM workflow_step WHERE "
                                    . "flow_id IN (SELECT flow_id FROM workflow_step ".$cond_where_child." ) ". $cond_where_node_time." ) AS step ON "
                                    . "type.wfid = step.flow_id")
                            ->where($cond_where)
                            ->order($cond_order)
                            ->limit($Page->firstRow, $Page->listRows)
                            ->select();
                $metaTitle = '办结案件';
            } break;
            //全公司
            case 'all':{
            //查询符合条件的总数
            $total_num = $this->caseListModel->join('case_type AS type ON case_list.id = type.case_id')
                            ->where($cond_where)
                            ->count();
            $Page = new \Think\Page($total_num, $limit);
            //查询列表数据
            $search_field_arr = array(
                            'case_list.*',
                            'type.id AS type_id',
                            'type.case_id',
                            'type.case_type_id',
                            'type.wfid',
                            'type.wftype',
                            'type.wfnode',
                            'type.step_id',
                            'type.dept_id',
                            'type.handle_user_type',
                            'type.handle_roleid',
                            'type.handle_uid',
                            'type.handle_time',
                            'type.creattime',
                            'type.status',
                        );
            $cond_order = array( 'type.handle_time' => 'desc' , 'type.creattime' => 'desc');
            //查询列表数据
            $case_list_info = $this->caseListModel->field($search_field_arr)->join('case_type AS type ON case_list.id = type.case_id')
                            ->where($cond_where)
                            ->order($cond_order)
                            ->limit($Page->firstRow, $Page->listRows)
                            ->select();
                $metaTitle = '所有案件';
            } break;
        }
        //业务类型
        $confBusinessType = $this->caseListModel->getCaseType();
        //贷款类型
        $confLoanType = $this->caseHouseModel->getLoanType();
        //案例状态
        $confCaseStatus = $this->caseListModel->getConfCaseStatus();
        //部门
        $deptList = M('dept')->where('org_id = 1')->getField('id, name');
//        /***流程类型***/
//        $flowType = new \Admin\Model\FlowTypeModel();
//        $conf_where['category'] = 1;
//        $confFlowTypeTemp = $flowType->getFlowTypeByConf($conf_where);
//        
//        /***流程类型数组***/
//        $confFlowType = array();
//        if(is_array($confFlowTypeTemp) && !empty($confFlowTypeTemp))
//        {
//            foreach($confFlowTypeTemp as $key => $value)
//            {
//                $confFlowType[$value['id']] = $value['type_name'];
//            }
//        }
//        
//        /***流程节点数组***/
//        $flowConfType = new \Admin\Model\FlowConfModel();
//        $confFlowNodeTemp = $flowConfType->getFlowNodeByConf();
//        $confFlowNode = array();
//        if(is_array($confFlowNodeTemp) && !empty($confFlowNodeTemp))
//        {
//            foreach($confFlowNodeTemp as $key => $value)
//            {
//                $confFlowNode[$value['id']] = $value['node_name'];
//            }
//        }
        
        
        
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
        $this->assign('tab_type', $tabType);
        $this->assign('meta_title', $metaTitle);
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
                        $data['case_type_id'] = $val;
                        $data['creat_time'] = time();
                        $data['status'] = 1;
                        $model->add($data);
                    }
                    $this->ajaxReturn(array('status'=>1, 'info'=>'添加成功'));
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
