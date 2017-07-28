<?php
/**
 * 交易人列表控制器
 */
namespace Admin\Controller;
use Common\Controller\AdminController;

class CaseDocumentController extends AdminController
{
    
    /**
     * 收件资料列表
     */
    public function index($case_id)
    {   
        $type = I('type',1);
        $model = D('Common/CaseDocument');
        if ($case_id) {
        $documnet_list = $model->where('case_id = '.$case_id.' and  transpart = '.$type)->select();
        }
        $this->assign('tab_arr', get_case_info_tabs());
        $this->assign('type_arr', D('Common/RequiredDocument')->getDocumentType());
        $this->assign('tab','CaseDocument/index');
        $this->assign('case_id', $case_id);
        $this->assign('type',$type);
        $this->assign('documnet_list',$documnet_list);
        $this->display();
    }
    

    /*
     * 增加
     */
    public function add($case_id)
    {   
        $type = I('type',1);
        $document_list = M('RequiredDocument')->where('isvalid = 1')->select();
        foreach($document_list as $value) {
            $res[$value['document_type']][] = $value;
        }
        
        
        $this->assign('type', $type);
        $this->assign('case_id', $case_id);
        $this->assign('type_arr', D('Common/RequiredDocument')->getDocumentType());
        $this->assign('doc_arr', $res);
        $this->display();
    }
    
    
    /*
     * 新增提交
     */
    public function do_add()
    {   
        $model = D('Common/CaseDocument');
        if (IS_POST) {
            $doc = I('post.doc');
            $case_id = I('post.case_id');
            $type = I('post.transpart');
            if (empty($doc)) {
                $this->error('您未选择收件资料，请重新选择');
            }
            if (!$case_id || !$type) {
                $this->error('参数异常！');
            }
            
            $doc_arr = M('RequiredDocument')->where('isvalid=1')->getField('id, document_name, document_type');
            foreach ($doc as $va) {
                $result['file_id'] = $va;
                $result['file_name'] = $doc_arr[$va]['document_name'];
                $result['type_id'] = $doc_arr[$va]['document_type'];
                $result['case_id'] = $case_id;
                $result['transpart'] = $type;
                $result['getit'] = 0;
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
        $model = D('Common/CaseDocument');
        $ids = I('get.ids');
        $id_arr = array_filter(explode(',', $ids));
        if (empty($id_arr)) {
            $this->error('未获取到收件资料'); 
        } else {
            $where['id'] = array('IN', $id_arr);
            $data['getit'] = 1;
            $data['accept_uid'] = session('userInfo.id');
            $data['accept_name'] = session('userInfo.true_name');
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
        if (D('Common/CaseDocument')->where(array("id"=>$id))->delete()) {
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        } 
    } 
         
}
