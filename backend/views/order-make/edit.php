<?php

use common\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\app\OrderMake */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Order Make';
$this->params['breadcrumbs'][] = ['label' => 'Order Makes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">基本信息</h3>
            </div>
            <div class="box-body">
                <?php $form = ActiveForm::begin([
                    'fieldConfig' => [
                        'template' => "<div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}\n{hint}\n{error}</div>",
                    ],
                ]); ?>
                <div class="col-sm-12">
                    <?= $form->field($model, 'member_id')->textInput() ?>
                    <?= $form->field($model, 'detail_ids')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'address_id')->textInput() ?>
                    <?= $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'date')->textInput() ?>
                    <?= $form->field($model, 'start')->textInput() ?>
                    <?= $form->field($model, 'end')->textInput() ?>
                    <?= $form->field($model, 'finsh')->textInput() ?>
                    <?= $form->field($model, 'shop_id')->textInput() ?>
                    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'make_status')->textInput() ?>
                    <?= $form->field($model, 'refund_status')->textInput() ?>
                    <?= $form->field($model, 'status')->textInput() ?>
                    <?= $form->field($model, 'sort')->textInput() ?>
                    <?= $form->field($model, 'created_at')->textInput() ?>
                    <?= $form->field($model, 'updated_at')->textInput() ?>
                </div>
                <div class="form-group">
                    <div class="col-sm-12 text-center">
                        <button class="btn btn-primary" type="submit">保存</button>
                        <span class="btn btn-white" onclick="history.go(-1)">返回</span>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
