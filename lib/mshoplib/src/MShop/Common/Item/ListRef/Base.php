<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Item\ListRef;


/**
 * Abstract class for items containing referenced list items.
 *
 * @package MShop
 * @subpackage Common
 * @deprecated Use ListRef trait directly
 */
abstract class Base extends \Aimeos\MShop\Common\Item\Base
{
	use \Aimeos\MShop\Common\Item\ListRef\Traits;


	/**
	 * Initializes the item with the given values.
	 *
	 * @param string $prefix Prefix for the keys returned by toArray()
	 * @param array $values Associative list of key/value pairs of the item properties
	 * @param array $listItems Two dimensional associative list of domain / ID / list items that implement \Aimeos\MShop\Common\Item\Lists\Iface
	 * @param array $refItems Two dimensional associative list of domain / ID / domain items that implement \Aimeos\MShop\Common\Item\Iface
	 */
	public function __construct( $prefix, array $values = [], array $listItems = [], array $refItems = [] )
	{
		parent::__construct( $prefix, $values );

		$this->initListItems( $listItems, $refItems );
	}
}
