<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2021
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


/**
 * 'Time decorator for service providers
 *
 * @package MShop
 * @subpackage Service
 */
class Time
	extends \Aimeos\MShop\Service\Provider\Decorator\Base
	implements \Aimeos\MShop\Service\Provider\Decorator\Iface
{
	private $beConfig = array(
		'time.start' => array(
			'code' => 'time.start',
			'internalcode' => 'time.start',
			'label' => 'Earliest delivery time in 24h "HH:MM" format',
			'type' => 'time',
			'internaltype' => 'string',
			'default' => '00:00',
			'required' => false,
		),
		'time.end' => array(
			'code' => 'time.end',
			'internalcode' => 'time.end',
			'label' => 'Latest delivery time in 24h "HH:MM" format',
			'type' => 'time',
			'internaltype' => 'string',
			'default' => '23:59',
			'required' => false,
		),
		'time.weekdays' => array(
			'code' => 'time.weekdays',
			'internalcode' => 'time.weekdays',
			'label' => 'Comma separated week days the start and end time is valid for, i.e. number from 1 (Monday) to 7 (Sunday)',
			'type' => 'string',
			'internaltype' => 'string',
			'default' => '1,2,3,4,5,6,7',
			'required' => false,
		),
	);

	private $feConfig = array(
		'time.hourminute' => array(
			'code' => 'time.hourminute',
			'internalcode' => 'hourminute',
			'label' => 'Delivery time',
			'type' => 'time',
			'internaltype' => 'time',
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
		$minute = date( 'i' );
		$feconfig = $this->feConfig;
		$feconfig['time.hourminute']['default'] = date( 'H:i', time() + ( $minute + 15 - ( $minute % 15 ) ) * 60 );

		try
		{
			$type = \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_DELIVERY;
			$service = $this->getBasketService( $basket, $type, $this->getServiceItem()->getCode() );

			if( ( $value = $service->getAttribute( 'time.hourminute', 'delivery' ) ) != '' ) {
				$feconfig['time.hourminute']['default'] = $value;
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

		if( $result['time.hourminute'] !== null ) {
			return $result;
		}

		$time = \DateTime::createFromFormat( 'H:i', $attributes['time.hourminute'] );
		$days = explode( ',', $this->getConfigValue( 'time.weekdays', '1,2,3,4,5,6,7' ) );

		if( in_array( date( 'N' ), $days, true ) )
		{
			$start = $this->getConfigValue( 'time.start', '00:00' );
			$end = $this->getConfigValue( 'time.end', '23:59' );

			if( $time->getTimeStamp() < \DateTime::createFromFormat( 'H:i', $start )->getTimeStamp() ) {
				$result['time.hourminute'] = sprintf( 'Time value before "%1$s"', $start );
			}

			if( $time->getTimeStamp() > \DateTime::createFromFormat( 'H:i', $end )->getTimeStamp() ) {
				$result['time.hourminute'] = sprintf( 'Time value after "%1$s"', $end );
			}
		}

		return $result;
	}
}
