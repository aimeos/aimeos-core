<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();
$order = $this->extOrderItem;

/// Payment e-mail intro with order ID (%1$s), order date (%2$s) and payment status (%3%s)
$msg = $this->translate( 'client', 'Thank you for your order %1$s from %2$s.

The order is pending until we receive the final payment. If you\'ve chosen to pay in advance, please transfer the money to our bank account with the order ID %1$s as reference.' );

$key = 'pay:' . $order->getPaymentStatus();
$status = $this->translate( 'client/code', $key );
$format = $this->translate( 'client', 'Y-m-d' );

$string = sprintf( $msg, $order->getId(), date_create( $order->getTimeCreated() )->format( $format ), $status );

?>
<?php $this->block()->start( 'email/payment/html/intro' ); ?>
<p class="email-common-intro content-block">
<?php echo $enc->html( nl2br( $string ), $enc::TRUST ); ?>
<?php echo $this->get( 'introBody' ); ?>
</p>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'email/payment/html/intro' ); ?>
