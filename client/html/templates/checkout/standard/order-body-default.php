<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();

?>
<?php $this->block()->start( 'checkout/standard/order' ); ?>
<section class="checkout-standard-order">
	<h1><?php echo $enc->html( $this->translate( 'client', 'order' ), $enc::TRUST ); ?></h1>
<?php echo $this->get( 'orderBody' ); ?>
</section>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'checkout/standard/order' ); ?>
