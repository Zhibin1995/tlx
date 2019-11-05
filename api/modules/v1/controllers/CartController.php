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
use common\models\app\Goods;

class CartController  extends OnAuthController
{
    public $modelClass = 'Cart';
    protected $optional = ['list', 'detail','create','edit','delete','default','set-default','province'];
    public function actionList(){
        $post = $this->getPost();
        $member_id = $post['member_id'];
        $list = Cart::find()->where(['member_id' => $member_id,'status' => 1])->all();
        $res = [];
        foreach ($list as $value){
            $goods = Goods::find()->where(['id' => $value->good_id])->asArray()->one();
            $goods['url'] = explode(',',$goods['url'])[0];
            $goods['num'] = $value['num'];
            $goods['cart_id'] = $value['id'];
            $res[] =$goods;
        }
        return $res;
    }
    public function actionCreate(){
        $post = $this->getPost();
        $cart = Cart::find()->where(['member_id' => $post['member_id'],'good_id' => $post['good_id']])->one();
        if(!$cart){
            $cart = new Cart();
            $cart->member_id = $post['member_id'];
            $cart->good_id = $post['good_id'];
            $cart->num = $post['num'];
        }else{
            $cart->num += $post['num'];
        }
        return $cart->save();
    }
    public function actionEdit(){
        $post = $this->getPost();
        $cart = Cart::findOne($post['cart_id']);
        $cart->num = $post['num'];
        return $cart->save();
    }
    public function actionDelete()
    {
        return parent::actionDelete(); // TODO: Change the autogenerated stub
    }
}