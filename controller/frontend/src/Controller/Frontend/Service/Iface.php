<?php

/**
 * @copyright Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Frontend
 */


namespace Aimeos\Controller\Frontend\Service;


/**
 * Interface for service frontend controllers.
 *
 * @package Controller
 * @subpackage Frontend
 */
interface Iface
	extends \Aimeos\Controller\Frontend\Common\Iface
{
	/**
	 * Returns the service items that are available for the service type and the content of the basket.
	 *
	 * @param string $type Service type, e.g. "delivery" (shipping related) or "payment" (payment related)
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket of the user
	 * @param array $ref List of domains for which the items referenced by the services should be fetched too
	 * @return array List of service items implementing \Aimeos\MShop\Service\Item\Iface with referenced items
	 */
	public function getServices( $type, \Aimeos\MShop\Order\Item\Base\Iface $basket,
		$ref = array( 'media', 'price', 'text' ) );

	/**
	 * Returns the list of attribute definitions which must be used to render the input form where the customer can
	 * enter or chose the required data necessary by the service provider.
	 *
	 * @param string $type Service type, e.g. "delivery" (shipping related) or "payment" (payment related)
	 * @param string $serviceId Identifier of one of the service option returned by getService()
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket object
	 * @return array List of attribute definitions implementing \Aimeos\MW\Criteria\Attribute\Iface
	 */
	public function getServiceAttributes( $type, $serviceId, \Aimeos\MShop\Order\Item\Base\Iface $basket );

	/**
	 * Returns the price of the service.
	 *
	 * @param string $type Service type, e.g. "delivery" (shipping related) or "payment" (payment related)
	 * @param string $serviceId Identifier of one of the service option returned by getService()
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket with products
	 * @return \Aimeos\MShop\Price\Item\Iface Price item
	 * @throws \Aimeos\Controller\Frontend\Service\Exception If no active service provider for this ID is available
	 * @throws \Aimeos\MShop\Exception If service provider isn't available
	 * @throws \Exception If an error occurs
	 */
	public function getServicePrice( $type, $serviceId, \Aimeos\MShop\Order\Item\Base\Iface $basket );

	/**
	 * Returns a list of attributes that are invalid.
	 *
	 * @param string $type Service type, e.g. "delivery" (shipping related) or "payment" (payment related)
	 * @param string $serviceId Identifier of the service option chosen by the customer
	 * @param array $attributes List of key/value pairs with name of the attribute from attribute definition object as
	 * 	key and the string entered by the customer as value
	 * @return array List of key/value pairs of attributes keys and an error message for values that are invalid or
	 * 	missing
	 */
	public function checkServiceAttributes( $type, $serviceId, array $attributes );
}
