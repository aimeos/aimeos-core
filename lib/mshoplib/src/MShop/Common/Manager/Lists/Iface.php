<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\Lists;


/**
 * Interface for all list manager implementations
 *
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
	 * $listManager->aggregate( $listManager->filter(), 'catalog.lists.domain' );
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
	 * search object for no further filtering ($listManager->filter() in this
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
	 * $search = $listManager->filter();
	 *
	 * $search->slice( $start, 2 );
	 * $result = $listManager->aggregate( $search, 'catalog.lists.domain' );
	 *
	 * $start += count( $result );
	 * $search->slice( $start, 2 );
	 * $result = $listManager->aggregate( $search, 'catalog.lists.domain' );
	 *
	 * $start += count( $result );
	 * $search->slice( $start, 2 );
	 * $result = $listManager->aggregate( $search, 'catalog.lists.domain' );
	 * </code>
	 *
	 * The problem is to set the value of $start to the number of returned records.
	 * That's correct for calls to search() but grouping records with
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
	 * $search = $listManager->filter();
	 *
	 * $search->slice( 0, 2 );
	 * $listManager->aggregate( $search, 'catalog.lists.domain' );
	 *
	 * $search->slice( 2, 2 );
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
	 * @param array|string $key Search key or list of keys to aggregate items for
	 * @param string|null $value Search key for aggregating the value column
	 * @param string|null $type Type of the aggregation, empty string for count or "sum" or "avg" (average)
	 * @return \Aimeos\Map List of the search keys as key and the number of counted items as value
	 * @see \Aimeos\MW\Criteria\Iface
	 */
	public function aggregate( \Aimeos\MW\Criteria\Iface $search, $key, string $value = null, string $type = null ) : \Aimeos\Map;
}
