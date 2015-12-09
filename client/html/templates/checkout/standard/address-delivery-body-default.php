<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015
 */

$enc = $this->encoder();

/** client/html/common/address/delivery/disable-new
 * Disables the billing address form for a new address
 *
 * Normally, customers are allowed to enter new delivery addresses in the
 * checkout process which are stored in the current order. For registered
 * customers they are also added to the list of delivery addresses in their
 * profile.
 *
 * You can disable the address form for the new delivery address by this setting
 * if it shouldn't be allowed to add another delivery address.
 *
 * @param boolean True to disable the "new delivery address" form, false to allow a new address
 * @since 2014.03
 * @category Developer
 * @category User
 * @see client/html/common/address/billing/disable-new
 */
$disablenew = $this->config( 'client/html/common/address/delivery/disable-new', false );


$target = $this->config( 'client/html/checkout/standard/url/target' );
$controller = $this->config( 'client/html/checkout/standard/url/controller', 'checkout' );
$action = $this->config( 'client/html/checkout/standard/url/action', 'index' );
$config = $this->config( 'client/html/checkout/standard/url/config', array() );

try {
	$addrArray = $this->standardBasket->getAddress( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY )->toArray();
} catch( Exception $e ) {
	$addrArray = array();
}

$deliveryDefault = ( $addrArray === array() ? -1 : 'null' );
$deliveryOption = $this->param( 'ca_deliveryoption', ( isset( $addrArray['order.base.address.addressid'] ) && $addrArray['order.base.address.addressid'] != '' ? $addrArray['order.base.address.addressid'] : $deliveryDefault ) );

$deliverySalutations = $this->get( 'deliverySalutations', array() );
$deliveryCountries = $this->get( 'addressCountries', array() );
$deliveryStates = $this->get( 'addressStates', array() );
$deliveryLanguages = $this->get( 'addressLanguages', array() );

$deliveryCssAll = array();

foreach( $this->get( 'deliveryMandatory', array() ) as $name ) {
	$deliveryCssAll[$name][] = 'mandatory';
}

foreach( $this->get( 'deliveryOptional', array() ) as $name ) {
	$deliveryCssAll[$name][] = 'optional';
}

foreach( $this->get( 'deliveryHidden', array() ) as $name ) {
	$deliveryCssAll[$name][] = 'hidden';
}

?>
<div class="checkout-standard-address-delivery">
	<h2><?php echo $enc->html( $this->translate( 'client/html', 'Delivery address' ), $enc::TRUST ); ?></h2>
	<div class="item-address item-like">
		<div class="header">
			<input type="radio" name="<?php echo $enc->attr( $this->formparam( array( 'ca_deliveryoption' ) ) ); ?>" value="-1" <?php echo ( $deliveryOption == -1 ? 'checked="checked"' : '' ); ?> />
			<div class="values"><span class="value value-like"><?php echo $enc->html( $this->translate( 'client/html', 'like billing address' ), $enc::TRUST ); ?></span></div>
		</div>
	</div>
<?php foreach( $this->get( 'addressDeliveryItems', array() ) as $id => $addr ) : ?>
	<div class="item-address">
		<div class="header">
			<a class="modify minibutton" href="<?php echo $enc->attr( $this->url( $target, $controller, $action, array( 'step' => 'address', 'ca_delivery_delete' => $id ), array(), $config ) ); ?>">X</a>
			<input type="radio" name="<?php echo $enc->attr( $this->formparam( array( 'ca_deliveryoption' ) ) ); ?>" value="<?php echo $enc->attr( $addr->getAddressId() ); ?>" <?php echo ( $deliveryOption == $id ? 'checked="checked"' : '' ); ?> />
			<div class="values">
