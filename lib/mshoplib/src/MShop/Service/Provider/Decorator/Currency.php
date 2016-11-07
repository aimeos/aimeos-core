<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


/**
 * Currency-limiting decorator for service providers.
 *
 * @package MShop
 * @subpackage Service
 */
class Currency
	extends \Aimeos\MShop\Service\Provider\Decorator\Base
	implements \Aimeos\MShop\Service\Provider\Decorator\Iface
{
	private $beConfig = array(
		'currency.include' => array(
			'code' => 'currency.include',
			'internalcode'=> 'currency.include',
			'label'=> 'List of currencies allowed for the service item',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> false,
		),
		'currency.exclude' => array(
			'code' => 'currency.exclude',
			'internalcode'=> 'currency.exclude',
			'label'=> 'List of currencies not allowed for the service item',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> false,
		),
	);


	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid
	 */
	public function checkConfigBE( array $attributes )
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
	public function getConfigBE()
	{
		$list = $this->getProvider()->getConfigBE();

		foreach( $this->beConfig as $key => $config ) {
			$list[$key] = new \Aimeos\MW\Criteria\Attribute\Standard( $config );
		}

		return $list;
	}




	/**
	 * Checks if the country code is allowed for the service provider.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket object
	 * @return boolean True if payment provider can be used, false if not
	 */
	public function isAvailable( \Aimeos\MShop\Order\Item\Base\Iface $basket )
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
	 */
	protected function checkCurrencyCode( $code, $key )
	{
		if( ( $str = $this->getConfigValue( array( $key ) ) ) === null ) {
			return null;
		}

		return in_array( $code, explode( ',', str_replace( ' ', '', strtoupper( $str ) ) ) );
	}
}
