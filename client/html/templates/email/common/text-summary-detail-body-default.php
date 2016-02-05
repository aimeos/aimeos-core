<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

try {
	$products = $this->extOrderBaseItem->getProducts();
} catch( Exception $e ) {
	$products = array();
}

try
{
	$price = $this->extOrderBaseItem->getPrice();
	$priceValue = $price->getValue();
	$priceService = $price->getCosts();
	$priceRebate = $price->getRebate();
	$priceTaxflag = $price->getTaxFlag();
	$priceTaxvalue = $price->getTaxValue();
	$priceCurrency = $this->translate( 'client/currency', $price->getCurrencyId() );
}
catch( Exception $e )
{
	$priceValue = '0.00';
	$priceRebate = '0.00';
	$priceService = '0.00';
	$priceTaxvalue = '0.00';
	$priceTaxflag = true;
	$priceCurrency = '';
}

try
{
	$deliveryPriceItem = $this->extOrderBaseItem->getService( 'delivery' )->getPrice();
	$deliveryPriceService = $deliveryPriceItem->getCosts();
	$deliveryPriceValue = $deliveryPriceItem->getValue();
}
catch( Exception $e )
{
	$deliveryPriceValue = '0.00';
	$deliveryPriceService = '0.00';
}

try
{
	$paymentPriceItem = $this->extOrderBaseItem->getService( 'payment' )->getPrice();
	$paymentPriceService = $paymentPriceItem->getCosts();
	$paymentPriceValue = $paymentPriceItem->getValue();
}
catch( Exception $e )
{
	$paymentPriceValue = '0.00';
	$paymentPriceService = '0.00';
}

/// Price format with price value (%1$s) and currency (%2$s)
$priceFormat = $this->translate( 'client', '%1$s %2$s' );

?>

<?php echo strip_tags( $this->translate( 'client', 'Order details' ) ); ?>:
<?php foreach( $products as $product ) : ?>
<?php	$price = $product->getPrice(); ?>

<?php echo strip_tags( $product->getName() ); ?>

<?php	foreach( $product->getAttributes() as $attribute ) : ?>
<?php		switch( $attribute->getType() ) : case 'hidden': ?>
<?php				if( $attribute->getCode() === 'download' ) : ?>
- <?php 				echo strip_tags( $attribute->getName()); ?>: <?php echo $this->content( $attribute->getValue() ); ?>

<?php				endif; ?>
<?php			break; default: ?>
- <?php 			echo strip_tags( $this->translate( 'client/code', $attribute->getCode() ) ); ?>: <?php echo strip_tags( ( $attribute->getName() != '' ? $attribute->getName() : $attribute->getValue() ) ); ?>

<?php		endswitch; ?>
<?php	endforeach; ?>
<?php echo strip_tags( $this->translate( 'client', 'Quantity' ) ); ?>: <?php echo $product->getQuantity(); ?>

<?php echo strip_tags( $this->translate( 'client', 'Price' ) ); ?>: <?php printf( $priceFormat, $this->number( $price->getValue() ), $priceCurrency ); ?>

<?php echo strip_tags( $this->translate( 'client', 'Sum' ) ); ?>: <?php printf( $priceFormat, $this->number( $price->getValue() * $product->getQuantity() ), $priceCurrency ); ?>

<?php endforeach; ?>

<?php if( ( $serviceValue = $deliveryPriceValue + $paymentPriceValue ) > 0 ) : ?>

<?php	echo strip_tags( $this->translate( 'client', 'Service fees' ) ); ?>: <?php printf( $priceFormat, $this->number( $serviceValue ), $priceCurrency ); ?>


<?php endif; ?>
<?php if( $paymentPriceService > 0 ) : ?>
<?php echo strip_tags( $this->translate( 'client', 'Sub-total' ) ); ?>: <?php printf( $priceFormat, $this->number( $priceValue ), $priceCurrency ); ?>

<?php echo strip_tags( $this->translate( 'client', '+ Shipping' ) ); ?>: <?php printf( $priceFormat, $this->number( $priceService - $paymentPriceService ), $priceCurrency ); ?>

<?php echo strip_tags( $this->translate( 'client', '+ Payment costs' ) ); ?>: <?php printf( $priceFormat, $this->number( $paymentPriceService ), $priceCurrency ); ?>

<?php echo strip_tags( $this->translate( 'client', 'Total' ) ); ?>: <?php printf( $priceFormat, $this->number( $priceValue + $priceService ), $priceCurrency ); ?>

<?php echo strip_tags( $this->translate( 'client', 'Included rebates' ) ); ?>: <?php printf( $priceFormat, $this->number( $priceRebate ), $priceCurrency ); ?>
<?php else : ?>
<?php echo strip_tags( $this->translate( 'client', 'Sub-total' ) ); ?>: <?php printf( $priceFormat, $this->number( $priceValue ), $priceCurrency ); ?>

<?php echo strip_tags( $this->translate( 'client', '+ Shipping' ) ); ?>: <?php printf( $priceFormat, $this->number( $priceService - $paymentPriceService ), $priceCurrency ); ?>

<?php if( $priceTaxflag === true ) : ?>
<?php	echo strip_tags( $this->translate( 'client', 'Total' ) ); ?>: <?php printf( $priceFormat, $this->number( $priceValue + $priceService ), $priceCurrency ); ?>
<?php endif; ?>

<?php foreach( $this->get( 'summaryTaxRates', array() ) as $taxRate => $priceItem ) : $taxValue = $priceItem->getTaxValue(); ?>
<?php	if( $taxRate > '0.00' && $taxValue > '0.00' ) : ?>
<?php		$taxFormat = ( $priceItem->getTaxFlag() ? $this->translate( 'client', 'Incl. %1$s%% VAT' ) : $this->translate( 'client', '+ %1$s%% VAT' ) ); ?>

<?php		echo strip_tags( sprintf( $taxFormat, $this->number( $taxRate ) ) ); ?>: <?php printf( $priceFormat, $this->number( $taxValue ), $priceCurrency ); ?>
<?php	endif; ?>
<?php endforeach; ?>

<?php if( $priceTaxflag === false ) : ?>
<?php	echo strip_tags( $this->translate( 'client', 'Total' ) ); ?>: <?php printf( $priceFormat, $this->number( $priceValue + $priceService + $priceTaxvalue ), $priceCurrency ); ?>
<?php endif; ?>

<?php echo strip_tags( $this->translate( 'client', 'Included rebates' ) ); ?>: <?php printf( $priceFormat, $this->number( $priceRebate ), $priceCurrency ); ?>
<?php endif; ?>

<?php echo $this->get( 'detailBody' ); ?>
