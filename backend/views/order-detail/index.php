<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Order Details';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
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
            //'order_id',
            [
                'attribute' => 'good_id',
                'value' => function ($model) {
                    return \common\models\app\Goods::find()->select('name')->where(['id' => $model->good_id])->scalar();
                }
            ],
            'num',
            [
                'attribute' => 'make_status',
                'value' => function ($model) {
                    return $model->getMakeStatus();
                }
            ],
            'make_time:datetime',
            [
                'attribute' => 'is_refund',
                'value' => function ($model) {
                    $arr = [
                        0 => '否',
                        1 => '是'
                    ];
                    return $arr[$model->is_refund];
                }
            ],
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
