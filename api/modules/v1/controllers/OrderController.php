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
use common\models\app\Comment;
use common\models\app\CommentImg;
use common\models\app\Goods;
use common\models\app\Order;
use common\models\app\OrderDetail;
use common\models\app\OrderMake;
use common\models\app\Package;
use common\models\app\Shop;
use common\models\app\ShopComment;
use common\models\app\ShopTime;

class OrderController extends OnAuthController
{
    public $modelClass = '';
    protected $optional = ['list', 'wait-make','refund','detail','wait-serve','make','unmake','comment','get-time','new-make'];

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
                $good['num'] = $item1->num;
                $good['name'] = $good_info->name;
                $good['desc'] = $good_info->desc;
                $good['price'] = $good_info->price;
                $good['img'] = explode(',',$good_info->url)[0];
                $detail_arr[] = $good;
            }
            $temp =[] ;
            $address = Address::findOne($item->address_id);
            $shop = Shop::findOne($item->shop_id);
            $temp['id'] = $item->id;
            $temp['date'] = $item->date_string;
            $temp['status'] = $item->make_status;
            $temp['order_num'] = date('YmdHis',$item->created_at).$item->id;
            $temp['address'] = $address->address_name.$address->address_details;
            $temp['shop_name'] = $shop->username;
            $temp['shop_phone'] = $shop->userphone;
            $temp['create_time'] = date('Y-m-d H:i:s',$item->created_at);
            $temp['city_id'] = $address->city_id;
            $temp['province_id'] = $address->province_id;
            $temp['area_id'] = $address->area_id;
            $temp['times'] = $item->hour;
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
        $shop_id = $post['shop_id'];
//        $code = rand(100000,999999);
        $code = 000000;
        $model = new OrderMake();
        $model->member_id = $member_id;
        $model->detail_ids = $ids;
        $model->address_id = $address_id;
        $model->remark = $remark;
        $model->date_string = $date_string;
        $model->date = strtotime($year);
        $model->start = strtotime($year.' '.$start.":00");
        $model->end = strtotime($year.' '.$end.":00");
        $model->shop_id = $shop_id;
        $model->code = $code;
        $model->hour = sizeof($times) * 2;
        $model->save(false);
        $times_ids = ShopTime::find()->andWhere(['shop_id' => $shop_id])
            ->andWhere(['date' => $model->date])
            ->andWhere(['>=','start_time',$model->start])
            ->andWhere(['<=','end_time',$model->end])
            ->select('id')->column();
        ShopTime::updateAll(['is_use' => 1],['in','id',$times_ids]);
        OrderDetail::updateAll(['make_status' => 1,'make_id' =>$model->id,'make_time' => time()],['in','id',explode(',',$ids)]);
        return $model->save(false);
    }
    public function actionUnmake(){
        $post = $this->getPost();
        $info = OrderMake::findOne($post['id']);
        OrderDetail::updateAll(['make_status' => 0,'make_id' =>'','make_time' => ''],['in','id',explode(',',$info->detail_ids)]);
        $times_ids = ShopTime::find()->andWhere(['shop_id' => $info->shop_id])
            ->andWhere(['date' => $info->date])
            ->andWhere(['>=','start_time',$info->start])
            ->andWhere(['<=','end_time',$info->end])
            ->select('id')->column();
        ShopTime::updateAll(['is_use' => 0],['in','id',$times_ids]);
        return $info->delete();
    }
    public function actionComment(){
        $post = $this->getPost();
        $id = $post['id'];
        $member_id = $post['member_id'];
        $is_hide = $post['is_hide'] ?? 0;
        $content = $post['content'];
        $img = $post['img'] ?? [];
        $serve = $post['serve'] ?? 0;
        $art = $post['art'] ?? 0;
        $flow = $post['flow'] ?? 0;
        $wear = $post['wear'] ?? 0;
        $detail = OrderDetail::findOne($id);
        $make = OrderMake::findOne($detail->make_id);
        $goodComment = new Comment();
        $goodComment->member_Id = $member_id;
        $goodComment->good_id = $detail->good_id;
        $goodComment->content = $content;
        $goodComment->is_hide = $is_hide;
        $goodComment->save();
        if($img){
            foreach ($img as $item){
                $img_model = new CommentImg();
                $img_model->comment_Id = $goodComment->id;
                $img_model->url = $item;
                $img_model->save();
            }
        }
        $shopComment = new ShopComment();
        $shopComment->shop_id = $make->shop_id;
        $shopComment->serve = $serve;
        $shopComment->art = $art;
        $shopComment->flow = $flow;
        $shopComment->wear = $wear;
        $shopComment->total = $wear+$art+$flow+$wear;
        $shopComment->save();
        return true;
    }
    public function actionGetTime(){
        $post = $this->getPost();
        //$ids = $post['ids'];
        $times = $post['times'];
        $date = $post['date'];
        $area_id = $post["area_id"];
        $city_id = $post['city_id'];
        $province_id = $post['province_id'];
        $shop_id = Shop::find()
            ->andWhere(['area_id' => $area_id])
            ->andWhere(['city_id' => $city_id])
            ->andWhere(['province_id' => $province_id])
            ->select('id')->column();
        $rank = ShopComment::find()->select('shop_id,sum(total) as total')->where(['in','shop_id',$shop_id])->orderBy('total desc')->groupBy('shop_id')->all();
        foreach ($rank as $item){
            $count = ShopTime::find()->where(['shop_id' => $item['shop_id'], 'date' => strtotime($date),'is_use' =>0])->asArray()->all();
            if(sizeof($count) >= $times*2){
                $res = [];
                foreach ($count as $item){
                    $res_arr['start'] = date('H:i',$item['start_time']);
                    $res_arr['end'] = date('H:i',$item['end_time']);
                    $res_arr['status'] = $item['is_use'] ? 2:1;
                    $res[] = $res_arr;
                }
                $ret = [
                    'shop_id' => $item['shop_id'],
                    'time' => $res
                ];
                return $ret;
            }

        }
        return ResultDataHelper::api(201, '暂时没有可预约的师傅');
    }
    public function actionNewMake(){
        $post = $this->getPost();
        $id = $post['id'];
        $date_string = $post['string'];
        $year = $post['year'];
        $start = $post['start'];
        $end = $post['end'];
        $times = $post['times'];
        $shop_id = $post['shop_id'];
        $code = 000000;
        $model = OrderMake::findOne($id);
        $times_ids = ShopTime::find()->andWhere(['shop_id' => $model->shop_id])
            ->andWhere(['date' => $model->date])
            ->andWhere(['>=','start_time',$model->start])
            ->andWhere(['<=','end_time',$model->end])
            ->select('id')->column();
        ShopTime::updateAll(['is_use' => 0],['in','id',$times_ids]);
        $model->date_string = $date_string;
        $model->date = strtotime($year);
        $model->start = strtotime($year.' '.$start.":00");
        $model->end = strtotime($year.' '.$end.":00");
        $model->shop_id = $shop_id;
        $model->code = $code;
        $model->hour = sizeof($times) * 2;
        $times_ids = ShopTime::find()->andWhere(['shop_id' => $model->shop_id])
            ->andWhere(['date' => $model->date])
            ->andWhere(['>=','start_time',$model->start])
            ->andWhere(['<=','end_time',$model->end])
            ->select('id')->column();
        ShopTime::updateAll(['is_use' => 1],['in','id',$times_ids]);
        return $model->save(false);
    }
}
