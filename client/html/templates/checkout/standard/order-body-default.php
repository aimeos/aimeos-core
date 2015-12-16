<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

$enc = $this->encoder();

?>
<section class="checkout-standard-order">
	<h1><?php echo $enc->html( $this->translate( 'client', 'order' ), $enc::TRUST ); ?></h1>
<?php echo $this->get( 'orderBody' ); ?>
</section>
