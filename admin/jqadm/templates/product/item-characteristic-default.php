<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */

$enc = $this->encoder();

?>
<div class="product-item-characteristic card panel">
	<div id="product-item-characteristic" class="header card-header collapsed" role="tab" data-toggle="collapse" data-parent="#accordion" data-target="#product-item-characteristic-data" aria-expanded="false" aria-controls="product-item-characteristic-data">
		<?php echo $enc->html( $this->translate( 'admin', 'Characteristics' ) ); ?>
	</div>
	<div id="product-item-characteristic-data" class="item-characteristic card-block panel-collapse collapse" role="tabpanel" aria-labelledby="product-item-characteristic">
<?php echo $this->get( 'characteristicBody' ); ?>
	</div>
</div>
