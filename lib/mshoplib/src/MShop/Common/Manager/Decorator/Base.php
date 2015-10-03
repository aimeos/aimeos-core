<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Common
 */


/**
 * Provides common methods for manager decorators.
 *
 * @package MShop
 * @subpackage Common
 */
abstract class MShop_Common_Manager_Decorator_Base
	extends MShop_Common_Manager_Base
	implements MShop_Common_Manager_Decorator_Iface
{
	private $manager;


	/**
	 * Initializes the manager decorator.
	 *
	 * @param MShop_Context_Item_Iface $context Context object with required objects
	 * @param MShop_Common_Manager_Iface $manager Manager object
	 */
	public function __construct( MShop_Context_Item_Iface $context, MShop_Common_Manager_Iface $manager )
	{
		parent::__construct( $context );
		$this->manager = $manager;
	}


	/**
	 * Passes unknown methods to wrapped objects.
	 *
	 * @param string $name Name of the method
	 * @param array $param List of method parameter
	 * @return mixed Returns the value of the called method
	 * @throws MShop_Exception If method call failed
	 */
	public function __call( $name, array $param )
	{
		if( ( $result = call_user_func_array( array( $this->manager, $name ), $param ) ) === false ) {
			throw new MShop_Exception( sprintf( 'Method "%1$s" for provider not available', $name ) );
		}

		return $result;
	}


	/**
	 * Removes old entries from the storage
	 *
	 * @param array $siteids List of IDs for sites Whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		return $this->manager->cleanup( $siteids );
	}


	/**
	 * Creates new item object.
	 *
	 * @return MShop_Common_Item_Iface New item object
	 */
	public function createItem()
	{
		return $this->manager->createItem();
	}


	/**
	 * Creates a search object and optionally sets base criteria.
	 *
	 * @param boolean $default Add default criteria
	 * @return MW_Common_Criteria_Iface Criteria object
	 */
	public function createSearch( $default = false )
	{
		return $this->manager->createSearch( $default );
	}


	/**
	 * Deletes the item specified by its ID.
	 *
	 * @param integer $id ID of the item object
	 */
	public function deleteItem( $id )
	{
		$this->manager->deleteItem( $id );
	}


	/**
	 * Removes multiple items specified by ids in the array.
	 *
	 * @param array $ids List of IDs
	 */
	public function deleteItems( array $ids )
	{
		$this->manager->deleteItems( $ids );
	}


	/**
	 * Returns the item specified by its ID
	 *
	 * @param integer $id Unique ID of the item
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return MShop_Common_Item_Iface Item object
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->manager->getItem( $id, $ref );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing MW_Common_Criteria_Attribute_Iface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		return $this->manager->getSearchAttributes( $withsub );
	}


	/**
	 * Creates a new extension manager in the domain.
	 *
	 * @return MShop_Common_Manager_Iface Manager extending the domain functionality
	 */
	public function getSubManager( $domain, $name = null )
	{
		return $this->manager->getSubManager( $domain, $name );
	}


	/**
	 * Adds or updates an item object.
	 *
	 * @param MShop_Common_Item_Iface $item Item object whose data should be saved
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Iface $item, $fetch = true )
	{
		$this->manager->saveItem( $item, $fetch );
	}


	/**
	 * Searches for all items matching the given critera.
	 *
	 * @param MW_Common_Criteria_Iface $search Criteria object with conditions, sortations, etc.
	 * @param integer &$total Number of items that are available in total
	 * @return array List of items implementing MShop_Common_Item_Iface
	 */
	public function searchItems( MW_Common_Criteria_Iface $search, array $ref = array(), &$total = null )
	{
		return $this->manager->searchItems( $search, $ref, $total );
	}


	/**
	 * Search for all referenced items from the list based on the given critera.
	 *
	 * Only criteria from the list and list type can be used for searching and
	 * sorting, but no criteria from the referenced items.
	 *
	 * @param MW_Common_Criteria_Iface $search Search object with search conditions
	 * @param integer &$total Number of items that are available in total
	 * @return array List of list items implementing MShop_Common_Item_List_Iface
	 * @throws MShop_Exception if creating items failed
	 * @see MW_Common_Criteria_SQL
	 */
	public function searchRefItems( MW_Common_Criteria_Iface $search, array $ref = array(), &$total = null )
	{
		return $this->manager->searchRefItems( $search, $ref, $total );
	}



	/**
	 * Returns the manager object.
	 *
	 * @return MShop_Common_Manager_Iface Manager object
	 */
	protected function getManager()
	{
		return $this->manager;
	}
}