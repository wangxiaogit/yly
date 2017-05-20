<?php
/**
 * 案件列表控制器
 */
namespace Admin\Controller;
use Common\Controller\AdminController;

class CaseListController extends AdminController
{   
    protected  $caseListModel;
    
    public function _initialize()
    {
        parent::_initialize();
        $this->caseListModel = D('Common/CaseList');
    }
    
    /**
     * 案件列表
     */
    public function index()
    {   
        //model
        $dept_model = D('Common/Dept');
        $uid = session('uid');
        
        //搜索
        $action = I('post.action_type');
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
        }

        //实例化分页类 传入总记录数和每页显示的记录数
        $page_show_num = I('request.r');//自定义每页显示行数
        //每页显示条数
        $limit = !empty($page_show_num) ? $page_show_num : C('LIST_ROWS');
        
        $tabType = I('tab_type','needToDo');
        switch ($tabType)
        {   
            //待办
            case 'needToDo':{
                //搜索部门，并且是部门管理员
                if (session('manage_privilege') == 1 && session('dept_id') > 0) {
                    $childDeptIds = $dept_model->getChildDeptIdsByDeptid(session('dept_id'));
                    $childDeptIds = array_merge($childDeptIds, array(0 => session('dept_id')));
                    $childDeptIds_str = !empty($childDeptIds) ? implode(',', $childDeptIds) : '-1';
                    $cond_where['_string'] = " (case_list.accept_deptid IN (".$childDeptIds_str.") AND "
                            . " ( type.wfid is NULL OR type.wfid = 0) ) OR type.dept_id IN (".$childDeptIds_str.")";
                } else {
                    $cond_where['_string'] = " ((case_list.accept_uid = '".$uid."' AND (type.wfid is NULL OR type.wfid = 0)) OR type.handle_uid = '".$uid."' ";
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
                    $total_num = $case_list_model->join('case_type AS type ON case_list.id = type.case_id')
                        ->where($cond_where)
                        ->count();
                } else {
                    $total_num = $case_list_model->join('case_type AS type ON case_list.id = type.case_id')
                        ->join("(SELECT distinct(flowid) FROM org_workflow_step ".$cond_where_child." ) AS step ON type.wfid = step.flowid")
                        ->where($cond_where)
                        ->count();
                }

                $Page = new \Think\Page($total_num, $limit);
                if($total_num>$listRows){
                    $Page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
                }
                
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
                $cond_order = array('type.handle_time' => 'desc');
                if($cond_where_child == "") {
                    $case_list_info = $case_list_model->field($search_field_arr)->join('case_type AS type ON case_list.id = type.case_id')
                    ->where($cond_where)
                    ->order($cond_order)
                    ->limit($Page->firstRow, $Page->listRows)
                    ->select();
                } else {
                    $case_list_info = $case_list_model->field($search_field_arr)->join('case_type AS type ON case_list.id = type.case_id')
                    ->join("(SELECT distinct(flowid) FROM org_workflow_step ".$cond_where_child." ) AS step ON type.wfid = step.flowid")
                    ->where($cond_where)
                    ->order($cond_order)
                    ->limit($Page->firstRow, $Page->listRows)
                    ->select();
                }
                $metaTitle = '待办案件';
            } break;
        
            //已办
            case 'done':{
                $metaTitle = '已办案件';
            } break;
            
            //办结
            case 'end':{
                $metaTitle = '办结案件';
            } break;
            //全公司
            case 'all':{
                $metaTitle = '所有案件';
            } break;
        }
        //业务类型
        $confBusinessType = $this->caseListModel->getCaseType();
        //贷款类型
        $confLoanType = $this->caseListModel->getLoanType();
        //案例状态
        $confCaseStatus = $this->caseListModel->getConfCaseStatus();
        /***流程类型***/
        $flowType = new \Admin\Model\FlowTypeModel();
        $conf_where['category'] = 1;
        $confFlowTypeTemp = $flowType->getFlowTypeByConf($conf_where);
        
        /***流程类型数组***/
        $confFlowType = array();
        if(is_array($confFlowTypeTemp) && !empty($confFlowTypeTemp))
        {
            foreach($confFlowTypeTemp as $key => $value)
            {
                $confFlowType[$value['id']] = $value['type_name'];
            }
        }
        
        /***流程节点数组***/
        $flowConfType = new \Admin\Model\FlowConfModel();
        $confFlowNodeTemp = $flowConfType->getFlowNodeByConf();
        $confFlowNode = array();
        if(is_array($confFlowNodeTemp) && !empty($confFlowNodeTemp))
        {
            foreach($confFlowNodeTemp as $key => $value)
            {
                $confFlowNode[$value['id']] = $value['node_name'];
            }
        }
        
        
        $show = $Page->show();
        $this->assign('_Page', $show ? $show : '');
        $this->assign('_Total',$total);
        $this->assign('confBusinessType',$confBusinessType);
        $this->assign('confLoanType',$confLoanType);
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
        $feeType = $this->feeStandardModel->getFeeType();
        $this->assign('meta_title', '费用标准');
        $this->assign('feeType', $feeType);
        $this->display();
    }
    
    
    /*
     * 新增提交
     */
    public function do_add()
    {
        if (IS_POST) {
            if ($this->feeStandardModel->create()) {
                if ($this->feeStandardModel->add()!==false) {
                    $this->success('添加成功', U('FeeStandard/index'));
                } else {
                    $this->error('添加失败');
                }
            } else {
                $this->error($this->feeStandardModel->getError());
            }
        }
    }
    
    
    /**
     * 编辑费用
     */
    public function edit($id)
    {
        $feeInfo = $this->feeStandardModel->find($id);
        $feeType = $this->feeStandardModel->getFeeType();
        
        if (false === $feeInfo) {
            $this->error('未查询到收费项目');
        }
        $this->assign('id', $id);
        $this->assign('feeInfo', $feeInfo);
        $this->assign('feeType', $feeType);
        $this->assign('meta_title', '修改费用');
        $this->display();
    }        
    
    /**
     * 提交编辑
     */
    public function do_edit()
    {
        if (IS_POST) {
            if ($this->feeStandardModel->create()) {
                if ($this->feeStandardModel->save() !== false) {
                    $this->success('修改成功！', U('FeeStandard/index'));
                } else {
                    $this->error('修改失败！');
                }
            } else {
                $this->error($this->feeStandardModel->getError());
            }
        } 
    }
}
