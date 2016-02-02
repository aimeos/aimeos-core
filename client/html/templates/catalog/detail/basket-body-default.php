<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

$basketTarget = $this->config( 'client/html/basket/standard/url/target' );
$basketController = $this->config( 'client/html/basket/standard/url/controller', 'basket' );
$basketAction = $this->config( 'client/html/basket/standard/url/action', 'index' );
$basketConfig = $this->config( 'client/html/basket/standard/url/config', array() );

$reqstock = (int) $this->config( 'client/html/basket/standard/require-stock', true );
$enc = $this->encoder();

?>
<div class="catalog-detail-basket" data-reqstock="<?php echo $reqstock; ?>">
<?php if( isset( $this->detailProductItem ) ) : ?>
	<div class="price price-main price-actual price-prodid-<?php echo $this->detailProductItem->getId(); ?>">
<?php	echo $this->partial( $this->config( 'client/html/common/partials/price', 'common/partials/price-default.php' ), array( 'prices' => $this->detailProductItem->getRefItems( 'price', null, 'default' ) ) ); ?>
	</div>
<?php endif; ?>
	<form method="POST" action="<?php echo $enc->attr( $this->url( $basketTarget, $basketController, $basketAction, array(), array(), $basketConfig ) ); ?>">
<!-- catalog.detail.basket.csrf -->
<?php echo $this->csrf()->formfield(); ?>
<!-- catalog.detail.basket.csrf -->
<?php echo $this->get( 'basketBody' ); ?>
		<div class="stock" data-prodid="<?php echo $enc->attr( implode( ' ', $this->get( 'basketProductIds', array() ) ) ); ?>"></div>
		<div class="addbasket">
			<div class="group">
				<input name="<?php echo $enc->attr( $this->formparam( 'b_action' ) ); ?>" type="hidden" value="add" />
				<input name="<?php echo $enc->attr( $this->formparam( array( 'b_prod', 0, 'prodid' ) ) ); ?>" type="hidden" value="<?php echo $enc->attr( $this->detailProductItem->getId() ); ?>" />
				<input name="<?php echo $enc->attr( $this->formparam( array( 'b_prod', 0, 'quantity' ) ) ); ?>" type="number" min="1" max="2147483647" maxlength="10" step="1" required="required" value="1" />
				<button class="standardbutton btn-action" type="submit" value=""><?php echo $enc->html( $this->translate( 'client', 'Add to basket' ), $enc::TRUST ); ?></button>
			</div>
		</div>
	</form>
</div>
