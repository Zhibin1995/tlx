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
use common\models\app\Show;
use Yii;
class ShowController  extends OnAuthController
{
    public $modelClass = '';
    protected $optional = ['list', 'detail'];
    public function actionList(){
        $post = $this->getPost();
        $id = $post['id'];
        $page = $post['page'] ?? 1;
        $size =$post['size'] ?? 10;
        $offset = ($page - 1 )*$size;
        $list = Show::find()->andWhere(['!=','id' ,$id])->andWhere(['status' =>1])->offset($offset)->limit($size)->orderBy('look desc')->asArray()->all();
        return $list;
    }
    public function actionDetail(){
        $post = $this->getPost();
        $id = $post['id'];
        $info = Show::find()->asArray()->where(['id' => $id])->one();
        return $info;
    }
}