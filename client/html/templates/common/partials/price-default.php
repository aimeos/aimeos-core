<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();
$iface = '\Aimeos\MShop\Price\Item\Iface';
$priceItems = $this->get( 'prices', array() );
$prices = array();

if( !is_array( $priceItems ) ) {
	$priceItems = array( $priceItems );
}

foreach( $priceItems as $priceItem )
{
	$qty = $priceItem->getQuantity();
	if( !isset( $prices[$qty] ) || $prices[$qty]->getValue() > $priceItem->getValue() ) {
		$prices[$qty] = $priceItem;
	}
}

$format = array(
	/// Price quantity format with quantity (%1$s)
	'quantity' => $this->translate( 'client', 'from %1$s' ),
	/// Price shipping format with shipping / payment cost value (%1$s) and currency (%2$s)
	'costs' => ( $this->get( 'costsItem', true ) ? $this->translate( 'client', '+ %1$s %2$s/item' ) : $this->translate( 'client', '%1$s %2$s' ) ),
	/// Rebate format with rebate value (%1$s) and currency (%2$s)
	'rebate' => $this->translate( 'client', '%1$s %2$s off' ),
	/// Rebate percent format with rebate percent value (%1$s)
	'rebate%' => $this->translate( 'client', '-%1$s%%' ),
);

/// Tax rate format with tax rate in percent (%1$s)
$withtax = $this->translate( 'client', 'Incl. %1$s%% VAT' );
/// Tax rate format with tax rate in percent (%1$s)
$notax = $this->translate( 'client', '+ %1$s%% VAT' );

$first = true;

?>
<?php foreach( $prices as $priceItem ) : ?>
<?php
	if( !( $priceItem instanceof $iface ) ) {
		throw new \Aimeos\MW\View\Exception( sprintf( 'Object doesn\'t implement "%1$s"', $iface ) );
	}

	$costs = $priceItem->getCosts();
	$rebate = $priceItem->getRebate();
	$key = 'price:' . $priceItem->getType();

	/// Price format with price value (%1$s) and currency (%2$s)
	$format['value'] = $this->translate( 'client/code', $key );
	$currency = $this->translate( 'client/currency', $priceItem->getCurrencyId() );
	$taxformat = ( $priceItem->getTaxFlag() ? $withtax : $notax );
?>
<?php if( $first === true ) : $first = false; ?>
<meta itemprop="price" content="<?php echo $priceItem->getValue(); ?>" />
<?php endif; ?>
<div class="price-item <?php echo $enc->attr( $priceItem->getType() ); ?>" itemprop="priceSpecification" itemscope="" itemtype="http://schema.org/PriceSpecification">
	<meta itemprop="valueAddedTaxIncluded" content="<?php echo ( $priceItem->getTaxFlag() ? 'true' : 'false' ); ?>" />
	<meta itemprop="priceCurrency" content="<?php echo $priceItem->getCurrencyId(); ?>" />
	<meta itemprop="price" content="<?php echo $priceItem->getValue(); ?>" />
	<span class="quantity" itemscope="" itemtype="http://schema.org/QuantitativeValue">
		<meta itemprop="minValue" content="<?php echo $priceItem->getQuantity(); ?>" />
		<?php echo $enc->html( sprintf( $format['quantity'], $priceItem->getQuantity() ), $enc::TRUST ); ?>
	</span>
	<span class="value"><?php echo $enc->html( sprintf( $format['value'], $this->number( $priceItem->getValue() ), $currency ), $enc::TRUST ); ?></span>
<?php if( $rebate > 0 ) : ?>
	<span class="rebate"><?php echo $enc->html( sprintf( $format['rebate'], $this->number( $rebate ), $currency ), $enc::TRUST ); ?></span>
	<span class="rebatepercent"><?php echo $enc->html( sprintf( $format['rebate%'], $this->number( round( $rebate * 100 / ( $priceItem->getValue() + $rebate ) ), 0 ) ), $enc::TRUST ); ?></span>
<?php endif; ?>
<?php if( $costs > 0 ) : ?>
	<span class="costs"><?php echo $enc->html( sprintf( $format['costs'], $this->number( $costs ), $currency ), $enc::TRUST ); ?></span>
<?php endif; ?>
	<span class="taxrate"><?php echo $enc->html( sprintf( $taxformat, $this->number( $priceItem->getTaxrate() ) ), $enc::TRUST ); ?></span>
</div>
<?php endforeach; ?>
