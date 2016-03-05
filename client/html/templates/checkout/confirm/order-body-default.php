<?php

/**
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.php
 * @copyright Aimeos (aimeos.org), 2015
 */

$enc = $this->encoder();

?>
<?php $this->block()->start( 'checkout/confirm/order' ); ?>
<div class="checkout-confirm-detail common-summary">
	<h2 class="header"><?php echo $enc->html( $this->translate( 'client', 'Order details' ), $enc::TRUST ); ?></h2>
<?php echo $this->get( 'orderBody' ); ?>
</div>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'checkout/confirm/order' ); ?>
