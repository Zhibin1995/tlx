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
use common\models\app\Tip;

class IndexController extends OnAuthController
{
    public $modelClass = '';
    protected $optional = ['banner', 'category','package','hot','show','about','tip','category-banner','serve-num'];

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
    public function actionCategoryBanner(){
        $info = SysSet::findOne(1);
        return $info->category_bannr;
    }
    public function actionServeNum(){
        $info = SysSet::findOne(1);
        return $info->serve_num;
    }
}