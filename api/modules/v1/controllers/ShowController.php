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
use common\models\app\ShowDigg;
use Yii;
class ShowController  extends OnAuthController
{
    public $modelClass = '';
    protected $optional = ['list', 'detail','digg','watch'];
    public function actionList(){
        $post = $this->getPost();
        $page = $post['page'] ?? 1;
        $size =$post['size'] ?? 10;
        $offset = ($page - 1 )*$size;
        $list = Show::find()->andWhere(['status' =>1])->offset($offset)->limit($size)->orderBy('look desc')->asArray()->all();
        return $list;
    }
    public function actionDetail(){
        $post = $this->getPost();
        $id = $post['id'];
        $member_id = $post['member_id'] ?? 0;
        $info = Show::find()->asArray()->where(['id' => $id])->one();
        $info['is_digg'] = ShowDigg::findOne(['member_id' => $member_id,'show_id' =>$id]) ? 1 :0;
        return $info;
    }
    public function actionDigg(){
        $post = $this->getPost();
        $id = $post['id'];
        $member_id = $post['member_id'];
        $type = $post['type'];
        $model = Show::findOne($id);
        if($type == 1){
            $count = ShowDigg::findOne(['member_id' => $member_id,'show_id' =>$id]);
            if(!$count){
                $model->digg +=1;

            }
        }else{
            $count = ShowDigg::deleteAll(['member_id' => $member_id,'show_id' =>$id]);
            if($count){
                $model->digg -=1;
            }
        }
        return $model->save();
    }
    public function actionWatch(){
        $post = $this->getPost();
        $id = $post['id'];
        $model = Show::findOne($id);
        $model->look +=1;
        return $model->save();
    }
}