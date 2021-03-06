<?php
/**
 *
 */
namespace Home\Model;
use Think\Model\ViewModel;
class LineViewModel extends ViewModel {
    /**
     * [$viewFields description]
     * @var array
     */
    public $viewFields = array(
        'Line'=>array('line_id','title','sub_title','images','dest','ct_type','start','base_order','is_position','_type'=>'LEFT'),
        'LineType'=>array('type_id','_on'=>'Line.line_id=LineType.line_id','_type'=>'LEFT'),
        'LineCate'=>array('title'=>'cname','_on'=>'LineCate.id=LineType.type_id','_type'=>'LEFT'),
        'LineTc'=>array('tc_id','typename','price','best_price','end_time','date_price_data','_on'=>'Line.line_id=LineTc.line_id and Line.status=LineTc.status=1','_type'=>'LEFT'),
    );



}
