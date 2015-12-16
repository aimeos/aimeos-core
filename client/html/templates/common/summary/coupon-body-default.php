<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

$enc = $this->encoder();

$coupons = array();
if( isset( $this->summaryBasket ) ) {
	$coupons = $this->summaryBasket->getCoupons();
}

?>
<div class="common-summary-coupon container">
	<h2><?php echo $enc->html( $this->translate( 'client', 'Coupons' ), $enc::TRUST ); ?></h2>
	<div class="header">
<?php if( isset( $this->summaryUrlCoupon ) ) : ?>
		<a class="modify" href="<?php echo $enc->attr( $this->summaryUrlCoupon ); ?>"><?php echo $enc->html( $this->translate( 'client', 'Change' ), $enc::TRUST ); ?></a>
<?php endif; ?>
		<h3><?php echo $enc->html( $this->translate( 'client', 'Coupon codes' ), $enc::TRUST ); ?></h3>
	</div>
	<div class="content">
<?php if( !empty( $coupons ) ) : ?>
		<ul class="attr-list">
<?php	foreach( $coupons as $code => $products ) : ?>
			<li class="attr-item"><?php echo $code; ?></li>
<?php	endforeach; ?>
		</ul>
<?php endif; ?>
	</div>
<?php echo $this->get( 'couponBody' ); ?>
</div>
