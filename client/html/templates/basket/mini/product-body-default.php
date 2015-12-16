<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @copyright Joyce Darimont, 2015
 */

$enc = $this->encoder();

try
{
	$products = $this->miniBasket->getProducts();
	$priceItem = $this->miniBasket->getPrice();
	$priceValue = $priceItem->getValue();
	$priceService = $priceItem->getCosts();
	$priceCurrency = $this->translate( 'client/html/currency', $priceItem->getCurrencyId() );
}
catch( Exception $e )
{
	$priceValue = $priceCosts = '0.00';
	$priceCurrency = '';
	$products = array();
}

/// Price format with price value (%1$s) and currency (%2$s)
$priceFormat = $this->translate( 'client', '%1$s %2$s' );

?>
<div class="basket-mini-product">
	<span class="minibutton"><?php echo $enc->html( $this->translate( 'client', '▼' ), $enc::TRUST ); ?></span>
	<div class="basket">
		<table>
			<thead>
				<tr>
					<th class="name"><?php echo $enc->html( $this->translate( 'client', 'Product' ), $enc::TRUST ); ?></th>
					<th class="quantity"><?php echo $enc->html( $this->translate( 'client', 'Qty' ), $enc::TRUST ); ?></th>
					<th class="price"><?php echo $enc->html( $this->translate( 'client', 'Price' ), $enc::TRUST ); ?></th>
				</tr>
			</thead>
			<tbody>
<?php foreach( $products as $product ) : ?>
				<tr class="product">
					<td class="name"><?php echo $enc->html( $product->getName() ) ?></td>
					<td class="quantity"><?php echo $enc->html( $product->getQuantity() ) ?></td>
					<td class="price"><?php echo $enc->html( sprintf( $priceFormat, $this->number( $product->getPrice()->getValue() ), $priceCurrency ) ); ?></td>
				</tr>
<?php endforeach; ?>
			</tbody>
			<tfoot>
				<tr class="delivery">
					<td class="name" colspan="2"><?php echo $enc->html( $this->translate( 'client', 'Shipping' ), $enc::TRUST ); ?></td>
					<td class="price"><?php echo $enc->html( sprintf( $priceFormat, $this->number( $priceService ), $priceCurrency ) ); ?></td>
				</tr>
				<tr class="total">
					<td class="name" colspan="2"><?php echo $enc->html( $this->translate( 'client', 'Total' ), $enc::TRUST ); ?></td>
					<td class="price"><?php echo $enc->html( sprintf( $priceFormat, $this->number( $priceValue + $priceService ), $priceCurrency ) ); ?></td>
				</tr>
			</tfoot>
		</table>
	</div>
<?php echo $this->get( 'productBody' ); ?>
</div>

