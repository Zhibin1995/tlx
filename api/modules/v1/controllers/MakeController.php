<?php
/**
 * tlx2
 * PhpStorm
 * @author zhibin
 * @date 2019-10-22
 * Class GoodController.php
 */

namespace api\modules\v1\controllers;


use api\controllers\OnAuthController;
use common\helpers\ResultDataHelper;
use common\models\app\Address;
use common\models\app\Goods;
use common\models\app\MakeDetail;
use common\models\app\OrderDetail;
use common\models\app\OrderMake;
use common\models\app\Shop;

class MakeController  extends OnAuthController
{
    public $modelClass = '';
    protected $optional = ['list','finish','detail','agree'];
    public function actionList(){
        $post = $this->getPost();
        $page = $post['page'] ?? 1;
        $size =$post['size'] ?? 10;
        $offset = ($page - 1 )*$size;
        $status = $post['status'];
        $shop_id = $post['shop_id'];
        $date = $post['date'] ?? '';
        $query = OrderMake::find();
        $query->andWhere(['status' => 1]);
        $query->andWhere(['shop_id' => $shop_id]);
        $query->andWhere(['make_status' => $status]);
        if($date){
            $start = strtotime($date.'-01');
            $end = strtotime(date($date.'-1',strtotime('next month')).'-1 day');
            $query->andWhere(['>=','finsh' ,$start]);
            $query->andWhere(['<=','finsh' ,$end]);
        }
        $list =$query->offset($offset)->limit($size)->asArray()->all();
        foreach ($list as $k => $item){
            $order = OrderDetail::find()->where(['in','id',$item['detail_ids']])->all();
            $goods = [];
            foreach ($order as $o_l){
                $good_info = Goods::findOne($o_l->good_id);
                $good = [
                    'name' => $good_info->name,
                    'num' => $o_l->num
                ];
                $goods[] = $good;
            }
            $list[$k]['goods'] = $goods;
            $address = Address::findOne($item['address_id']);
            $list[$k]['username'] = $address->realname;
            $list[$k]['userphone'] = $address->mobile;
            $list[$k]['username'] = $address->address_name.$address->address_details;
        }
        return $list;
    }
    public function actionFinish(){
        $post = $this->getPost();
        $id = $post['id'];
        $code = $post['code'];
        $info = $post['info'];
        $shop_id = $post['shop_id'];

        $order = OrderMake::findOne($id);
        if($code != $order->code){
            return ResultDataHelper::api(422, '服务码不正确');
        }
        foreach ($info as $list){
            $detail_model = new MakeDetail();
            $detail_model->make_id = $order->id;
            $detail_model->category = $list['category'];
            $detail_model->brand = $list['brand'];
            $detail_model->type = $list['type'];
            $detail_model->buy_time = strtotime($list['buy_time']);
            $detail_model->last_time = strtotime($list['last_time']);
            $detail_model->remake = $list['remake'];
            $detail_model->save();
        }
        $order->finsh = time();
        $order->shop_id = $shop_id;
        $order->make_status = 2;
        return $order->save();
    }
    public function actionDetail(){
        $post = $this->getPost();
        $id = $post['id'];
        $info =  OrderMake::find()->where(['id' => $id])->asArray()->one();
        $address = Address::findOne($info['address_id']);
        $custom = [
            'username' => $address->realname,
            'userphone' => $address->mobile,
            'address' => $address->address_name.$address->address_details,
            'maketime' => date('Y-m-d H:i:s', $info['created_at']),
            'remark' => $info['remark']
        ];
        $detail = MakeDetail::find()->where(['make_id' => $id])->asArray()->all() ?? [];
        $make_info = [];
        if($info['make_status'] == 2){
            $shop = Shop::findOne($info['shop_id']);
            $make_info = [
                'finish_time' => date('Y-m-d H:i:s',$info['finsh']),
                'name' => $shop->username,
                'position' => $shop->position,
                'code' => $info['code']
            ];
        }
        $res = [
            'custom' => $custom,
            'detail' => $detail,
            'make_info' => $make_info
        ];
        return $res;
    }
    public function actionAgree(){
        $post = $this->getPost();
        $model = OrderMake::findOne($post['id']);
        $model->make_status = 1;
        return $model->save();
    }
}