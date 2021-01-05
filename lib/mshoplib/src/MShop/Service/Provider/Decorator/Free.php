<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


/**
 * Checks if basket total value is 0.00 and enables/disables option
 *
 * @package MShop
 * @subpackage Service
 */
class Free
	extends \Aimeos\MShop\Service\Provider\Decorator\Base
	implements \Aimeos\MShop\Service\Provider\Decorator\Iface
{
	private $beConfig = [
		'free.show' => [
			'code' => 'free.show',
			'internalcode' => 'free.show',
			'label' => 'Enable or disable option if basket total is 0.00',
			'type' => 'boolean',
			'internaltype' => 'boolean',
			'default' => '',
			'required' => true,
		],
	];


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
	 * Checks if the service provider should be available.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket object
	 * @return bool True if payment provider can be used, false if not
	 */
	public function isAvailable( \Aimeos\MShop\Order\Item\Base\Iface $basket ) : bool
	{
		$value = $basket->getPrice()->getValue();

		if( $value === '0.00' && $this->getConfigValue( 'free.show' ) == false ) {
			return false;
		}

		return $this->getProvider()->isAvailable( $basket );
	}
}
