<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Comments';
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

            //'id',
            //'member_Id',
            [
                'attribute' => 'good_id',
                'value' => function ($model) {
                    return \common\models\app\Goods::find()->select('name')->where(['id' => $model->good_id])->scalar();
                }
            ],
            'content',
            'reply',
//            'audit',
            [
                'attribute' => 'is_hide',
                'value' => function ($model) {
                    $arr = [
                        0 => '否',
                        1 => '是'
                    ];
                    return $arr[$model->is_hide];
                }
            ],
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    $arr = [
                        0 => '未审核',
                        1 => '已审核'
                    ];
                    return $arr[$model->status];
                }
            ],
            //'sort',
            'created_at:datetime',
            'updated_at:datetime',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{comment-img} {edit} {status} {delete}',
                'buttons' => [
                    'comment-img' => function($url, $model, $key){
                        return "<a class=\"btn btn-primary btn-sm\" href=\"/backend/comment-img/index?comment_id={$model->id}\">评论图片</a>";
                        },
                'edit' => function($url, $model, $key){
                        return Html::edit(['edit', 'id' => $model->id], '回复');
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
