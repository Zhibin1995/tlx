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
use common\models\app\Goodcase;
use common\models\app\Shop;
use common\models\app\ShopTime;

class GoodcaseController  extends OnAuthController
{
    public $modelClass = '';
    protected $optional = ['list','watch'];
    public function actionList(){
        $post = $this->getPost();
        $page = $post['page'] ?? 1;
        $size =$post['size'] ?? 10;
        $offset = ($page - 1 )*$size;
        $key = $post['key'];
        $query = Goodcase::find();
        $query->andWhere(['status' => 1]);
        if($key){
            $query->andWhere(['like','name',$key]);
        }
        $list =$query->offset($offset)->limit($size)->asArray()->all();
        return $list;
    }
    public function actionWatch(){
        $post = $this->getPost();
        $id = $post['id'];
        $model = Goodcase::findOne($id);
        $model->count +=1;
        return $model->save();
    }
}