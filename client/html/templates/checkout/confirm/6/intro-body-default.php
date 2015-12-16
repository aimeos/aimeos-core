<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

$enc = $this->encoder();

?>
<div class="checkout-confirm-intro">
	<p class="note"><?php echo nl2br( $enc->html( $this->translate( 'client', 'Thank you for your order.
We received your payment and an e-mail with the order details will be sent to you within the next few minutes.' ), $enc::TRUST ) ); ?></p>
<?php echo $this->get( 'introBody' ); ?>
</div>
