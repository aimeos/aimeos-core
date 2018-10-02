<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2018
 * @package MShop
 * @subpackage Coupon
 */


namespace Aimeos\MShop\Coupon\Provider\Decorator;


/**
 * Decorator for restricting coupons to suppliers
 *
 * @package MShop
 * @subpackage Coupon
 */
class Supplier
	extends \Aimeos\MShop\Coupon\Provider\Decorator\Base
	implements \Aimeos\MShop\Coupon\Provider\Decorator\Iface
{
	private $beConfig = array(
		'supplier.code' => array(
			'code' => 'supplier.code',
			'internalcode' => 'supplier.code',
			'label' => 'Required code of the supplier',
			'type' => 'string',
			'internaltype' => 'string',
			'default' => '',
			'required' => true,
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
		return $this->checkConfig( $this->beConfig, $attributes );
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing \Aimeos\MW\Common\Critera\Attribute\Iface
	 */
	public function getConfigBE()
	{
		return $this->getConfigItems( $this->beConfig );
	}


	/**
	 * Checks for requirements.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Basic order of the customer
	 * @return boolean True if the requirements are met, false if not
	 */
	public function isAvailable( \Aimeos\MShop\Order\Item\Base\Iface $base )
	{
		$services = $base->getServices();
		$supplier = $this->getConfigValue( 'supplier.code' );

		if( isset( $services[\Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_DELIVERY] ) )
		{
			foreach( $services[\Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_DELIVERY] as $service )
			{
				if( $service->getAttribute( 'supplier.code', 'delivery' ) === $supplier ) {
					return parent::isAvailable( $base );
				}
			}
		}

		return false;
	}
}
