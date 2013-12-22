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
	 * Creates new common list item object.
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
	 * $result = $searchRefItems( $search, array( 'text', 'media' ) );
	 *
	 * // The result in the example will be:
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
