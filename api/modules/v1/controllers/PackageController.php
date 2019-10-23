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
use common\models\app\Goods;
use common\models\app\Package;
use common\models\app\PackageGoods;
use Yii;
class PackageController  extends OnAuthController
{
    public $modelClass = '';
    protected $optional = ['list', 'detail'];
    public function actionDetail(){
        $post = $this->getPost();
        $id = $post['id'];
        $info = Package::find()->asArray()->where(['id' => $id])->one();
        $ids = PackageGoods::find()->andWhere(['status' => 1])->andWhere(['package_id' => $id])->select('goods_id')->column();
        $list = Goods::find()->andWhere(['in','id',$ids])->andWhere(['status' => 1])->asArray()->all();
        $res = [
            'info' => $info,
            'list' => $list
        ];
        return $res;
    }
}