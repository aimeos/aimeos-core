<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2024
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Item\TypeRef;


/**
 * Common trait for items containing type items
 *
 * @package MShop
 * @subpackage Common
 */
trait Traits
{
	/**
	 * Returns the item property for the given name
	 *
	 * @param string $name Name of the property
	 * @param mixed $default Default value if property is unknown
	 * @return mixed|null Property value or default value if property is unknown
	 */
	abstract public function get( string $name, $default = null );

	/**
	 * Tests if the code is valid.
	 *
	 * @param string $code New code for an item
	 * @param int $length Number of allowed characters
	 * @return string Item code
	 * @throws \Aimeos\MShop\Exception If the code is invalid
	 */
	abstract protected function checkCode( string $code, int $length = 64 ) : string;


	/**
	 * Returns the type item of the item if available.
	 *
	 * @return \Aimeos\MShop\Common\Item\Type\Iface|null Type item or NULL if not available
	 */
	public function getTypeItem() : ?\Aimeos\MShop\Common\Item\Type\Iface
	{
		return $this->get( '.type' );
	}


	/**
	 * Returns the type code of the item.
	 *
	 * @return string Type code of the item
	 */
	public function getType() : string
	{
		return $this->get( $this->prefix() . 'type', '' );
	}


	/**
	 * Sets the new type of the item.
	 *
	 * @param string $type Type of the item
	 * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item for chaining method calls
	 */
	public function setType( string $type ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( $this->prefix() . 'type', $this->checkCode( $type ) );
	}
}
