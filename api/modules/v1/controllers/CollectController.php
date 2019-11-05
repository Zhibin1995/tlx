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
use common\models\app\Cart;
use common\models\app\Collect;
use common\models\app\Goods;

class CollectController  extends OnAuthController
{
    public $modelClass = 'Cart';
    protected $optional = ['list', 'edit'];
    public function actionList(){
        $post = $this->getPost();
        $page = $post['page'] ?? 1;
        $size =$post['size'] ?? 10;
        $offset = ($page - 1 )*$size;
        $member_id = $post['member_id'];
        $list = Collect::find()->where(['member_id' => $member_id,'status' => 1])->offset($offset)->limit($size)->all();
        $res = [];
        foreach ($list as $value){
            $goods = Goods::find()->where(['id' => $value->good_id])->asArray()->one();
            $goods['url'] = explode(',',$goods['url'])[0];
            $res[] =$goods;
        }
        return $res;
    }
    public function actionEdit(){
        $post = $this->getPost();
        $member_id = $post['member_id'];
        $good_id = $post['good_id'];
        $type = $post['type'];
        if($type){
            $c = new Collect();
            $c->member_id = $member_id;
            $c->good_id = $good_id;
            $c->save();
        }else{
            Collect::deleteAll(['member_id' => $member_id,'good_id' => $good_id]);
        }
        return true;
    }
}