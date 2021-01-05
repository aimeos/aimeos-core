<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


/**
 * Date decorator for service providers
 *
 * @package MShop
 * @subpackage Service
 */
class Date
	extends \Aimeos\MShop\Service\Provider\Decorator\Base
	implements \Aimeos\MShop\Service\Provider\Decorator\Iface
{
	private $beConfig = array(
		'date.minimumdays' => array(
			'code' => 'date.minimumdays',
			'internalcode' => 'date.minimumdays',
			'label' => 'Miniumn number of days to wait when selecting dates',
			'type' => 'integer',
			'internaltype' => 'integer',
			'default' => '0',
			'required' => false,
		),
	);

	private $feConfig = array(
		'date.value' => array(
			'code' => 'date.value',
			'internalcode' => 'value',
			'label' => 'Delivery date',
			'type' => 'date',
			'internaltype' => 'date',
			'default' => '',
			'required' => true
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
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the frontend.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket object
	 * @return array List of attribute definitions implementing \Aimeos\MW\Common\Critera\Attribute\Iface
	 */
	public function getConfigFE( \Aimeos\MShop\Order\Item\Base\Iface $basket ) : array
	{
		$feconfig = $this->feConfig;

		try
		{
			$days = $this->getConfigValue( 'date.minimumdays', 0 );
			$type = \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_DELIVERY;
			$service = $this->getBasketService( $basket, $type, $this->getServiceItem()->getCode() );

			if( ( $value = $service->getAttribute( 'date.value', 'delivery' ) ) == '' ) {
				$feconfig['date.value']['default'] = date( 'Y-m-d', time() + 86400 * $days );
			} else {
				$feconfig['date.value']['default'] = $value;
			}
		}
		catch( \Aimeos\MShop\Service\Exception $e ) {} // If service isn't available

		return array_merge( $this->getProvider()->getConfigFE( $basket ), $this->getConfigItems( $feconfig ) );
	}


	/**
	 * Checks the frontend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes entered by the customer during the checkout process
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid resp. null for attributes whose values are OK
	 */
	public function checkConfigFE( array $attributes ) : array
	{
		$result = $this->getProvider()->checkConfigFE( $attributes );
		$result = array_merge( $result, $this->checkConfig( $this->feConfig, $attributes ) );

		if( $result['date.value'] !== null ) {
			return $result;
		}

		$minimum = date( 'Y-m-d', time() + 86400 * $this->getConfigValue( 'date.minimumdays', 0 ) );

		if( $attributes['date.value'] < $minimum ) {
			$result['date.value'] = sprintf( 'Date value before "%1$s"', $minimum );
		}

		return $result;
	}
}
