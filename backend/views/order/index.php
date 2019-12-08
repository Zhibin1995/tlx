<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Orders';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools">
                    <?= Html::create(['export'],'导出') ?>
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
            [
                'attribute' => 'member_id',
                'value' => function ($model) {
                    return \common\models\app\Member::find()->select('nickname')->where(['id' => $model->member_id])->scalar();
                }
            ],
            'order_no',
            //'transaction_id',
            [
                'attribute' => 'type',
                'value' => function ($model) {
                    $arr = [
                        1 => '商品',
                        2 => '套餐'
                    ];
                    return $arr[$model->type];
                }
            ],
            [
                'attribute' => 'pay_status',
                'value' => function ($model) {
                    return $model->getPayStatus();
                }
            ],
            //'username',
            //'userphone',
            //'address',
            'num',
            'amount',
            //'remark',
            //'status',
            'created_at:datetime',
            //'updated_at',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{delete}',
                'buttons' => [
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
