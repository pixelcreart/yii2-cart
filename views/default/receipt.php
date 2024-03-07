<?php
$formatter = Yii::$app->formatter;

$this->registerCssFile("@web/css/receipt.css");
$this->registerJs("
	setTimeout(function () { window.print(); }, 500);
	window.onfocus = function () { setTimeout(function () { window.close(); }, 500); }
");
?>
<body>
	<div style="font-size: 10pt;"><?=Yii::$app->session->get('site')['name']?></div>

	<div style="font-size: 10pt;">Aguas de Jucutuma</div>
	<div style="font-size: 11pt;">Recibo de Pago</div>

	<br>

	<div style="float: left; clear: both; text-align: left;">Cliente: <?=current($model->invoices)->subscription->affiliate->name?></div><br>
	<?php foreach(current($model->invoices)->subscription->affiliate->affiliateAddress as $aa) : ?>
		<div style="float: left; clear: both;">Dirección: <?=$aa->address_1?>, <?=$aa->address_2?></div><br>
	<?php endforeach ?>

	<div style="float: left; clear: both;">Código: <?=current($model->invoices)->subscription->customer_code?></div><br>

	<div style="clear: both;"></div><br>

	<div><?=$model->collector->name?></div>
	<?=$formatter->asDatetime($model->created_at)?><br>

	<br>
	
	<?php foreach($model->invoices as $i) : ?>

		<?php foreach($i->invoiceItems as $ii) : ?>
			<div style="float: left;">
				<?=$ii->description?>
			</div>
			<div style="float: right;">
				<?=$formatter->asDecimal($ii->amount,2)?>
			</div>
			<br>
		<?php endforeach ?>

	<?php endforeach ?>

	<br>

	<div style="float: left;">Subtotal:</div> <div style="float: right;"><?=$formatter->asDecimal($model->subtotal_amount,2)?></div><br>
	<div style="float: left;">ISV (15%):</div> <div style="float: right;"><?=$formatter->asDecimal($model->tax_amount,2)?></div><br>
	<div style="float: left;">Total:</div> <div style="float: right;"><?=$formatter->asDecimal($model->amount,2)?></div><br>
	<div style="float: left;">Cargo por Servicio:</div> <div style="float: right;"><?=$formatter->asDecimal($model->service_fee,2)?></div><br>

	<br>
	¡Gracias por su pago!
</body>