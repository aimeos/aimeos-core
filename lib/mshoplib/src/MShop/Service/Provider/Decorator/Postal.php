<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


/**
 * Postal-limiting decorator for service providers
 *
 * This decorator interacts with the ServiceUpdate and Autofill basket plugins!
 * If the delivery/payment option isn't available any more, the ServiceUpdate
 * plugin will remove it from the basket and the Autofill plugin will add one
 * of the available options again.
 *
 * @package MShop
 * @subpackage Service
 */
class Postal
	extends \Aimeos\MShop\Service\Provider\Decorator\Base
	implements \Aimeos\MShop\Service\Provider\Decorator\Iface
{
	private $beConfig = array(
		'postal.billing-include' => array(
			'code' => 'postal.billing-include',
			'internalcode' => 'postal.billing-include',
			'label' => 'List of postal codes allowed for the billing address',
			'type' => 'string',
			'internaltype' => 'string',
			'default' => '',
			'required' => false,
		),
		'postal.billing-exclude' => array(
			'code' => 'postal.billing-exclude',
			'internalcode' => 'postal.billing-exclude',
			'label' => 'List of postal codes not allowed for the billing address',
			'type' => 'string',
			'internaltype' => 'string',
			'default' => '',
			'required' => false,
		),
		'postal.delivery-include' => array(
			'code' => 'postal.delivery-include',
			'internalcode' => 'postal.delivery-include',
			'label' => 'List of postal codes allowed for the delivery address',
			'type' => 'string',
			'internaltype' => 'string',
			'default' => '',
			'required' => false,
		),
		'postal.delivery-exclude' => array(
			'code' => 'postal.delivery-exclude',
			'internalcode' => 'postal.delivery-exclude',
			'label' => 'List of postal codes not allowed for the delivery address',
			'type' => 'string',
			'internaltype' => 'string',
			'default' => '',
			'required' => false,
		),
	);


	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid
	 */
	public function checkConfigBE( array $attributes ) : array
	{
		$error = $this->getProvider()->checkConfigBE( $attributes );
		$error += $this->checkConfig( $this->beConfig, $attributes );

		return $error;
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing \Aimeos\MW\Common\Critera\Attribute\Iface
	 */
	public function getConfigBE() : array
	{
		return array_merge( $this->getProvider()->getConfigBE(), $this->getConfigItems( $this->beConfig ) );
	}


	/**
	 * Checks if the postal code is allowed for the service provider.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket object
	 * @return bool True if payment provider can be used, false if not
	 */
	public function isAvailable( \Aimeos\MShop\Order\Item\Base\Iface $basket ) : bool
	{
		$paymentType = \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT;
		$deliveryType = \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY;


		if( ( $addresses = $basket->getAddress( $deliveryType ) ) !== [] )
		{
			foreach( $addresses as $address )
			{
				$code = $address->getPostal();

				if( $this->checkPostalCode( $code, 'postal.delivery-include' ) === false
					|| $this->checkPostalCode( $code, 'postal.delivery-exclude' ) === true
				) {
					return false;
				}
			}
		}
		elseif( ( $addresses = $basket->getAddress( $paymentType ) ) !== [] ) // use billing address if no delivery address is available
		{
			foreach( $addresses as $address )
			{
				$code = $address->getPostal();

				if( $this->checkPostalCode( $code, 'postal.delivery-include' ) === false
					|| $this->checkPostalCode( $code, 'postal.delivery-exclude' ) === true
				) {
					return false;
				}

				if( $this->checkPostalCode( $code, 'postal.billing-include' ) === false
					|| $this->checkPostalCode( $code, 'postal.billing-exclude' ) === true
				) {
					return false;
				}
			}
		}

		return $this->getProvider()->isAvailable( $basket );
	}


	/**
	 * Checks if the postal code is in the list of codes specified by the given key
	 *
	 * @param string $code Postal code
	 * @param string $key Configuration key referring to the postal code configuration
	 * @return bool|null True if postal code is in the list, false if not, null if no codes are availble
	 */
	protected function checkPostalCode( string $code, string $key ) : ?bool
	{
		if( ( $str = $this->getConfigValue( $key ) ) == null ) {
			return null;
		}

		return in_array( $code, explode( ',', str_replace( ' ', '', $str ) ) );
	}
}
