<?php

/**
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.php
 * @copyright Aimeos (aimeos.org), 2015
 */

$enc = $this->encoder();

?>
<div class="checkout-confirm-detail common-summary">
	<h2 class="header"><?php echo $enc->html( $this->translate( 'client/html', 'Order details' ), $enc::TRUST ); ?></h2>
<?php echo $this->get( 'orderBody' ); ?>
</div>
