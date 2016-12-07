<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\ListRef;


/**
 * Common interface for managers working with referenced list items.
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface
	extends \Aimeos\MShop\Common\Manager\Iface
{
	/**
	 * Updates the list items of the given base item for the domain and type
	 *
	 * This method adds new list items if the referenced items are not yet
	 * associated to the base item, removes list items whose referenced items
	 * are not in the given map any more and updates list items that already
	 * exist.
	 *
	 * To update the referenced variant attributes of a selection product:
	 *  $item = $productManager->getItem( '<id>', array( 'attribute' ) );
	 *  $map = array(
	 *      '<attrid-1>' => array(
	 *          'product.list.datestart' => '2000-01-01 00:00:00',
	 *          'product.list.dateend' => '2100-01-01 00:00:00',
	 *          'product.list.config' => array( 'key' => 'value' ),
	 *      ),
	 *      '<attrid-2>' => array(
	 *          'product.list.config' => array( 'test' => 'something' ),
	 *      ),
	 *  );
	 *  $productManager->updateListItems( $item, $map, 'attribute', 'variant' );
	 *
	 * '''Caution:''' Don't forget to retrieve the already existing references
	 * associated to the base item when using ''getItem()'' or ''searchItems()''.
	 * Otherwise, you will get an exception because of duplicate entries!
	 *
	 * @param \Aimeos\MShop\Common\Item\ListRef\Iface $item Item including references to other domain items
	 * @param array $map Associative list of reference ID as key and list of list item properties as key/value pairs
	 * @param string $domain Domain name of the referenced items
	 * @param string $type List type for the referenced items
	 * @return null
	 */
	public function updateListItems( \Aimeos\MShop\Common\Item\ListRef\Iface $item, array $map, $domain, $type );
}
