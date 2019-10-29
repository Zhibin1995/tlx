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
use common\models\app\Shop;
use common\models\app\ShopTime;

class ShopController  extends OnAuthController
{
    public $modelClass = '';
    protected $optional = ['login','create-time','get-time'];
    public function actionLogin(){
        $post = $this->getPost();
        $userphone = $post['userphone'];
        $password = $post['password'];
        $model = Shop::find()->where(['userphone' => $userphone,'password' => md5(md5($password))])->asArray()->one();
        if(!$model){
            return ResultDataHelper::api(422, $this->getError($model));
        }
        return $model;
    }
    public function actionCreateTime(){
        $post = $this->getPost();
        $shop_id = $post['shop_id'];
        $data = $post['data'];
        ShopTime::deleteAll(['shop_id' => $shop_id]);
        foreach ($data as $list){
            $data = $list['date'];
            foreach ($list['time'] as $time){
                $start = $data . $time['start'];
                $end = $data . $time['end'];
                $shoptime = new ShopTime();
                $shoptime->shop_id = $shop_id;
                $shoptime->date = strtotime($data);
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
                    'end' => date('H:i',$time->end_time)
                ];
            }
            $res[] = [
                'date' => date('Y-m-d',$date),
                'time' => $time_res
            ];
        }
        return $res;
    }
}