<?php

namespace backend\controllers;

use Yii;
use common\models\app\SysSet;
use common\components\Curd;
use common\models\base\SearchModel;
use backend\controllers\BaseController;

/**
* SysSet
*
* Class SysSetController
* @package backend\controllers
*/
class SysSetController extends BaseController
{
    use Curd;

    /**
    * @var SysSet
    */
    public $modelClass = SysSet::class;


    /**
    * 首页
    *
    * @return string
    * @throws \yii\web\NotFoundHttpException
    */
    public function actionIndex()
    {
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
    /**
     * 编辑/创建
     *
     * @return mixed
     */
    public function actionEdit()
    {
        $id = Yii::$app->request->get('id', null);
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            if(!$model->id){
                $model->password = md5(md5($model->password));
            }
            $model->save();
            return $this->render($this->action->id, [
                'model' => $model,
            ]);
        }

        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }
}
