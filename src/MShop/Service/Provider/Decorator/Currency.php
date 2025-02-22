<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2025
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


/**
 * Currency-limiting decorator for service providers
 *
 * This decorator interacts with the ServiceUpdate and Autofill basket plugins!
 * If the delivery/payment option isn't available any more, the ServiceUpdate
 * plugin will remove it from the basket and the Autofill plugin will add one
 * of the available options again.
 *
 * @package MShop
 * @subpackage Service
 */
class Currency
	extends \Aimeos\MShop\Service\Provider\Decorator\Base
	implements \Aimeos\MShop\Service\Provider\Decorator\Iface
{
	private array $beConfig = array(
		'currency.include' => array(
			'code' => 'currency.include',
			'internalcode' => 'currency.include',
			'label' => 'List of currencies allowed for the service item',
			'default' => '',
			'required' => false,
		),
		'currency.exclude' => array(
			'code' => 'currency.exclude',
			'internalcode' => 'currency.exclude',
			'label' => 'List of currencies not allowed for the service item',
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
	 * @return array List of attribute definitions implementing \Aimeos\Base\Critera\Attribute\Iface
	 */
	public function getConfigBE() : array
	{
		return array_replace( parent::getConfigBE(), $this->getConfigItems( $this->beConfig ) );
	}




	/**
	 * Checks if the country code is allowed for the service provider.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $basket Basket object
	 * @return bool True if payment provider can be used, false if not
	 */
	public function isAvailable( \Aimeos\MShop\Order\Item\Iface $basket ) : bool
	{
		$code = strtoupper( $basket->getPrice()->getCurrencyId() );

		if( $this->checkCurrencyCode( $code, 'currency.include' ) === false
			|| $this->checkCurrencyCode( $code, 'currency.exclude' ) === true
		) {
			return false;
		}

		return $this->getProvider()->isAvailable( $basket );
	}


	/**
	 * Checks if the currency code is in the list of codes specified by the given key
	 *
	 * @param string $code Three letter ISO currency code in upper case
	 * @param string $key Configuration key referring to the currency code configuration
	 * @return bool|null True if currency code is in the list, false if not, null if no codes are availble
	 */
	protected function checkCurrencyCode( string $code, string $key ) : ?bool
	{
		if( ( $str = $this->getConfigValue( array( $key ) ) ) === null ) {
			return null;
		}

		return in_array( $code, explode( ',', str_replace( ' ', '', strtoupper( $str ) ) ) );
	}
}
