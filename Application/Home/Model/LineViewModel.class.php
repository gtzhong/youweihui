<?php
// 线路视图模型
namespace Home\Model;
use Think\Model\ViewModel;
class LineViewModel extends ViewModel {
   public $viewFields = array(
     'Line'=>array('line_id','title','sub_title','images', '_type'=>'LEFT'),
     'LineType'=>array('type_id', '_on'=>'Line.line_id=LineType.line_id','_type'=>'LEFT'),
     'LineTc'=>array('tc_id','typename','price','best_price','end_time',
                     'date_price_data','_on'=>'Line.line_id=LineTc.line_id and Line.status=LineTc.status=1','_type'=>'LEFT'
                    ),
   );



 }
