<?php
namespace Home\Controller;
use User\Api\UserApi;
/**
 * 前台线路控制器
 */
class LineController extends HomeController {

    // 线路列表
    public function index(){
        $LineView = D('LineView');
        $catid =  I('get.catid',1);
        $pageNum = 10;
        $nowPage =  I('get.p',1);
        $firstRow = ($nowPage-1)*$pageNum;
        $souso = array('catid'=>$catid);
        $map = array(
            'type_id' => $catid,
            'end_time' => array('gt', NOW_TIME),
            'is_default' => 1,
        );
        // 搜索关键词
        $keyword = I('keyword', '', 'trim');
        if (!empty($keyword) && $keyword != '请输入主题或关键词') {
            $map['title'] = array('like', '%'. $keyword .'%');
            $souso['keyword'] = $keyword;
        }
        // 出发城市
        $start = I('start', '', 'trim');
        if (!empty($start)) {
            if (strpos($start, '-') === false) {
                $map['start'] = array('like', '%'. $start .'%');
            } else {
                $keywords = explode('-', $start);
                foreach ($keywords as $key => $value) {
                    $keywords[$key] = '%'. $value .'%';
                }
                $map['start'] = array('like', $keywords,'OR');
            }
            $souso['start'] = $start;
        }
        // 目的地
        $dest = I('dest');
        if (!empty($dest)) {
            $map['type_id'] = $dest;
            $souso['dest'] = $dest;
        }
        // 参团性质
        $ct_type = I('ct_type', 0, 'intval');
        if (!empty($ct_type)) {
            $map['ct_type'] = $ct_type;
            $souso['ct_type'] = $ct_type;
        }
        // 线路类型
        $l_type = I('l_type', 0, 'intval');
        if (!empty($l_type)) {
            $map['l_type'] = $l_type;
            $souso['l_type'] = $l_type;
        }
        // 旅行天数
        $daynum = I('daynum', 0, 'intval');
        if (!empty($daynum)) {
            $map['daynum'] = array('elt', $daynum);
            $souso['daynum'] = $daynum;
        }
        // 价格区间
        $price = I('price');
        if (!empty($price)) {
            list($p1, $p2) = explode('-', $price);
            if ($p1 == 0 && $p2 > 0) {
                $map['price'] = array('elt', $p2);
            } elseif ($p1 > 0 && $p2 == 0) {
                $map['price'] = array('egt', $p1);
            } elseif ($p1 > 0 && $p2 > 0) {
                $map['price'] = array(array('egt', $p1), array('elt', $p2));
            }
            $souso['price'] = $price;
        }
        // sort
        $sort = I('sort');
        switch ($sort) {
            case 'base_order':
                $order = 'base_order desc, end_time desc, line_id desc';
                break;
            case 'price':
                $order = 'price desc, end_time desc, line_id desc';
                break;
            default:
                $order = 'end_time desc, line_id desc';
                break;
        }
        $souso['sort'] = $sort;
        // $map['cname'] = '北京';
        $list = $LineView->field('')->where($map)->order($order)->limit($firstRow,$pageNum)->group('line_id')->select();
        // echo $LineView->_sql();
        foreach ($list as $key => $val) {
             $list[$key]['start_date'] = get_start_date($val['date_price_data']);
             $list[$key]['img'] = get_cover(array_shift(explode(',', $val['images'])), 'path');
             $list[$key]['url'] = U('Line/show', array('id'=>$val['line_id']));
        }

        $categorys = array();
        foreach ($this->line_cate as $value) {
            if ($value['id'] == $catid) {
                $categorys = $value;
                break;
            } else {
                foreach ($value['_'] as $val) {
                    if ($val['id'] == $catid) {
                        $categorys = $val;
                        break;
                    } else {
                        continue;
                    }
                }
            }
        }
        $this->assign('categorys', $categorys);

        $count = $LineView->where($map)->count();
        $_page = article_pages($count,$pageNum);
        $this->assign('list', $list);
        $this->assign('souso', $souso);
        $this->assign('_page', $_page);
        $this->display();
    }

