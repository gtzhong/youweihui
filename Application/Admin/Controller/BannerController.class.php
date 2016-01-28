<?php
namespace Admin\Controller;
use Admin\Model\AuthGroupModel;
use Think\Page;

/**
 * 后台内容控制器
 */
class BannerController extends AdminController {

    /* 列表 */
    public function dList(){
        $map = array();
        $bid = I('bid', 0, 'intval');
        if (!empty($bid)) {
            $map['bid'] = $bid;
        }
        $truename = I('truename');
        if(!empty($truename)){
            $map['title'] = array('like','%'.$truename.'%');
        }
        $banner_info = D('Banner')->find($bid);
        $list = $this->lists('BannerData', $map, 'sort desc, id desc');
        $this->assign('_list', $list);
        $this->assign('bid', $bid);
        $this->assign('banner_info', $banner_info);
        $this->meta_title = '广告列表';
        $this->display();
    }

    /* 新增 */
    public function dAdd() {
        $bid = I('bid', 0, 'intval');
        if (IS_POST) {
            $BannerData = D('BannerData');
            if (!$BannerData->input()) {
                $this->error($BannerData->getError());
            } else {
                $this->success('新增成功', U('dList', array('bid'=>$bid)));
            }
        } else {
            $this->assign('info', array('bid'=>$bid));
            $this->assign('bid', $bid);
            $banner_info = D('Banner')->find($bid);
            $this->assign('banner_info', $banner_info);
            $this->meta_title = '新增广告';
            $this->display("dEdit");
        }
    }

    /* 修改 */
    public function dEdit() {
        $BannerData = D('BannerData');
        if (IS_POST) {
            if (!$BannerData->update()) {
                $this->error($BannerData->getError());
            } else {
                $this->success('更新成功', U('dList?bid=' . I('bid')));
            }
        } else {
            $id = I('id',0,'intval');
            $bid = I('bid',0,'intval');
            $info = $BannerData->find($id);
            if (!$info) {
                $this->error('不存在！');
            }
            $banner_info = D('Banner')->find($info['bid']);
            $this->assign('banner_info', $banner_info);
            $this->assign('info', $info);
            $this->assign('bid', $bid);
            $this->meta_title = '更新广告';
            $this->display();
        }
    }

    /*  删除 */
    public function dDel() {
        $id = array_unique((array)I('id',0));
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $map = array('id' => array('in', $id) );
        $BannerData = D('BannerData');
        if($BannerData->where($map)->delete()){
            action_log();
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /* 列表 */
    public function index(){
        $map = array();
        $map['status'] = 1;
        $id = I('id', 0, 'intval');
        if (!empty($id)) {
            $map['id'] = I('id');
        }
        $truename = I('truename');
        if(!empty($truename)){
            $map['name'] = array('like','%'.$truename.'%');
        }
        $list = $this->lists('Banner', $map, 'sort desc, id desc');
        $this->assign('_list', $list);
        $this->meta_title = '广告位列表';
        $this->display();
    }

    /* 新增 */
    public function add() {
        if (IS_POST) {
            $Banner = D('Banner');
            if (!$Banner->input()) {
                $this->error($Banner->getError());
            } else {
                $this->success('新增成功', U('index'));
            }
        } else {
            $this->assign('info', array('id'=>I('id')));
            $this->meta_title = '新增广告位';
            $this->display("edit");
        }
    }

    /* 修改 */
    public function edit() {
        $Banner = D('Banner');
        if (IS_POST) {
            if (!$Banner->update()) {
                $this->error($Banner->getError());
            } else {
                $this->success('更新成功', U('index'));
            }
        } else {
            $id = I('id',0,'intval');
            $info = $Banner->find($id);
            if (!$info) {
                $this->error('不存在！');
            } else {
                $this->assign('info', $info);
            }
            $this->meta_title = '更新广告位';
            $this->display();
        }
    }

    /*  删除 */
    public function del() {
        $id = array_unique((array)I('id',0));
        //print_r($id); exit;
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $map = array('id' => array('in', $id) );
        if(D('Banner')->where($map)->delete()){
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

}
