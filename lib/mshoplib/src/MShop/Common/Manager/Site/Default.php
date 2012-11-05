<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Common
 * @version $Id: Default.php 1095 2012-08-06 16:00:11Z doleiynyk $
 */


/**
 * Default site manager implementation
 *
 * @package MShop
 * @subpackage Common
 */

class MShop_Common_Manager_Site_Default
extends MShop_Common_Manager_Abstract
implements MShop_Common_Manager_Site_Interface
{
	private $_prefix;
	private $_context;
	private $_config;
	private $_searchConfig;


	/**
	 * Creates the site manager using the given context object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 * @param array $config Associative list of SQL statements
	 * @param array $searchConfig Associative list of search configuration
	 *
	 * @throws MShop_Common_Exception if no configuration is available
	 */
	public function __construct( MShop_Context_Item_Interface $context, array $config, array $searchConfig )
	{
		$required = array( 'count', 'delete', 'insert', 'newid', 'search', 'update' );
		$isList = array_keys( $config );

		foreach( $required as $key )
		{
			if( !in_array( $key, $isList ) ) {
				throw new MShop_Exception( sprintf( 'Configuration for "%1$s" is missing', $key ) );
			}
		}

		parent::__construct( $context );

		$this->_config = $config;
		$this->_context = $context;
		$this->_searchConfig = $searchConfig;

		if( ( $entry = reset( $searchConfig ) ) === false ) {
			throw new MShop_Exception( 'Search configuration is invalid' );
		}

		if( ( $pos = strrpos( $entry['code'], '.' ) ) == false ) {
			throw new MShop_Exception( sprintf( 'Search configuration for "%1$s" is invalid', $entry['code']) );
		}

		if( ( $this->_prefix = substr( $entry['code'], 0, $pos+1 ) ) === false ) {
			throw new MShop_Exception( sprintf( 'Search configuration for "%1$s" is invalid', $entry['code'] ) );
		}
	}


	/**
	 * Creates new site item object.
	 *
	 * @return MShop_Common_Item_Site_Interface New site item object
	 */
	public function createItem()
	{
		$values = array( 'siteid' => $this->_context->getLocale()->getSiteId() );
		return $this->_createItem( $values );
	}


	/**
	 * Adds or updates a site item object.
	 *
	 * @param MShop_Common_Item_Site_Interface $item Site item object which should be saved
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Common_Item_Site_Interface';
		if( !( $item instanceof $iface ) ) {
			throw new MShop_Exception( sprintf( 'Object does not implement "%1$s"', $iface ) );
		}

		if( $item->isModified() === false ) {
			return;
		}

		$locale = $this->_context->getLocale();
		$dbm = $this->_context->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$id = $item->getId();

			if( $id === null ) {
				$sql = $this->_config['insert'];
			} else {
				$sql = $this->_config['update'];
			}

			$statement = $conn->create( $sql );

			$statement->bind( 1, $locale->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT );
			$statement->bind( 2, $item->getParentId(), MW_DB_Statement_Abstract::PARAM_INT );
			$statement->bind( 3, $item->getValue(), MW_DB_Statement_Abstract::PARAM_INT );
			$statement->bind( 4, date('Y-m-d H:i:s', time())); // mtime
			$statement->bind( 5, $this->_context->getEditor());

			if( $id !== null ) {
				$statement->bind( 6, $id, MW_DB_Statement_Abstract::PARAM_INT );
			} else {
				$statement->bind( 6, date('Y-m-d H:i:s', time()));//ctime
			}

			$result = $statement->execute()->finish();

			if( $fetch === true )
			{
				if( $id === null ) {
					$item->setId( $this->_newId( $conn, $this->_config['newid'] ) );
				} else {
					$item->setId( $id ); // modified false
				}
			}

			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}
	}



	/**
	 * Deletes the site item object specified by its ID.
	 *
	 * @param integer $id Id of the site item object
	 */
	public function deleteItem( $id )
	{
		$dbm = $this->_context->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$stmt = $this->_getCachedStatement($conn, $this->_prefix . 'delete', $this->_config['delete']);
			$stmt->bind( 1, $id, MW_DB_Statement_Abstract::PARAM_INT );
			$result = $stmt->execute()->finish();
			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}
	}


	/**
	 * Returns the site item specified by its ID
	 *
	 * @param integer $id Id of site item object
	 * @return MShop_Common_Item_Site_Interface Site item object
	 */
	public function getItem( $id, array $ref = array() )
	{
		$conf = reset( $this->_searchConfig );
		return parent::_getItem( $conf['code'], $id, $ref );
	}


	/**
	 * Searches for all site items matching the given critera.
	 *
	 * @param MW_Common_Criteria_Interface $search Search object with conditions
	 * @param integer &$total Number of items that are available in total
	 * @return array List of site items implementing MShop_Common_Item_Site_Interface
	 * @throws MShop_Common_Exception if creating items failed
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$dbm = $this->_context->getDatabaseManager();
		$conn = $dbm->acquire();
		$items = array();

		try
		{
			$domain = explode( '.', $this->_prefix);

			if ( ( $topdomain = array_shift($domain) ) === null ) {
				throw new MShop_Exception('No configuration available.');
			}

			$cfgPathSearch = 'mshop/' . $topdomain . '/manager/' . implode( '/', $domain ) . '/default/item/search';
			$cfgPathCount =  'mshop/' . $topdomain . '/manager/' . implode( '/', $domain ) . '/default/item/count';
			$required = array( trim( $this->_prefix, '.' ) );

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total );
			while( ( $row = $results->fetch() ) !== false ) {
				$items[ $row['id'] ] = $this->_createItem( $row );
			}

			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}

		return $items;
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		$list = array();

		foreach( $this->_searchConfig as $key => $fields ) {
			$list[ $key ] = new MW_Common_Criteria_Attribute_Default( $fields );
		}

		if( $withsub === true )
		{
			$path = 'classes/common/manager/site/submanagers';
			$config = $this->_context->getConfig();
			foreach ( $config->get($path, array()) as $domain ) {
				$list = array_merge($list, $this->getSubManager($domain)->getSearchAttributes(true));
			}
		}

		return $list;
	}


	/**
	 * Creates a new manager for site extensions.
	 *
	 * @param string $manager Name of the sub manager site in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->_getSubManager( 'common', 'site/' . $manager, $name );
	}


	/**
	 * Creates new site item object.
	 *
	 * @param array $values Associative list of key/value pairs
	 * @return MShop_Common_Item_Site_Default New site item object
	 */
	protected function _createItem( array $values = array() )
	{
		return new MShop_Common_Item_Site_Default( $this->_prefix, $values );
	}


	protected function _getPrefix()
	{
		return $this->_prefix;
	}

}