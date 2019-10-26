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
use common\models\app\Package;
use common\models\app\Show;
use common\models\app\SysSet;

class IndexController extends OnAuthController
{
    public $modelClass = '';
    protected $optional = ['banner', 'category','package','hot','show','about'];

    public function actionBanner()
    {
        $list = Banner::find()->where(['status' => 1])->asArray()->all();
        return $list;
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
            $list[$k]['img_arr'] = explode(',',$v['url']);
        }
        return $list;
    }
    public function actionShow(){
        $list = Show::find()->where(['status' => 1])->asArray()->orderBy('look desc')->all();
        return $list;
    }
    public function actionAbout(){
        $info = SysSet::findOne(1);
        return $info->about;
    }
}