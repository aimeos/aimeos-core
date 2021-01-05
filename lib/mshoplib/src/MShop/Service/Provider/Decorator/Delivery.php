<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


/**
 * Delivery type decorator for service providers
 *
 * @package MShop
 * @subpackage Service
 */
class Delivery
	extends \Aimeos\MShop\Service\Provider\Decorator\Base
	implements \Aimeos\MShop\Service\Provider\Decorator\Iface
{
	private $beConfig = array(
		'delivery.partial' => array(
			'code' => 'delivery.partial',
			'internalcode' => 'delivery.partial',
			'label' => 'Choice of partitial delivery',
			'type' => 'boolean',
			'internaltype' => 'boolean',
			'default' => '0',
			'required' => false,
		),
		'delivery.collective' => array(
			'code' => 'delivery.collective',
			'internalcode' => 'delivery.collective',
			'label' => 'Choice of collective delivery',
			'type' => 'boolean',
			'internaltype' => 'boolean',
			'default' => '0',
			'required' => false,
		),
	);

	private $feConfig = array(
		'delivery.type' => array(
			'code' => 'delivery.type',
			'internalcode' => 'type',
			'label' => 'Delivery type',
			'type' => 'list',
			'internaltype' => 'integer',
			'default' => [1 => 'complete delivery'],
			'required' => true
		),
	);


	/**
	 * Initializes a new service provider object using the given context object.
	 *
	 * @param \Aimeos\MShop\Service\Provider\Iface $provider Service provider or decorator
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 * @param \Aimeos\MShop\Service\Item\Iface $serviceItem Service item with configuration for the provider
	 */
	public function __construct( \Aimeos\MShop\Service\Provider\Iface $provider,
		\Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MShop\Service\Item\Iface $serviceItem )
	{
		parent::__construct( $provider, $context, $serviceItem );

		if( $this->getConfigValue( 'delivery.partial', 0 ) ) {
			$this->feConfig['delivery.type']['default'][0] = 'partial delivery';
		}

		if( $this->getConfigValue( 'delivery.collective', 0 ) ) {
			$this->feConfig['delivery.type']['default'][2] = 'collective delivery';
		}
	}


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
			$values = $this->feConfig['delivery.type']['default'];

			$type = \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_DELIVERY;
			$service = $this->getBasketService( $basket, $type, $this->getServiceItem()->getCode() );

			if( ( $value = $service->getAttribute( 'delivery.type', 'delivery' ) ) != '' ) {
				$feconfig['delivery.type']['default'] = $this->sort( $values, (int) $value );
			} else {
				$feconfig['delivery.type']['default'] = $values;
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
		$result += array_merge( $result, $this->checkConfig( $this->feConfig, $attributes ) );

		if( isset( $attributes['delivery.type'] )
			&& !isset( $this->feConfig['delivery.type']['default'][$attributes['delivery.type']] )
		) {
			$result['delivery.type'] = $this->getContext()->getI18n()->dt( 'mshop', 'Invalid delivery type' );
		}

		return $result;
	}


	/**
	 * Sorts the entry with the given key to the first position
	 *
	 * @param array $values Associative list of keys and codes
	 * @param int $value Key that should be at first position
	 * @return array Sorted associative array
	 */
	protected function sort( array $values, int $value ) : array
	{
		if( !isset( $values[$value] ) ) {
			return $values;
		}

		$code = $values[$value];
		unset( $values[$value] );

		return [$value => $code] + $values;
	}
}
