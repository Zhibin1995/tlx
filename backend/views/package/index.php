<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Packages';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools">
                    <?= Html::create(['edit']) ?>
                </div>
            </div>
            <div class="box-body table-responsive">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-hover'],
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'visible' => false,
            ],

            'id',
            'name',
            //'url:url',
            'price',
            //'desc',
            //'sale_num',
            //'status',
            'sort',
            'created_at:datetime',
            //'updated_at',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{package-goods} {edit} {status} {delete}',
                'buttons' => [
                    'package-goods' => function($url, $model, $key){
                        return "<a class=\"btn btn-primary btn-sm\" href=\"/backend/package-goods/index?package_id={$model->id}\">套餐商品</a>";
                    },
                    'edit' => function($url, $model, $key){
                            return Html::edit(['edit', 'id' => $model->id]);
                    },
                   'status' => function($url, $model, $key){
                            return Html::status($model['status']);
                      },
                    'delete' => function($url, $model, $key){
                            return Html::delete(['delete', 'id' => $model->id]);
                    },
                ]
            ]
    ]
    ]); ?>
            </div>
        </div>
    </div>
</div>
