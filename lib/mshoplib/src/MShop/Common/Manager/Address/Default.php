<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Common
 * @version $Id: Default.php 14682 2012-01-04 11:30:14Z nsendetzky $
 */


/**
 * Common address manager implementation.
 *
 * @package MShop
 * @subpackage Common
 */
class MShop_Common_Manager_Address_Default
	extends MShop_Common_Manager_Abstract
	implements MShop_Common_Manager_Address_Interface
{
	private $_context;
	private $_config;
	private $_searchConfig;
	private $_prefix;


	/**
	 * Initializes a new common address manager object using the given context object.
	 *
	 * @param MShop_Context_Interface $_context Context object with required objects
	 */
	public function __construct( MShop_Context_Item_Interface $context,
		array $config = array( ), array $searchConfig = array( ) )
	{
		$whitelist = array( 'delete', 'insert', 'update', 'search', 'count', 'newid' );
		$isList = array_keys( $config );
		foreach ( $whitelist as $str ) {
			if ( !in_array($str, $isList) ) {
				throw new MShop_Exception( sprintf( 'No configuration available or missing parts: \"%1$s\"', $str ) );
			}
		}

		$this->_config = $config;

		parent::__construct( $context );

		$this->_context = $context;
		$this->_searchConfig = $searchConfig;

		if ( ( $entry = reset( $searchConfig ) ) === false ) {
			throw new MShop_Exception( sprintf( 'Search configuration is invalid' ) );
		}

		if ( ( $pos = strrpos( $entry['code'], '.' ) ) == false ) {
			throw new MShop_Exception( sprintf( 'An error occured in a manager. Search configuration for "%1$s" is not available.', $entry['code'] ) );
		}

		if ( ( $this->_prefix = substr( $entry['code'], 0, $pos + 1 ) ) === false ) {
			throw new MShop_Exception( sprintf( 'An error occured in a manager. Search configuration for "%1$s" is not available.', $entry['code'] ) );
		}
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

		return $list;
	}


	/**
	 * Instantiates a new common address item object.
	 *
	 * @return MShop_Common_Item_Address_Interface
	 */
	public function createItem()
	{
		$values = array( 'siteid' => $this->_context->getLocale()->getSiteId() );
		return $this->_createItem( $values );
	}


	/**
	 * Deletes a common address item object.
	 *
	 * @param integer $id Unique common address ID referencing an existing address
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
		catch ( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}
	}


	/**
	 * Returns the common address item object specificed by its ID.
	 *
	 * @param integer $id Unique common address ID referencing an existing address
	 */
	public function getItem( $id, array $ref = array() )
	{
		if( ( $conf = reset( $this->_searchConfig ) ) === false ) {
			throw new MShop_Exception( sprintf( 'No address search configuration available' ) );
		}

		return $this->_getItem( $conf['code'], $id, $ref );
	}


	/**
	 * Saves a common address item object.
	 *
	 * @param MShop_Common_Item_Address_Interface $item common address item object
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Common_Item_Address_Interface';
		if( !( $item instanceof $iface ) ) {
			throw new MShop_Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		$config = $this->_context->getConfig();
		$dbm = $this->_context->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$id = $item->getId();

			if ( $id === null ) {
				$sql = $this->_config['insert'];
				$type = 'insert';
			} else {
				$sql = $this->_config['update'];
				$type = 'update';
			}

			$stmt = $this->_getCachedStatement($conn, $this->_prefix . $type, $sql);

			$stmt->bind( 1, $this->_context->getLocale()->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, $item->getRefId(), MW_DB_Statement_Abstract::PARAM_STR ); //reference id
			$stmt->bind( 3, $item->getCompany(), MW_DB_Statement_Abstract::PARAM_STR ); //company
			$stmt->bind( 4, $item->getSalutation(), MW_DB_Statement_Abstract::PARAM_STR ); //salutation
			$stmt->bind( 5, $item->getTitle(), MW_DB_Statement_Abstract::PARAM_STR ); //title
			$stmt->bind( 6, $item->getFirstname(), MW_DB_Statement_Abstract::PARAM_STR ); //firstname
			$stmt->bind( 7, $item->getLastname(), MW_DB_Statement_Abstract::PARAM_STR ); //lastname
			$stmt->bind( 8, $item->getAddress1(), MW_DB_Statement_Abstract::PARAM_STR ); //address1
			$stmt->bind( 9, $item->getAddress2(), MW_DB_Statement_Abstract::PARAM_STR ); //address2
			$stmt->bind( 10, $item->getAddress3(), MW_DB_Statement_Abstract::PARAM_STR ); //address3
			$stmt->bind( 11, $item->getPostal(), MW_DB_Statement_Abstract::PARAM_STR ); //postal
			$stmt->bind( 12, $item->getCity(), MW_DB_Statement_Abstract::PARAM_STR ); //city
			$stmt->bind( 13, $item->getState(), MW_DB_Statement_Abstract::PARAM_STR ); //state
			$stmt->bind( 14, $item->getCountryId(), MW_DB_Statement_Abstract::PARAM_STR ); //countryid
			$stmt->bind( 15, $item->getLanguageId(), MW_DB_Statement_Abstract::PARAM_STR ); //langid
			$stmt->bind( 16, $item->getTelephone(), MW_DB_Statement_Abstract::PARAM_STR ); //telephone
			$stmt->bind( 17, $item->getEmail(), MW_DB_Statement_Abstract::PARAM_STR ); //email
			$stmt->bind( 18, $item->getTelefax(), MW_DB_Statement_Abstract::PARAM_STR ); //telefax
			$stmt->bind( 19, $item->getWebsite(), MW_DB_Statement_Abstract::PARAM_STR ); //website
			$stmt->bind( 20, $item->getFlag(), MW_DB_Statement_Abstract::PARAM_INT ); //generic flag
			$stmt->bind( 21, $item->getPosition(), MW_DB_Statement_Abstract::PARAM_INT ); //position
			$stmt->bind( 22, date('Y-m-d H:i:s', time()));//mtime
			$stmt->bind( 23, $this->_context->getEditor());// editor

			if ( $id !== null ) {
				$stmt->bind( 24, $id, MW_DB_Statement_Abstract::PARAM_INT );
				$item->setId( $id ); //is not modified anymore
			} else {
				$stmt->bind( 24, date('Y-m-d H:i:s', time()));// ctime
			}

			$result = $stmt->execute()->finish();

			if ( $id === null && $fetch === true) {
				$item->setId( $this->_newId( $conn, $this->_config['newid'] ) );
			}

			$dbm->release( $conn );
		}
		catch ( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}
	}


	/**
	 * Returns the item objects matched by the given search criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search criteria object
	 * @param integer &$total Number of items that are available in total
	 * @return array List of items implementing MShop_Common_Item_Address_Interface
	 * @throws MShop_Common_Exception If creating items failed
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

			$level = MShop_Locale_Manager_Abstract::SITE_ALL;
			$cfgPathSearch = 'mshop/'. $topdomain . '/manager/' . implode('/', $domain) . '/default/item/search';
			$cfgPathCount =  'mshop/'. $topdomain . '/manager/' . implode('/', $domain) . '/default/item/count';
			$required = array( trim( $this->_prefix, '.' ) );

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );
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
	 * Returns a new manager for address extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g type, etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->_getSubManager( 'common', 'address/' . $manager, $name );
	}


	/**
	 * Creates a new address item
	 *
	 * @param array $values List of attributes for address item
	 * @return MShop_Common_Item_Address_Interface New address item
	 */
	protected function _createItem( array $values = array( ) )
	{
		return new MShop_Common_Item_Address_Default( $this->_prefix, $values );
	}

}
