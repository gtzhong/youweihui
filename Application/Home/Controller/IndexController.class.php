<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;

/**
 * 前台首页控制器.
 */
class IndexController extends HomeController {
    /**
     * [index description].
     *
     * @return [type] [description]
     */
    public function index() {

        $this->display();
    }

    /**
     * [send description].
     *
     * @return [type] [description]
     */
    public function send() {
        print_r(send_sms('15044858848', array('content' => '测试信息')));
    }
}
