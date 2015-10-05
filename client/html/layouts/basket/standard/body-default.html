<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

$basketTarget = $this->config( 'client/html/basket/standard/url/target' );
$basketController = $this->config( 'client/html/basket/standard/url/controller', 'basket' );
$basketAction = $this->config( 'client/html/basket/standard/url/action', 'index' );
$basketConfig = $this->config( 'client/html/basket/standard/url/config', array() );

$checkoutTarget = $this->config( 'client/html/checkout/standard/url/target' );
$checkoutController = $this->config( 'client/html/checkout/standard/url/controller', 'checkout' );
$checkoutAction = $this->config( 'client/html/checkout/standard/url/action', 'index' );
$checkoutConfig = $this->config( 'client/html/checkout/standard/url/config', array() );

$checkout = true;
try {
	$this->standardBasket->check( \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT );
} catch( Exception $e ) {
	$checkout = false;
}

$enc = $this->encoder();

?>
<section class="aimeos basket-standard">
<?php if( isset( $this->standardErrorList ) ) : ?>
	<ul class="error-list">
<?php foreach( (array) $this->standardErrorList as $errmsg ) : ?>
		<li class="error-item"><?php echo $enc->html( $errmsg ); ?></li>
<?php endforeach; ?>
	</ul>
<?php endif; ?>
	<h1><?php echo $enc->html( $this->translate( 'client/html', 'Basket' ), $enc::TRUST ); ?></h1>
	<form method="POST" action="<?php echo $enc->attr( $this->url( $basketTarget, $basketController, $basketAction, array(), array(), $basketConfig ) ); ?>">
<?php echo $this->csrf()->formfield(); ?>
<?php echo $this->get( 'standardBody' ); ?>
		<div class="button-group">
<?php if( isset( $this->standardBackUrl ) ) : ?>
			<a class="standardbutton btn-back" href="<?php echo $enc->attr( $this->standardBackUrl ); ?>"><?php echo $enc->html( $this->translate( 'client/html', 'Back' ), $enc::TRUST ); ?></a>
<?php endif; ?>
			<button class="standardbutton btn-update" type="submit"><?php echo $enc->html( $this->translate( 'client/html', 'Update' ), $enc::TRUST ); ?></button>
<?php if( $checkout === true ) : ?>
			<a class="standardbutton btn-action" href="<?php echo $enc->attr( $this->url( $checkoutTarget, $checkoutController, $checkoutAction, array(), array(), $checkoutConfig ) ); ?>"><?php echo $enc->html( $this->translate( 'client/html', 'Checkout' ), $enc::TRUST ); ?></a>
<?php endif; ?>
		</div>
	</form>
</section>