<?php
		echo preg_replace( "/\n+/m", "<br/>", trim( $enc->html( sprintf(
			/// Address format with company (%1$s), salutation (%2$s), title (%3$s), first name (%4$s), last name (%5$s),
			/// address part one (%6$s, e.g street), address part two (%7$s, e.g house number), address part three (%8$s, e.g additional information),
			/// postal/zip code (%9$s), city (%10$s), state (%11$s), country (%12$s), language (%13$s),
			/// e-mail (%14$s), phone (%15$s), facsimile/telefax (%16$s), web site (%17$s), vatid (%18$s)
			$this->translate( 'client/html', '%1$s
%2$s %3$s %4$s %5$s
%6$s %7$s
%8$s
%9$s %10$s
%11$s
%12$s
%13$s
%14$s
%15$s
%16$s
%17$s
%18$s
'
			),
			$addr->getCompany(),
			( !in_array( $addr->getSalutation(), array( 'company' ) ) ? $this->translate( 'client/html/code', $addr->getSalutation() ) : '' ),
			$addr->getTitle(),
			$addr->getFirstName(),
			$addr->getLastName(),
			$addr->getAddress1(),
			$addr->getAddress2(),
			$addr->getAddress3(),
			$addr->getPostal(),
			$addr->getCity(),
			$addr->getState(),
			$this->translate( 'client/html/country', $addr->getCountryId() ),
			$this->translate( 'client/html/language', $addr->getLanguageId() ),
			$addr->getEmail(),
			$addr->getTelephone(),
			$addr->getTelefax(),
			$addr->getWebsite(),
			$addr->getVatID()
		) ) ) );
?>
			</div>
		</div>
<?php
		$deliveryCss = $deliveryCssAll;
		if( $deliveryOption == $id )
		{
			foreach( $this->get( 'deliveryError', array() ) as $name => $msg ) {
				$deliveryCss[$name][] = 'error';
			}
		}

		$addrValues = $addr->toArray();

		if( !isset( $addrValues['order.base.address.languageid'] ) || $addrValues['order.base.address.languageid'] == '' ) {
			$addrValues['order.base.address.languageid'] = $this->get( 'deliveryLanguage', 'en' );
		}

		$values = array(
			'address' => $addrValues,
			'salutations' => $deliverySalutations,
			'languages' => $deliveryLanguages,
			'countries' => $deliveryCountries,
			'states' => $deliveryStates,
			'type' => 'delivery',
			'css' => $deliveryCss,
			'id' => $id,
		);
?>
<?php	echo $this->partial( $this->config( 'client/html/common/partials/address', 'common/partials/address-default.php' ), $values ); ?>
	</div>
<?php endforeach; ?>
<?php if( $disablenew === false ) : ?>
	<div class="item-address item-new" data-option="<?php echo $enc->attr( $deliveryOption ); ?>">
		<div class="header">
			<input type="radio" name="<?php echo $enc->attr( $this->formparam( array( 'ca_deliveryoption' ) ) ); ?>" value="null" <?php echo ( $deliveryOption == 'null' ? 'checked="checked"' : '' ); ?> />
			<div class="values"><span class="value value-new"><?php echo $enc->html( $this->translate( 'client/html', 'new address' ), $enc::TRUST ); ?></span></div>
		</div>
<?php
		$deliveryCss = $deliveryCssAll;
		if( $deliveryOption == 'null' )
		{
			foreach( $this->get( 'deliveryError', array() ) as $name => $msg ) {
				$deliveryCss[$name][] = 'error';
			}
		}

		$addrValues = array_merge( $addrArray, $this->param( 'ca_delivery', array() ) );

		if( !isset( $addrValues['order.base.address.languageid'] ) || $addrValues['order.base.address.languageid'] == '' ) {
			$addrValues['order.base.address.languageid'] = $this->get( 'deliveryLanguage', 'en' );
		}

		$values = array(
			'address' => $addrValues,
			'salutations' => $deliverySalutations,
			'languages' => $deliveryLanguages,
			'countries' => $deliveryCountries,
			'states' => $deliveryStates,
			'type' => 'delivery',
			'css' => $deliveryCss,
		);
?>
<?php	echo $this->partial( $this->config( 'client/html/common/partials/address', 'common/partials/address-default.php' ), $values ); ?>
	</div>
<?php endif; ?>
<?php echo $this->get( 'deliveryBody' ); ?>
</div>