    // 线路详情
    public function show($id = 0){
        if (empty($id)) {
            $this->error('无效参数');
        }
        $Line = M('Line');
        $line_info = $Line->find($id);
        // 线路信息
        $line_info['images'] = explode(',', $line_info['images']);
        foreach ($line_info['images'] as $key => $val) {
            $line_info['images'][$key] = get_cover($val, 'path');
        }
        $line_info['xingcheng'] = unserialize($line_info['xingcheng']);
        $line_info['remark'] = unserialize($line_info['remark']);
        // 套餐信息
        $map = array(
            'line_id' => $id,
            'end_time' => array('egt', strtotime('+'.$line_info['earlier_date'].'day'))
        );
        $line_tc = M('LineTc')->where($map)->select();
        if (empty($line_tc)) {
            $this->error('没有报价方案');
        }
        $default_tc = array();
        foreach ($line_tc as $key => $value) {
            if ($value['is_default']) {
                $default_tc = $value;
                break;
            }
        }
        if (empty($default_tc)) {
            $default_tc = $line_tc[0];
        }
        $map = array(
            'product_id' => $id,
            'status' => 1
        );
        $comment_lists = M('Comment')->where($map)->select();

        $this->assign('comment_lists', $comment_lists);
        $this->assign('line_info', $line_info);
        $this->assign('line_tc', $line_tc);
        $this->assign('default_tc', $default_tc);
        $this->display();
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

    public function order(){
        if (IS_POST) {
            $Order = D('Order');
            $order_id = 'NS' . date('YmdHis') . mt_rand(1000, 9999);
            $uid = is_login();
            $site_id = I('site_id', 0, 'intval');
            if ($uid) {
                $result = $Order->input($order_id, $uid, $site_id);
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
                        $result = $Order->input($order_id, $uid, $site_id);
                    }
                } else {
                    $user_info = $User->getinfo($mobile, 3);
                    $result = $Order->input($order_id, $user_info[0], $site_id);
                }
            }

            if ($result) {
                send_sms($mobile, array('orderid'=>$order_id), 'onOrder');
                $this->redirect('checkOrder', array('order_id'=>$order_id));
            } else {
                $this->error('订单提交失败');
            }
        } else {
            $line_id = I('line_id', 0, 'intval');
            $tc_id = I('type_id', 0, 'intval');
            $date = I('date', 0, 'strtotime');
            if (empty($line_id) || empty($tc_id) || empty($date)) {
                $this->error('无效参数');
            }
            // 线路信息
            $line_info = M('Line')->find($line_id);
            // 套餐信息
            $map = array(
                'line_id' => $line_id,
                'end_time' => array('egt', strtotime('+'.$line_info['earlier_date'].'day'))
            );
            $line_tc = M('LineTc')->where($map)->select();
            if (empty($line_tc)) {
                $this->error('没有报价方案');
            }
            $tc_info = array();
            foreach ($line_tc as $key => $value) {
                if ($value['tc_id'] == $tc_id) {
                    $tc_info = $value;
                    break;
                }
            }
            $ext_time = strtotime('+'.$line_info['earlier_date'].'day');
            $tc_str = explode(',', $tc_info['date_price_data']);
            foreach ($tc_str as $value) {
                list($k, $val) = explode('|', $value);
                $k = strtotime($k);
                if ($k <= $ext_time) {
                    continue;
                }
                if ($k == $date) {
                    $tc_info['price_info'] = explode('-', $val);
                    $tc_info['price_info'][] = date('Y-m-d', $k);
                    break;
                }
            }
            if (empty($tc_info['price_info'])) {
                $this->error('没有价格');
            }
            $this->assign('line_info', $line_info);
            $this->assign('line_tc', $line_tc);
            $this->assign('tc_info', $tc_info);
            $this->display();
        }

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

    // 价格方案
    public function showTypeDate($line_id, $type_id){
        $line_info = M('Line')->find($line_id);
        $line_tc = M('LineTc')->find($type_id);
        $ext_time = strtotime('+'.$line_info['earlier_date'].'day');
        $tc_str = explode(',', $line_tc['date_price_data']);
        $tc = array();
        foreach ($tc_str as $value) {
            list($k, $val) = explode('|', $value);
            if (strtotime($k) <= $ext_time) {
                continue;
            }
            $tc[$k] = explode('-', $val);
            $tc[$k][] = $k;
        }
        // print_r($tc);

        $moon = array();
        foreach ($tc as $key => $value) {
            $moon[date('Y-n', strtotime($key))][$key] = $value;
        }
        // print_r($moon);
        $date_type = array();
        foreach ($moon as $key => $value) {
            $t_num = date('t', strtotime($key));
            for ($i=0; $i < $t_num; $i++) {
                $day_k = date('Y-m-d', strtotime($key . '+' . $i . 'day'));
                $day_v = date('Y-n-j', strtotime($key . '+' . $i . 'day'));
                $date_type[$key][$day_k] = $tc[$day_v];
            }
        }
        // print_r($date_type);
        $this->assign('line_info', $line_info);
        $this->assign('line_tc', $line_tc);
        $this->assign('date_type', $date_type);
        $this->display();
    }

    // 查看价格方案
    public function showSpecifyPrice($line_id, $date){
        $line_info = M('Line')->find($line_id);
        $line_tcs = M('LineTc')->where(array('line_id'=>$line_id))->select();
        $data = array('status' => 0);
        $date = date('Y-n-j', strtotime($date));
        foreach ($line_tcs as $key => $line_tc) {
            $ext_time = strtotime('+'.$line_info['earlier_date'].'day');
            $tc_str = explode(',', $line_tc['date_price_data']);
            $tc = array();
            foreach ($tc_str as $value) {
                list($k, $val) = explode('|', $value);
                if (strtotime($k) <= $ext_time) {
                    continue;
                }
                $tc[$k] = explode('-', $val);
                $tc[$k][] = $k;
            }
            foreach ($tc as $key => $value) {
                if ($date == $key) {
                    $data['msg'][] = array(
                        'date' => date('Y-m-d', strtotime($date)),
                        'price' => $line_tc['price'],
                        'price_child_d' => $value[1],
                        'price_d' => $value[0],
                        'stock' => '-1',
                        'type_id' => $line_tc['tc_id'],
                        'typename' => $line_tc['typename'],
                        'typename_s' => $line_tc['typename'],
                    );
                }
            }
        }
        $this->ajaxReturn($data);
        exit;
    }

    // 咨询留言
    public function message() {
        if (IS_POST) {
            $result = D('Message')->input();
            if ($result) {
                $this->success('咨询成功，等待管理员回复');
            } else {
                $this->error('失败');
            }
        }
    }
}
