<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider\Decorator;

use \Aimeos\MShop\Order\Item\Base\Address;


/**
 * Country-limiting decorator for service providers
 *
 * This decorator interacts with the ServiceUpdate and Autofill basket plugins!
 * If the delivery/payment option isn't available any more, the ServiceUpdate
 * plugin will remove it from the basket and the Autofill plugin will add one
 * of the available options again.
 *
 * @package MShop
 * @subpackage Service
 */
class Country
	extends \Aimeos\MShop\Service\Provider\Decorator\Base
	implements \Aimeos\MShop\Service\Provider\Decorator\Iface
{
	private $beConfig = array(
		'country.billing-include' => array(
			'code' => 'country.billing-include',
			'internalcode' => 'country.billing-include',
			'label' => 'List of countries allowed for the billing address',
			'type' => 'string',
			'internaltype' => 'string',
			'default' => '',
			'required' => false,
		),
		'country.billing-exclude' => array(
			'code' => 'country.billing-exclude',
			'internalcode' => 'country.billing-exclude',
			'label' => 'List of countries not allowed for the billing address',
			'type' => 'string',
			'internaltype' => 'string',
			'default' => '',
			'required' => false,
		),
		'country.delivery-include' => array(
			'code' => 'country.delivery-include',
			'internalcode' => 'country.delivery-include',
			'label' => 'List of countries allowed for the delivery address',
			'type' => 'string',
			'internaltype' => 'string',
			'default' => '',
			'required' => false,
		),
		'country.delivery-exclude' => array(
			'code' => 'country.delivery-exclude',
			'internalcode' => 'country.delivery-exclude',
			'label' => 'List of countries not allowed for the delivery address',
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
	 * Checks if the country code is allowed for the service provider.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket object
	 * @return bool True if payment provider can be used, false if not
	 */
	public function isAvailable( \Aimeos\MShop\Order\Item\Base\Iface $basket ) : bool
	{
		if( ( $addresses = $basket->getAddress( Address\Base::TYPE_DELIVERY ) ) !== [] )
		{
			foreach( $addresses as $address )
			{
				$code = strtoupper( $address->getCountryId() );

				if( $this->checkCountryCode( $code, 'country.delivery-include' ) === false
					|| $this->checkCountryCode( $code, 'country.delivery-exclude' ) === true
				) {
					return false;
				}
			}
		}
		elseif( ( $addresses = $basket->getAddress( Address\Base::TYPE_PAYMENT ) ) !== [] )
		{
			// use billing address if no delivery address is available
			foreach( $addresses as $address )
			{
				$code = strtoupper( $address->getCountryId() );

				if( $this->checkCountryCode( $code, 'country.delivery-include' ) === false
					|| $this->checkCountryCode( $code, 'country.delivery-exclude' ) === true
				) {
					return false;
				}

				if( $this->checkCountryCode( $code, 'country.billing-include' ) === false
					|| $this->checkCountryCode( $code, 'country.billing-exclude' ) === true
				) {
					return false;
				}
			}
		}

		return $this->getProvider()->isAvailable( $basket );
	}


	/**
	 * Checks if the country code is in the list of codes specified by the given key
	 *
	 * @param string $code Two letter ISO country code in upper case
	 * @param string $key Configuration key referring to the country code configuration
	 * @return bool|null True if country code is in the list, false if not, null if no codes are availble
	 */
	protected function checkCountryCode( string $code, string $key ) : ?bool
	{
		if( ( $str = $this->getConfigValue( $key ) ) == null ) {
			return null;
		}

		return in_array( $code, explode( ',', str_replace( ' ', '', strtoupper( $str ) ) ) );
	}
}
