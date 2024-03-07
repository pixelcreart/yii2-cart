<?php
use yii\helpers\Html;
?>
<div class="card">
    <div class="card-body">
        <div class="col-12 text-center p-2">
            <i class="fas fa-info-circle text-blue fa-3x mb-3"></i>
            <p class="lead">Confirmar pago</p>
            <div class="d-grid gap-2">
                <?=Html::submitButton('Pagar', [ 'id' => 'checkout-submit', 'class' => 'btn btn-success btn-lg' ])?>
            </div>
        </div>
    </div>
</div>