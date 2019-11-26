<?php

use common\helpers\Html;
use common\helpers\Url;
use common\models\app\Goods;
use common\models\app\OrderDetail;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Order Makes';
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

            //'id',
            [
                'attribute' => 'member_id',
                'value' => function ($model) {
                    return \common\models\app\Member::find()->select('nickname')->where(['id' => $model->member_id])->scalar();
                }
            ],
            [
                    'attribute' => 'detail_ids',
                'label' => '商品',
                'value' => function ($model) {
                    $order = OrderDetail::find()->where(['in','id',explode(',',$model->detail_ids)])->all();
                    $goods = [];
                    foreach ($order as $o_l){
                        $good_info = Goods::findOne($o_l->good_id);
                        $goods[] = $good_info->name;
                    }

                    return implode('<br>', $goods);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'address_id',
                'value' => function ($model) {
                    $address = \common\models\app\Address::findOne($model->address_id);
                    return $address->address_name.$address->address_details;
                }
            ],
            'remark',
            'date_string',
            //'date',
            //'start',
            //'end',
            'finsh:datetime',
            [
                'attribute' => 'shop_id',
                'value' => function ($model) {
                    return \common\models\app\Shop::find()->select('username')->where(['id' => $model->shop_id])->scalar();
                }
            ],
            'code',
            [
                'attribute' => 'make_status',
                'value' => function ($model) {
                    return $model->getMakeStatus();
                }
            ],
            //'refund_status',
            //'hour',
            //'status',
            //'sort',
            'created_at:datetime',
            //'updated_at',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{make-detail} {edit} {status} {delete}',
                'buttons' => [
                        'make-detail' => function($url, $model, $key){
                        return "<a class=\"btn btn-primary btn-sm\" href=\"/backend/make-detail/index?make_id={$model->id}\">服务商品</a>";
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
