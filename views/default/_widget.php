<?php
use yii\helpers\Html;
?>
<div id="cart" class="card mb-4">
    <div class="card-header">
        <i class="bi bi-cart3"></i> <?=Yii::t('app','Cart')?>
    </div>
    <div class="card-body" id="cart-empty">
        <div class="text-center">
            <i class="bi bi-cart3 text-muted" style="font-size: 3rem;"></i><br>
            <?=Yii::t('app', 'Your cart is empty')?>
        </div>
    </div>
    <table id="cart-items" class="table table-borderless mb-0" style="display: none;">
        <tbody>
        </tbody>
        <tfoot class="border-top">
            <tr>
                <th><?=Yii::t('app','Subtotal')?></th>
                <td class="text-end">L <span class="subtotal_value">0.00</span></td>
            </tr>
            <tr id="discount-container">
                <th><?=Yii::t('app','Discount')?></th>
                <td class="text-end">L <span class="discount_value">0.00</span></td>
            </tr>
            <tr id="tax-container">
                <th><?=Yii::t('app','Taxes')?></th>
                <td class="text-end">L <span class="tax_value">0.00</span></td>
            </tr>
            <tr id="service-fee-container">
                <th><?=Yii::t('app','Service Fee')?></th>
                <td class="text-end">L <span class="service_fee">0.00</span></td>
            </tr>
            <tr>
                <th><?=Yii::t('app','Total')?></th>
                <td class="text-end">L <span class="total_value">0.00</span></td>
            </tr>
            <tr class="bg-light">
                <td colspan="2">
                    <div class="d-grid gap-2">
                        <?=Html::submitButton(Yii::t('app','Checkout').' <i class="bi bi-arrow-right"></i>', [ 'class' => 'btn btn-success btn-lg'])?>
                    </div>
                </td>
            </tr>
        </tfoot>
    </table>

    <?=Html::hiddenInput('service_fee', 0, ['id' => 'service_fee'])?>
    <?=Html::hiddenInput('customer_code', $customer_code)?>
</div>