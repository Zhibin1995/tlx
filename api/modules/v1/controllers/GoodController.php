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
use common\models\app\Category;
use common\models\app\Goods;
use Yii;
class GoodController  extends OnAuthController
{
    public $modelClass = '';
    protected $optional = ['list', 'detail','comment'];
    public function actionList(){
        $post = $this->getPost();
        $category_id = $post['category_id'];
        $category_info = Category::find()->where(['id' => $category_id])->asArray()->one();
        $query = Goods::find();
        $query->andWhere(['status' => 1]);
        if($category_id){
            $query->andWhere(['category_id' => $category_id]);
        }
        $list =$query->asArray()->all();
        $res = [
            'list' => $list,
            'category' =>$category_info
        ];
        return $res;
    }
    public function actionDetail(){
        $post = $this->getPost();
        $id = $post['id'];
        $info = Goods::find()->asArray()->where(['id' => $id])->one();
        $info['img_arr'] = explode(',',$info['url']);
        return $info;
    }
    public function actionComment(){
        $post = $this->getPost();
        $id = $post['id'];
        return [];
    }

}