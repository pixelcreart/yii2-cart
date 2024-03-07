<?php
use kartik\form\ActiveForm;
use yii\helpers\Html;

?>
<?php $form = ActiveForm::begin([
    'id' => 'cart-location-form',
    'method' => 'get',
    'action' => ['/cart'],
]); ?>
<div class="input-group">
    <?=Html::activeInput('text', $model, 'location', ['class' => 'form-control', 'placeholder' => Yii::t('app','Enter location')])?>
    <?=Html::submitButton(Yii::t('app','Search'), ['class' => 'btn btn-primary'])?>
</div>
<?php ActiveForm::end(); ?>