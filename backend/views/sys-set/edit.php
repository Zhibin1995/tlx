<?php

use common\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SysSet */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Sys Set';
$this->params['breadcrumbs'][] = ['label' => 'Sys Sets', 'url' => ['index']];
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
                    <?= $form->field($model, 'about')->textarea(['rows' => 6]) ?>
                </div>

                <?= $form->field($model, 'category_banner')->widget(\common\widgets\webuploader\Files::class, [
                    'type' => 'images',
                    'theme' => 'default',
                    'themeConfig' => [],
                    'config' => [
                        // 可设置自己的上传地址, 不设置则默认地址
                        // 'server' => '',
                        'pick' => [
                            'multiple' => false,
                        ],
                    ]
                ]); ?>
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
