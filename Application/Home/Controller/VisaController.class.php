<?php
namespace Home\Controller;
/**
 * 前台签证控制器
 */
class VisaController extends HomeController {

    // 签证列表
    public function index(){
            $catid = I('get.catid','');
            $zone = I('get.zone','');
            $sub_id = I('get.sub_id','');
            $keyword = I('get.keyword','');
          //  print_r($keyword);
            $pageNum = 6;
            $nowPage =  I('get.p',1);
            $firstRow = ($nowPage-1)*$pageNum;

            $map = array();
            $map['visa_catid'] = $catid;
            $map['zone'] = $zone;
            $map['sub_id'] = $sub_id;
            $map['title'] = array('like','%'.$keyword.'%');

            $map['_logic'] = 'OR';
            $where['_complex'] = $map;
            $where['status'] = 1;

            $Visa = M('Visa');
            $visa_lists = $Visa->where($where)->order('sort desc,update_time desc')->limit($firstRow,$pageNum)->select();
            foreach($visa_lists as $k=>$val){
                   $visa_lists[$k]['is_yaoqing'] = $val['is_yaoqing']?'需要':'不需要';
                   $visa_lists[$k]['is_mianshi'] = $val['is_mianshi']?'需要':'不需要';
                   $visa_lists[$k]['url'] = U('Visa/show',array('id'=>$val['visa_id']));
                   $visa_lists[$k]['image'] = get_cover($val['cover_id'],'path');
                   $visa_lists[$k]['zone'] = get_visa_field($val['zone'],'title');
            }

            $count = $Visa->where($where)->count();
            $page = article_pages($count,$pageNum);
            $sub_ids = C(QZ_TYPE);
            $this->assign('sub_ids',$sub_ids);
            $this->assign('visa_lists',$visa_lists);
            $this->assign('_page',$page);
            $this->display();
    }

    // 签证详情
    public function show(){
            $visa_id = I('get.id');
            if(empty($visa_id)){
              $this->error();
            }
            $Visa = M('Visa');
            $map = array();
            $map['visa_id'] = $visa_id;
            $map['status'] = 1;
            $detail = $Visa->where($map)->find();
            $detail['image'] = get_cover($detail['cover_id'],'path');
            $detail['is_yaoqing'] = $detail['is_yaoqing']?'需要':'不需要';
            $detail['is_mianshi'] = $detail['is_mianshi']?'需要':'不需要';
            $this->assign('detail',$detail);
            $this->display();
    }

    public function order(){
        if (IS_POST) {
            $Order = D('Order');
            $order_id = 'NS' . date('YmdHis') . mt_rand(1000, 9999);
            $uid = is_login();
            $site_id = I('site_id', 0, 'intval');
            if ($uid) {
                $result = $Order->inputVisa($order_id, $uid, $site_id);
                $mobile = get_userinfo($uid, 3);
            } else {
                $mobile = I('mobile', '', 'trim');
                /* 调用注册接口注册用户 */
                $User = new UserApi;
                $res = $User->checkMobile($mobile);
                if ($res == 1) {
                    $password = mt_rand(100000, 999999);
                    $uid = $User->register('', $password, '', $mobile);
                    if(0 < $uid){ //注册成功
                        send_sms($mobile, array('mobile'=>$mobile, 'password'=>$password), 'password');
                        $result = $Order->inputVisa($order_id, $uid, $site_id);
                    }
                } else {
                    $user_info = $User->getinfo($mobile, 3);
                    $result = $Order->inputVisa($order_id, $user_info[0], $site_id);
                }
            }

            if ($result) {
                send_sms($mobile, array('orderid'=>$order_id), 'onOrder');
                $this->redirect('checkOrder', array('order_id'=>$order_id));
            } else {
                $this->error('订单提交失败');
            }
        } else {
            $visa_id = I('visa_id', 0, 'intval');
            if (empty($visa_id)) {
                $this->error('无效参数');
            }
            // 线路信息
            $map = array(
                'visa_id' => $visa_id,
                'expire_date' => array('egt', NOW_TIME)
            );
            $visa_info = M('Visa')->where($map)->find();
            if (empty($visa_info)) {
                $this->error('不存在');
            }
            $this->assign('visa_info', $visa_info);
            $this->display();
        }

    }

    public function checkOrder($order_id = ''){
        if (empty($order_id)) {
            $this->error('非法参数');
        }
        $order_info = D('Order')->where(array('order_id'=>$order_id))->find();
        if (empty($order_info)) {
            $this->error('订单不存在');
        }
        $this->assign('order_info', $order_info);
        $this->display();
    }

    // 检查验证码
    public function checkCode($verify = ''){
        if (check_verify($verify)) {
            $this->success('成功');
        } else {
            $this->error('失败');
        }
    }

    /* 验证码 */
	public function verify(){
		$verify = new \Think\Verify();
        $verify->length   = 4;
        $verify->codeSet = '0123456789';
        $verify->useCurve = false;
		$verify->entry(1);
	}
}
