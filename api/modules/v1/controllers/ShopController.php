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
use common\models\app\OrderMake;
use common\models\app\Shop;
use common\models\app\ShopTime;

class ShopController  extends OnAuthController
{
    public $modelClass = '';
    protected $optional = ['login','create-time','get-time','order'];
    public function actionLogin(){
        $post = $this->getPost();
        $userphone = $post['userphone'];
        $password = $post['password'];
        $model = Shop::find()->where(['userphone' => $userphone,'password' => md5(md5($password))])->asArray()->one();
        if(!$model){
            return ResultDataHelper::api(422, '密码错误');
        }
        return $model;
    }
    public function actionCreateTime(){
        $post = $this->getPost();
        $shop_id = $post['shop_id'];
        $data = $post['data'];
        foreach ($data as $list){
            $date = $list['date'];
            ShopTime::deleteAll(['shop_id' => $shop_id ,'is_use' => 0,'date' => strtotime($date)]);
            foreach ($list['time'] as $time){
                $start = $date . $time['start'];
                $end = $date . $time['end'];
                $shoptime = new ShopTime();
                $shoptime->shop_id = $shop_id;
                $shoptime->date = strtotime($date);
                $shoptime->start_time = strtotime($start);
                $shoptime->end_time = strtotime($end);
                $shoptime->save();
            }
        }
        return true;
    }
    public function actionGetTime(){
        $post = $this->getPost();
        $shop_id = $post['shop_id'];
        $date_arr = ShopTime::find()->select('date')->where(['shop_id' => $shop_id])->column();
        $date_arr = array_unique($date_arr);
        $res = [];
        foreach ($date_arr as $date){
            $time_arr = ShopTime::find()->where(['shop_id' => $shop_id,'date' => $date])->all();
            $time_res = [];
            foreach ($time_arr as $time){
                $time_res []= [
                    'start' => date('H:i',$time->start_time),
                    'end' => date('H:i',$time->end_time),
                    'status' => $time->is_use ? 2 : 1
                ];
            }
            $res[] = [
                'date' => date('Y-m-d',$date),
                'time' => $time_res
            ];
        }
        return $res;
    }
    public function actionOrder(){
        $post = $this->getPost();
        $shop_id = $post['shop_id'];
        $date = $post['date'];
        $start = strtotime($date.'-01');
        $end = strtotime(date('Y-m-01',strtotime('next month')));
        $query = OrderMake::find();
        $query->andWhere(['status' => 1]);
        $query->andWhere(['shop_id' => $shop_id]);
        $query->andWhere(['in','make_status',[2,3]]);
        $query->andWhere(['>=','finsh' ,$start]);
        $query->andWhere(['<=','finsh' ,$end]);
        $month =$query->count();
        $total = OrderMake::find()->andWhere(['status' => 1])->andWhere(['shop_id' => $shop_id])->andWhere(['in','make_status',[2,3]])->count();
        $res = [
            "month_total" => $month,
            'total' => $total
        ];
        return $res;
    }
}