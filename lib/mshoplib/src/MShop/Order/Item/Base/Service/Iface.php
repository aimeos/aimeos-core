<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item\Base\Service;


/**
 * Interface for order item base service.
 *
 * @package MShop
 * @subpackage Order
 */
interface Iface
	extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\TypeRef\Iface
{
	/**
	 * Returns the associated service item
	 *
	 * @return \Aimeos\MShop\Service\Item\Iface|null Service item
	 */
	public function getServiceItem() : ?\Aimeos\MShop\Service\Item\Iface;

	/**
	 * Sets the site ID of the item.
	 *
	 * @param string $value Unique site ID of the item
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Iface Order base service item for chaining method calls
	 */
	public function setSiteId( string $value ) : \Aimeos\MShop\Order\Item\Base\Service\Iface;

	/**
	 * Returns the order base ID of the order service if available.
	 *
	 * @return string|null Base ID of the item.
	 */
	public function getBaseId() : ?string;

	/**
	 * Sets the order service base ID of the order service item.
	 *
	 * @param string|null $id Order service base ID
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Iface Order base service item for chaining method calls
	 */
	public function setBaseId( ?string $id ) : \Aimeos\MShop\Order\Item\Base\Service\Iface;

	/**
	 * Returns the original ID of the service item used for the order.
	 *
	 * @return string Original service ID
	 */
	public function getServiceId() : string;

	/**
	 * Sets a new ID of the service item used for the order.
	 *
	 * @param string $servid ID of the service item used for the order
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Iface Order base service item for chaining method calls
	 */
	public function setServiceId( string $servid ) : \Aimeos\MShop\Order\Item\Base\Service\Iface;

	/**
	 * Returns the code of the service item.
	 *
	 * @return string Service item code
	 */
	public function getCode() : string;

	/**
	 * Sets a new code for the service item.
	 *
	 * @param string $code Code as defined by the service provider
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Iface Order base service item for chaining method calls
	 */
	public function setCode( string $code ) : \Aimeos\MShop\Order\Item\Base\Service\Iface;

	/**
	 * Returns the location of the media.
	 *
	 * @return string Location of the media
	 */
	public function getMediaUrl() : string;

	/**
	 * Sets the media url of the service item.
	 *
	 * @param string $value Location of the media/picture
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Iface Order base service item for chaining method calls
	 */
	public function setMediaUrl( string $value ) : \Aimeos\MShop\Order\Item\Base\Service\Iface;

	/**
	 * Returns the name of the service item.
	 *
	 * @return string service item name
	 */
	public function getName() : string;

	/**
	 * Sets a new name for the service item.
	 *
	 * @param string $name Service item name
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Iface Order base service item for chaining method calls
	 */
	public function setName( string $name ) : \Aimeos\MShop\Order\Item\Base\Service\Iface;

	/**
	 * Returns the position of the product in the order.
	 *
	 * @return int|null Product position in the order from 0-n
	 */
	public function getPosition() : ?int;

	/**
	 * Sets the position of the product within the list of ordered products.
	 *
	 * @param int|null $value Product position in the order from 0-n or null for resetting the position
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setPosition( ?int $value ) : \Aimeos\MShop\Order\Item\Base\Service\Iface;

	/**
	 * Returns the price object which belongs to the service item.
	 *
	 * @return \Aimeos\MShop\Price\Item\Iface Price item
	 */
	public function getPrice() : \Aimeos\MShop\Price\Item\Iface;

	/**
	 * Sets a new price object for the service item.
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $price Price item
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Iface Order base service item for chaining method calls
	 */
	public function setPrice( \Aimeos\MShop\Price\Item\Iface $price ) : \Aimeos\MShop\Order\Item\Base\Service\Iface;

	/**
	 * Returns the value of the attribute item for the ordered product with the given code.
	 *
	 * @param string $code Code of the product attribute item
	 * @param array|string $type Type or list of types of the product attribute items
	 * @return array|string|null Value of the attribute item for the ordered product and the given code
	 */
	public function getAttribute( string $code, $type = '' );

	/**
	 * Returns the attribute item for the ordered product with the given code.
	 *
	 * @param string $code Code of the product attribute item
	 * @param array|string $type Type or list of types of the product attribute items
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface|null Attribute item for the ordered product and the given code
	 */
	public function getAttributeItem( string $code, $type = '' );

	/**
	 * Returns the list of attribute items for the service.
	 *
	 * @param string|null $type Filters returned attributes by the given type or null for no filtering
	 * @return \Aimeos\Map List of attribute items implementing \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface
	 */
	public function getAttributeItems( string $type = null ) : \Aimeos\Map;

	/**
	 * Adds or replaces the attribute item in the list of service attributes.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface $item Service attribute item
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Iface Order base service item for chaining method calls
	 */
	public function setAttributeItem( \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface $item ) : \Aimeos\MShop\Order\Item\Base\Service\Iface;

	/**
	 * Sets the new list of attribute items for the service.
	 *
	 * @param \Aimeos\Map|\Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface[] $attributes List of order service attribute items
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Iface Order base service item for chaining method calls
	 */
	public function setAttributeItems( iterable $attributes ) : \Aimeos\MShop\Order\Item\Base\Service\Iface;

	/**
	 * Copys all data from a given service item.
	 *
	 * @param \Aimeos\MShop\Service\Item\Iface $service New service item
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Iface Order base service item for chaining method calls
	 */
	public function copyFrom( \Aimeos\MShop\Service\Item\Iface $service ) : \Aimeos\MShop\Order\Item\Base\Service\Iface;
}
