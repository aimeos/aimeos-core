<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

$enc = $this->encoder();

$services = $this->get( 'deliveryServices', array() );
$servicePrices = $this->get( 'deliveryServicePrices', array() );
$serviceAttributes = $this->get( 'deliveryServiceAttributes', array() );

try
{
	$orderService = $this->standardBasket->getService( 'delivery' );
	$orderServiceId = $orderService->getServiceId();
}
catch( Exception $e )
{
	$orderService = null;
	$orderServiceId = null;

	if( ( $service = reset( $services ) ) !== false ) {
		$orderServiceId = $service->getId();
	}
}

$serviceOption = $this->param( 'c_deliveryoption', $orderServiceId );

$deliveryCss = array();
foreach( $this->get( 'deliveryError', array() ) as $name => $msg ) {
	$deliveryCss[$name][] = 'error';
}

/// Price format with price value (%1$s) and currency (%2$s)
$priceFormat = $this->translate( 'client', '%1$s %2$s' );

?>
<section class="checkout-standard-delivery">
	<h1><?php echo $enc->html( $this->translate( 'client', 'delivery' ), $enc::TRUST ); ?></h1>
	<p class="note"><?php echo $enc->html( $this->translate( 'client', 'Please choose your delivery method' ), $enc::TRUST ); ?></p>
<?php foreach( $services as $id => $service ) : ?>
	<div id="c_delivery-<?php echo $enc->attr( $id ); ?>" class="item item-service">
		<label class="description" for="c_deliveryoption-<?php echo $enc->attr( $id ); ?>">
			<input class="option" type="radio" id="c_deliveryoption-<?php echo $enc->attr( $id ); ?>" name="<?php echo $enc->attr( $this->formparam( array( 'c_deliveryoption' ) ) ); ?>" value="<?php echo $enc->attr( $id ); ?>" <?php echo ( $id == $serviceOption ? 'checked="checked"' : '' ); ?> />
<?php	if( isset( $servicePrices[$id] ) ) : ?>
<?php		$currency = $this->translate( 'client/currency', $servicePrices[$id]->getCurrencyId() ); ?>
<?php		if( $servicePrices[$id]->getValue() > 0 ) : /// Service fee value (%1$s) and shipping cost value (%2$s) with currency (%3$s) ?>
			<span class="price-value"><?php echo $enc->html( sprintf( $this->translate( 'client', '%1$s%3$s + %2$s%3$s' ), $this->number( $servicePrices[$id]->getValue() ), $this->number( $servicePrices[$id]->getCosts() ), $currency ) ); ?></span>
<?php		else : ?>
			<span class="price-value"><?php echo $enc->html( sprintf( $priceFormat, $this->number( $servicePrices[$id]->getCosts() ), $currency ) ); ?></span>
<?php		endif; ?>
<?php	endif; ?>
			<div class="icons">
<?php	foreach( $service->getRefItems( 'media', 'default', 'default' ) as $mediaItem ) : ?>
<?php		echo $this->partial( $this->config( 'client/html/common/partials/media', 'common/partials/media-default.php' ), array( 'item' => $mediaItem, 'boxAttributes' => array( 'class' => 'icon' ) ) ); ?>
<?php	endforeach; ?>
			</div>
			<h2><?php echo $enc->html( $service->getName() ); ?></h2>
<?php	foreach( $service->getRefItems( 'text', null, 'default' ) as $textItem ) : ?>
<?php		if( ( $type = $textItem->getType() ) !== 'name' ) : ?>
			<p class="<?php echo $enc->attr( $type ); ?>"><?php echo $enc->html( $textItem->getContent(), $enc::TRUST ); ?></p>
<?php		endif; ?>
<?php	endforeach; ?>
		</label><?php
		if( isset( $serviceAttributes[$id] ) ) :
		?><ul class="form-list">
<?php		foreach( $serviceAttributes[$id] as $key => $attribute ) : ?>
<?php			$value = ( isset( $orderService ) && ( $value = $orderService->getAttribute( $key ) ) !== null ? $value : $attribute->getDefault() ); ?>
			<li class="form-item <?php echo $enc->attr( $key ) . ( isset( $deliveryCss[$key] ) ? ' ' . join( ' ', $deliveryCss[$key] ) : '' ) . ( $attribute->isRequired() ? ' mandatory' : '' ); ?>">
				<label for="delivery-<?php echo $enc->attr( $key ); ?>"><?php echo $enc->html( $this->translate( 'client/code', $key ) ); ?></label>
<?php			switch( $attribute->getType() ) : case 'select':
				?><select id="delivery-<?php echo $enc->attr( $key ); ?>" name="<?php echo $enc->attr( $this->formparam( array( 'c_delivery', $id, $key ) ) ); ?>">
<?php					foreach( (array) $attribute->getDefault() as $option ) : ?>
					<option value="<?php echo $enc->attr( $option ); ?>"><?php $code = $key . ':' . $option; echo $enc->html( $this->translate( 'client/code', $code ) ); ?></option>
<?php					endforeach; ?>
				</select><?php
					break; case 'boolean':
				 ?><input type="checkbox" id="delivery-<?php echo $enc->attr( $key ); ?>" name="<?php echo $enc->attr( $this->formparam( array( 'c_delivery', $id, $key ) ) ); ?>" value="<?php echo $enc->attr( $this->param( 'c_delivery/' . $id . '/' . $key, $value ) ); ?>" /><?php
					break; case 'integer': case 'number':
				 ?><input type="number" id="delivery-<?php echo $enc->attr( $key ); ?>" name="<?php echo $enc->attr( $this->formparam( array( 'c_delivery', $id, $key ) ) ); ?>" value="<?php echo $enc->attr( $this->param( 'c_delivery/' . $id . '/' . $key, $value ) ); ?>" /><?php
					break; case 'date': case 'datetime': case 'time':
				 ?><input type="<?php echo $attribute->getType(); ?>" id="delivery-<?php echo $enc->attr( $key ); ?>" name="<?php echo $enc->attr( $this->formparam( array( 'c_delivery', $id, $key ) ) ); ?>" value="<?php echo $enc->attr( $this->param( 'c_delivery/' . $id . '/' . $key, $value ) ); ?>" /><?php
					break; default:
				 ?><input type="text" id="delivery-<?php echo $enc->attr( $key ); ?>" name="<?php echo $enc->attr( $this->formparam( array( 'c_delivery', $id, $key ) ) ); ?>" value="<?php echo $enc->attr( $this->param( 'c_delivery/' . $id . '/' . $key, $value ) ); ?>" /><?php
				endswitch; ?>
			</li>
<?php		endforeach; ?>
		</ul>
<?php	endif; ?>
	</div>
<?php endforeach; ?>
<?php echo $this->get( 'deliveryBody' ); ?>
	<div class="button-group">
		<a class="standardbutton btn-back" href="<?php echo $enc->attr( $this->get( 'standardUrlBack' ) ); ?>"><?php echo $enc->html( $this->translate( 'client', 'Previous' ), $enc::TRUST ); ?></a>
		<button class="standardbutton btn-action"><?php echo $enc->html( $this->translate( 'client', 'Next' ), $enc::TRUST ); ?></button>
	</div>
</section>
