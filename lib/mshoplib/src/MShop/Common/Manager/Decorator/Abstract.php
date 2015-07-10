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
abstract class MShop_Common_Manager_Decorator_Abstract
	extends MShop_Common_Manager_Abstract
	implements MShop_Common_Manager_Decorator_Interface
{
	private $_manager;


	/**
	 * Initializes the manager decorator.
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 * @param MShop_Common_Manager_Interface $manager Manager object
	 */
	public function __construct( MShop_Context_Item_Interface $context, MShop_Common_Manager_Interface $manager )
	{
		parent::__construct( $context );
		$this->_manager = $manager;
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
		if ( ( $result = call_user_func_array( array( $this->_manager, $name ), $param ) ) === false ) {
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
		return $this->_manager->cleanup( $siteids );
	}


	/**
	 * Creates new item object.
	 *
	 * @return MShop_Common_Item_Interface New item object
	 */
	public function createItem()
	{
		return $this->_manager->createItem();
	}


	/**
	 * Creates a search object and optionally sets base criteria.
	 *
	 * @param boolean $default Add default criteria
	 * @return MW_Common_Criteria_Interface Criteria object
	 */
	public function createSearch( $default = false )
	{
		return $this->_manager->createSearch( $default );
	}


	/**
	 * Deletes the item specified by its ID.
	 *
	 * @param integer $id ID of the item object
	 */
	public function deleteItem( $id )
	{
		$this->_manager->deleteItem( $id );
	}


	/**
	 * Removes multiple items specified by ids in the array.
	 *
	 * @param array $ids List of IDs
	 */
	public function deleteItems( array $ids )
	{
		$this->_manager->deleteItems( $ids );
	}


	/**
	 * Returns the item specified by its ID
	 *
	 * @param integer $id Unique ID of the item
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return MShop_Common_Item_Interface Item object
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_manager->getItem( $id, $ref );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		return $this->_manager->getSearchAttributes( $withsub );
	}


	/**
	 * Creates a new extension manager in the domain.
	 *
	 * @return MShop_Common_Manager_Interface Manager extending the domain functionality
	 */
	public function getSubManager( $domain, $name = null )
	{
		return $this->_manager->getSubManager( $domain, $name );
	}


	/**
	 * Adds or updates an item object.
	 *
	 * @param MShop_Common_Item_Interface $item Item object whose data should be saved
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$this->_manager->saveItem( $item, $fetch );
	}


	/**
	 * Searches for all items matching the given critera.
	 *
	 * @param MW_Common_Criteria_Interface $search Criteria object with conditions, sortations, etc.
	 * @param integer &$total Number of items that are available in total
	 * @return array List of items implementing MShop_Common_Item_Interface
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		return $this->_manager->searchItems( $search, $ref, $total );
	}


	/**
	 * Search for all referenced items from the list based on the given critera.
	 *
	 * Only criteria from the list and list type can be used for searching and
	 * sorting, but no criteria from the referenced items.
	 *
	 * @param MW_Common_Criteria_Interface $search Search object with search conditions
	 * @param integer &$total Number of items that are available in total
	 * @return array List of list items implementing MShop_Common_Item_List_Interface
	 * @throws MShop_Exception if creating items failed
	 * @see MW_Common_Criteria_SQL
	 */
	public function searchRefItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		return $this->_manager->searchRefItems( $search, $ref, $total );
	}



	/**
	 * Returns the manager object.
	 *
	 * @return MShop_Common_Manager_Interface Manager object
	 */
	protected function _getManager()
	{
		return $this->_manager;
	}
}