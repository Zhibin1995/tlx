<?php
/**
 * tlx2
 * PhpStorm
 * @author zhibin
 * @date 2019-10-21
 * Class IndexController.php
 */

namespace api\modules\v1\controllers;


use api\controllers\OnAuthController;
use common\helpers\ResultDataHelper;
use common\models\app\Address;
use common\models\app\Banner;
use common\models\app\Category;
use common\models\app\Goods;
use common\models\app\MakeDetail;
use common\models\app\Order;
use common\models\app\OrderDetail;
use common\models\app\OrderMake;
use common\models\app\Package;
use common\models\app\Shop;
use common\models\app\Show;
use common\models\app\SysSet;
use common\models\app\Tip;

class OrderController extends OnAuthController
{
    public $modelClass = '';
    protected $optional = ['list', 'wait-make','refund','detail','wait-serve','make','unmake'];

    public function actionList()
    {
        $post = $this->getPost();
        $page = $post['page'] ?? 1;
        $size =$post['size'] ?? 10;
        $offset = ($page - 1 )*$size;
        $member_id = $post['member_id'];
        $status = $post['status'];
        $query = Order::find();
        $query->andWhere(['status' => 1]);
        $query->andWhere(['member_id' => $member_id]);

        if($status){
            $query->andWhere(['in','pay_status',[2,3]]);
        }
        $list =$query->offset($offset)->limit($size)->asArray()->all();
        $res = [];
        foreach ($list as $item){
            if($item['type'] == 1){
                $goods_all = OrderDetail::findAll(['order_id' => $item['id']]);
                $goods = [];
                foreach ($goods_all as $item_g){
                    $good_info = Goods::findOne($item_g->good_id);
                    $good = [];
                    $good['id'] = $good_info->id;
                    $good['num'] = $item_g->num;
                    $good['name'] = $good_info->name;
                    $good['desc'] = $good_info->desc;
                    $good['price'] = $good_info->price;
                    $good['img'] = explode(',',$good_info->url)[0];
                    $goods[] = $good;
                }
            }else{
                $goods = [];
                $good_info = Package::findOne($item['package_id']);
                $good = [];
                $good['id'] = $good_info->id;
                $good['num'] = 1;
                $good['name'] = $good_info->name;
                $good['desc'] = $good_info->desc;
                $good['price'] = $good_info->price;
                $good['img'] = explode(',',$good_info->url)[0];
                $goods[] = $good;


            }
            $temp = [];
            $temp['create_at'] = date('Y-m-d H:i:s',$item['created_at']);
            $temp['status'] = $item['pay_status'];
            $temp['id'] = $item['id'];
            $temp['type'] = $item['type'];
            $temp['amount'] = $item['amount'];
            $temp['goods'] = $goods;
            $res[] = $temp;
        }
        return $res;
    }
    public function actionRefund(){
        $post = $this->getPost();
        $order = Order::findOne($post['id']);
        if($order->status != 1){
            return ResultDataHelper::api(201, '不可退单');
        }
        $refund_info = OrderDetail::findOne(['order_id' => $post['id'],'is_refund' => 1]);
        $make_info = OrderDetail::findOne(['order_id' => $post['id'],'make_status' => 0]);
        if($refund_info || $make_info){
            return ResultDataHelper::api(201, '不可退单');

        }
        $order->status =2;
        $order->refund_remark = $post['remark'];
        OrderDetail::updateAll(['is_refund' => 1],['order_id' => $post['id']]);
        return $order->save();
    }
    public function actionWaitMake(){
        $post = $this->getPost();
        $member_id = $post['member_id'];
        $page = $post['page'] ?? 1;
        $size =$post['size'] ?? 10;
        $offset = ($page - 1 )*$size;
        $status = $post['status'];
        $list = OrderDetail::find()->andWhere(['member_id' => $member_id ,'make_status' => $status])->andWhere(['!=','is_refund',1])->offset($offset)->limit($size)->all();
        $res = [];
        foreach ($list as $item){
            $good_info = Goods::findOne($item->good_id);
            $good = [];
            $good['detail_id'] = $item->id;
            $good['id'] = $good_info->id;
            $good['num'] = $item->num;
            $good['name'] = $good_info->name;
            $good['desc'] = $good_info->desc;
            $good['price'] = $good_info->price;
            $good['time'] = $good_info->times;
            $good['img'] = explode(',',$good_info->url)[0];
            if($item->make_status >= 2){
                $make = OrderMake::findOne($item->make_id);
                $good['shop_name'] = Shop::find()->where(['id' => $make->shop_id])->select('username')->scalar();
                if($item->make_status == 3){
                    $good['finish_time'] = date('Y-m-d H:i:s',$make->finsh);
                }
            }
            $res[] = $good;
        }
        return $res;
    }
    public function actionDetail(){
        $post = $this->getPost();
        $info = Order::findOne($post['id']);
        $order_info['create_at'] = date('Y-m-d H:i:s',$info->created_at);
        $order_info['status'] = $info->pay_status;
        $order_info['id'] = $info->id;
        $order_info['type'] = $info->type;
        $order_info['amount'] = $info->amount;
        $order_info['address'] = $info->address;
        $order_info['username'] = $info->username;
        $order_info['userphone'] = $info->userphone;
        if($info->type == 1){
            $goods_all = OrderDetail::findAll(['order_id' => $info->id]);
            $goods = [];
            foreach ($goods_all as $item_g){
                $good_info = Goods::findOne($item_g->good_id);
                $good = [];
                $good['id'] = $good_info->id;
                $good['num'] = $item_g->num;
                $good['name'] = $good_info->name;
                $good['desc'] = $good_info->desc;
                $good['price'] = $good_info->price;
                $good['img'] = explode(',',$good_info->url)[0];
                $goods[] = $good;
            }
        }else{
            $goods = [];
            $good_info = Package::findOne($info->package_id);
            $good = [];
            $good['id'] = $good_info->id;
            $good['num'] = 1;
            $good['name'] = $good_info->name;
            $good['desc'] = $good_info->desc;
            $good['price'] = $good_info->price;
            $good['img'] = explode(',',$good_info->url)[0];
            $goods[] = $good;
        }
        $order_info['goods'] =$goods;
        return $order_info;
    }
    public function actionWaitServe(){
        $post = $this->getPost();
        $page = $post['page'] ?? 1;
        $size =$post['size'] ?? 10;
        $offset = ($page - 1 )*$size;
        $member_id = $post['member_id'];
        $list = OrderMake::find()->where(['make_status' => 0,'member_id' => $member_id])->offset($offset)->limit($size)->all();
        $res = [];
        foreach ($list as $item){
            $detail_list = OrderDetail::find()->andWhere(['in','id',explode(',',$item->detail_ids)])->all();
            $detail_arr = [];
            foreach ($detail_list as $item1){
                $good_info = Goods::findOne($item1->good_id);
                $good = [];
                $good['id'] = $good_info->id;
                $good['num'] = $item->num;
                $good['name'] = $good_info->name;
                $good['desc'] = $good_info->desc;
                $good['price'] = $good_info->price;
                $good['img'] = explode(',',$good_info->url)[0];
                $detail_arr[] = $good;
            }
            $temp =[] ;
            $address = Address::findOne($item->address_id);
            $temp['id'] = $item->id;
            $temp['date'] = $item->date_string;
            $temp['status'] = $item->make_status;
            $temp['order_num'] = date('YmdHis',$item->created_at).$item->id;
            $temp['address'] = $address->address_name.$address->address_details;
            $temp['goods'] = $detail_arr;
            $res[] = $temp;
        }
        return $res;
    }
    public function actionMake(){
        $post = $this->getPost();
        $ids = $post['ids'];
        $member_id = $post['member_id'];
        $address_id = $post['address_id'];
        $remark = $post['remark'] ?? '';
        $date_string = $post['string'];
        $year = $post['year'];
        $start = $post['start'];
        $end = $post['end'];
        $times = $post['times'];
        $shop_id = 1;
        $code = rand(100000,999999);
        $model = new OrderMake();
        $model->member_id = $member_id;
        $model->detail_ids = $ids;
        $model->address_id = $address_id;
        $model->remark = $remark;
        $model->date_string = $date_string;
        $model->date = strtotime($year);
        $model->start = strtotime($year.' '.$start);
        $model->end = strtotime($year.' '.$end);
        $model->shop_id = $shop_id;
        $model->code = $code;
        $model->hour = sizeof($times) * 2;
        return $model->save();
    }
    public function actionUnmake(){
        //todo :ds
        $post = $this->getPost();
        $info = OrderMake::findOne($post['id']);
        $info->make_status = 0;
        $info = SysSet::findOne(1);
        return $info->about;
    }
    public function actionComment(){
        $post = $this->getPost();
        $id = $post['id'];
        return $id;
    }
}