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
use common\models\app\Banner;
use common\models\app\Category;
use common\models\app\Goods;
use common\models\app\Order;
use common\models\app\OrderDetail;
use common\models\app\Package;
use common\models\app\Show;
use common\models\app\SysSet;
use common\models\app\Tip;

class OrderController extends OnAuthController
{
    public $modelClass = '';
    protected $optional = ['list', 'detail','refund','hot','show','about','tip'];

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
            $temp = [];
            $temp['create_at'] = date('Y-m-d H:i:s',$item['created_at']);
            $temp['status'] = $item['pay_status'];
            $temp['id'] = $item['id'];
            $temp['amount'] = $item['amount'];
            $temp['goods'] = $goods;
            $res[] = $temp;
        }
        return $res;
    }
    public function actionCategory(){
        $list = Category::find()->where(['status' => 1])->asArray()->all();
        return $list;
    }
    public function actionPackage(){
        $list = Package::find()->where(['status' => 1])->asArray()->all();
        return $list;
    }
    public function actionHot(){
        $list = Goods::find()->where(['status' => 1,'is_hot' =>1])->asArray()->all();
        foreach ($list as $k => $v){
            $list[$k]['url'] = explode(',',$v['url'])[0];
        }
        return $list;
    }
    public function actionShow(){
        $list = Show::find()->where(['status' => 1])->asArray()->orderBy('look desc')->all();
        return $list;
    }
    public function actionTip(){
        $list = Tip::find()->where(['status' => 1])->asArray()->all();
        return $list;
    }
    public function actionAbout(){
        $info = SysSet::findOne(1);
        return $info->about;
    }
}