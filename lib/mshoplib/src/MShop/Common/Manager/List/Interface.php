<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Common
 * @version $Id $
 */


/**
 * Default list manager implementation
 * @package MShop
 * @subpackage Common
 */
interface MShop_Common_Manager_List_Interface
	extends MShop_Common_Manager_Interface
{
	/**
	 * Moves an existing list item to a new position.
	 *
	 * The position is in front of the item with the ID given in $ref or at the
	 * end of the list if $ref is null.
	 *
	 * <code>
	 * // item ID list in database: 1, 2, 3, 4
	 * $listManager->moveItem( 2, 4 );
	 * // result: 1, 3, 2, 4
	 *
	 * // item ID list in database: 1, 2, 3, 4
	 * $listManager->moveItem( 2, null );
	 * // result: 1, 3, 4, 2
	 * </code>
	 *
	 * The method updates the position of the record with the given ID in $id
	 * and of all records afterwards. The gap left behind by the moved record
	 * is closed automatically. To retrive the items according to the new
	 * positions, you have to sort them by the '<domain>.list.position' key:
	 *
	 * <code>
	 * $search = $listManager->createSearch();
	 * $search->setSortations( array( $search->sort( '+', 'product.list.position' ) ) );
	 * $result = $listManager->searchItems( $search );
	 * </code>
	 *
	 * @param integer $id Id of the item which should be moved
	 * @param integer|null $ref Id where the given Id should be inserted before (null for appending)
	 */
	public function moveItem( $id, $ref = null );


	/**
	 * Search for all referenced items from the list based on the given critera.
	 *
	 * Only criteria from the list and list type can be used for searching and
	 * sorting, but no criteria from the referenced items.
	 *
	 * <code>
	 * $search = $listManager->createSearch( true );
	 * $expr = array(
	 *     $search->compare( '==', 'product.list.domain', array( 'attribute', 'product' ),
	 *     $search->getConditions(),
	 * );
	 * $search->setConditions( $search->combine( '&&', $expr ) );
	 *
	 * $total = 0;
	 * $result = $listManager->searchRefItems( $search, array( 'text', 'media' ) );
	 * </code>
	 *
	 * The result in the example will be:
	 *
	 * <code>
	 * array(
	 *     'attribute' => array(
	 *         <attribute id 1> => <attribute item with text and media items>,
	 *         ...
	 *     ),
	 *     'product' => array(
	 *         <product id 1> => <product item with text and media items>,
	 *         ...
	 *     ),
	 * )
	 * </code>
	 *
	 * The list of domains used in the compare() method will be the domains that
	 * are available as keys in the result array if at least one active
	 * referenced item is found in that domain. The list of domains in the
	 * searchRefItems() method are the items that will be included in the
	 * referenced items and are available via the $item->getRefItems() method.
	 *
	 * Please be aware that the value in $total will be the total number of list
	 * items. The number of referenced items returned can be lower if multiple
	 * list items in one slice point to the same referenced item. In this case
	 * the referenced item is only returned once in that slice.
	 *
	 * @param MW_Common_Criteria_Interface $search Search object with search conditions
	 * @param array $ref List of domains to fetch referenced items for
	 * @param integer &$total Number of items that are available in total
	 * @return array Associative list of domains as keys and lists with pairs
	 *	of IDs and items implementing MShop_Common_Item_Interface
	 * @throws MShop_Exception If creating items failed
	 * @see MW_Common_Criteria_SQL
	 */
	public function searchRefItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null );
}
