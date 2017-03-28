<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Common
 * @version $Id $
 */


namespace Aimeos\MShop\Common\Manager\Lists;


/**
 * Default list manager implementation
 * @package MShop
 * @subpackage Common
 */
interface Iface
	extends \Aimeos\MShop\Common\Manager\Iface
{
	/**
	 * Counts the number items that are available for the values of the given key.
	 *
	 * The keys of the result array are the values found for the given key (second
	 * parameter). The values of these keys are how often an item for this key is
	 * found, e.g.
	 *
	 * <code>
	 * // records in table:
	 * // first record: domain='text', refid=10
	 * // second record: domain='text', refid=11
	 * // third record: domain='media', refid=11
	 *
	 * $listManager->aggregate( $listManager->createSearch(), 'catalog.lists.domain' );
	 * </code>
	 *
	 * The result in the example will be:
	 *
	 * <code>
	 * array(
	 *     'text' => 2, // refid 10 and 11
	 *     'media' => 1, // refid 11
	 * )
	 * </code>
	 *
	 * The items used for aggregation can be filtered before counting the items by
	 * adding criteria to the first parameter instaed of handing over a default
	 * search object for no further filtering ($listManager->createSearch() in this
	 * case).
	 *
	 * Caution: When using getSlice() to retrieve certain record windows (e.g. from
	 * row number 100 to 200) then the number of records can be less than your slice
	 * size if records are grouped together an the counts are not always 1.
	 * Nevertheless, you have to add the full slice size to the next start.
	 * Otherwise, you will perform significantly more queries because the number
	 * of returned records will iterate logarithmically (log2) towards 0.
	 *
	 * <code>
	 * // records in table:
	 * // first record: domain='text', refid=10
	 * // second record: domain='text', refid=11
	 * // third record: domain='text', refid=12
	 * // fourth record: domain='text', refid=13
	 *
	 * $start = 0;
	 * $search = $listManager->createSearch();
	 *
	 * $search->setSlice( $start, 2 );
	 * $result = $listManager->aggregate( $search, 'catalog.lists.domain' );
	 *
	 * $start += count( $result );
	 * $search->setSlice( $start, 2 );
	 * $result = $listManager->aggregate( $search, 'catalog.lists.domain' );
	 *
	 * $start += count( $result );
	 * $search->setSlice( $start, 2 );
	 * $result = $listManager->aggregate( $search, 'catalog.lists.domain' );
	 * </code>
	 *
	 * The problem is to set the value of $start to the number of returned records.
	 * That's correct for calls to searchItems() but grouping records with
	 * aggregate() is different! The result of the example code would be:
	 *
	 * <code>
	 * array( 'text' => 2 ) // first result set with refid 10 and 11 counted
	 * array( 'text' => 2 ) // first result set with refid 11 and 12 counted
	 * array( 'text' => 2 ) // first result set with refid 12 and 13 counted
	 * </code>
	 *
	 * Caution: You must add a sortation when using slices to get acceptable results!
	 * Even then the results are not guaranteed to be 100% correct because the same
	 * key can be in two slices, e.g.
	 *
	 * <code>
	 * // records in table:
	 * // first record: domain='text', refid=10
	 * // second record: domain='text', refid=11
	 * // third record: domain='text', refid=12
	 *
	 * $search = $listManager->createSearch();
	 *
	 * $search->setSlice( 0, 2 );
	 * $listManager->aggregate( $search, 'catalog.lists.domain' );
	 *
	 * $search->setSlice( 2, 2 );
	 * $listManager->aggregate( $search, 'catalog.lists.domain' );
	 * </code>
	 *
	 * The result of the two iterations will be:
	 *
	 * <code>
	 * array( 'text' => 2 ) // first result set with refid 10 and 11 counted
	 * array( 'text' => 1 ) // second result set with refid 12 counted
	 * </code>
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria
	 * @param string $key Search key to aggregate items for
	 * @return array List of the search keys as key and the number of counted items as value
	 * @see \Aimeos\MW\Criteria\Iface
	 */
	public function aggregate( \Aimeos\MW\Criteria\Iface $search, $key );


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
	 * positions, you have to sort them by the '<domain>.lists.position' key:
	 *
	 * <code>
	 * $search = $listManager->createSearch();
	 * $search->setSortations( array( $search->sort( '+', 'product.lists.position' ) ) );
	 * $result = $listManager->searchItems( $search );
	 * </code>
	 *
	 * @param integer $id Id of the item which should be moved
	 * @param integer|null $ref Id where the given Id should be inserted before (null for appending)
	 * @return void
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
	 *     $search->compare( '==', 'product.lists.domain', array( 'attribute', 'product' ),
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
	 * @param \Aimeos\MW\Criteria\Iface $search Search object with search conditions
	 * @param array $ref List of domains to fetch referenced items for
	 * @param integer &$total Number of items that are available in total
	 * @return array Associative list of domains as keys and lists with pairs
	 *	of IDs and items implementing \Aimeos\MShop\Common\Item\Iface
	 * @see \Aimeos\MW\Criteria\Iface
	 */
	public function searchRefItems( \Aimeos\MW\Criteria\Iface $search, array $ref = [], &$total = null );
}
