<?php
namespace Home\Model;
use Think\Model;

class OrderModel extends Model{
    protected $_map = array(
        'line_id' =>'product_id',
        'visa_id' =>'product_id',
    );

    // 自动验证
    protected $_validate = array(
        array('product_id','require','产品id必须！'),
        array('truename','require','联系人必须！'),
        array('mobile','require','手机号必须！'),
    );

    // 自动完成
    protected $_auto = array(
        array('order_type', 'line', self::MODEL_INSERT),
        array('order_status', 1, self::MODEL_INSERT),
        array('pay_status', 1, self::MODEL_INSERT),
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
        array('is_read', 0, self::MODEL_INSERT),
    );

    // 写入
    public function inputLine($order_id, $user_id = 1, $site_id = 1){
        $info = $this->create();
        $info['order_type'] = 'line';
        $info['user_id'] = $user_id;
        $info['site_id'] = $site_id;
        $info['order_id'] = $order_id;
        $info['reserve_info'] = $this->_lineReserveInfo();
        $info['order_price'] = $this->_lineOrderPrice();
        $info['product_price'] = $this->_lineOrderPrice();
        $info['product_num'] = $this->_lineProductNum();
        return $this->add($info);
    }
    // 产品数量
    protected function _lineProductNum(){
        $adult_num = I('adult_num');
        $child_num = I('child_num');
        return $adult_num + $child_num;
    }
    // 预定信息
    protected function _lineReserveInfo(){
        $data = array(
            'product_id'    => I('line_id'),
            'adult_num'     => I('adult_num'),
            'child_num'     => I('child_num'),
            'adult_price'   => I('adult_price'),
            'child_price'   => I('child_price'),
            'start_time'    => I('start_time'),
            'truename'      => I('truename'),
            'sex'           => I('sex'),
            'mobile'        => I('mobile'),
            'user_email'    => I('user_email'),
            'tel'           => I('tel'),
            'shenfen_type'  => I('shenfen_type'),
            'shenfen_value' => I('shenfen_value'),
            'qq'            => I('qq'),
            'user_intro'    => I('user_intro'),
        );
        return serialize($data);
    }
    // 订单价格
    protected function _lineOrderPrice(){
        $adult_num      = I('adult_num');
        $child_num      = I('child_num');
        $adult_price    = I('adult_price');
        $child_price    = I('child_price');
        return $adult_num * $adult_price + $child_num * $child_price;
    }

    // 写入
    public function inputVisa($order_id, $user_id = 1, $site_id = 1){
        $info = $this->create();
        $info['order_type'] = 'visa';
        $info['user_id'] = $user_id;
        $info['site_id'] = $site_id;
        $info['order_id'] = $order_id;
        $info['reserve_info'] = $this->_visaReserveInfo();
        $info['order_price'] = $this->_visaOrderPrice();
        $info['product_price'] = $this->_visaOrderPrice();
        $info['product_num'] = I('adult_num');
        return $this->add($info);
    }

    // 预定信息
    protected function _visaReserveInfo(){
        $data = array(
            'product_id'    => I('visa_id'),
            'adult_num'     => I('adult_num'),
            'adult_price'   => I('adult_price'),
            'start_time'    => I('start_time'),
            'truename'      => I('truename'),
            'sex'           => I('sex'),
            'mobile'        => I('mobile'),
            'user_email'    => I('user_email'),
            'tel'           => I('tel'),
            'shenfen_type'  => I('shenfen_type'),
            'shenfen_value' => I('shenfen_value'),
            'qq'            => I('qq'),
            'user_intro'    => I('user_intro'),
        );
        return serialize($data);
    }

    // 订单价格
    protected function _visaOrderPrice(){
        $adult_num      = I('adult_num');
        $adult_price    = I('adult_price');
        return $adult_num * $adult_price;
    }

}
