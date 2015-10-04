<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Common
 */


/**
 * Common abstract address manager implementation.
 *
 * @package MShop
 * @subpackage Common
 */
abstract class MShop_Common_Manager_Address_Base
	extends MShop_Common_Manager_Base
	implements MShop_Common_Manager_Address_Iface
{
	private $context;
	private $searchConfig;
	private $prefix;


	/**
	 * Initializes a new common address manager object using the given context object.
	 *
	 * @param MShop_Context_Item_Iface $context Context object with required objects
	 *
	 * @throws MShop_Exception if no configuration is available
	 */
	public function __construct( MShop_Context_Item_Iface $context )
	{
		parent::__construct( $context );

		$this->context = $context;
		$this->searchConfig = $this->getSearchConfig();

		if( ( $entry = reset( $this->searchConfig ) ) === false ) {
			throw new MShop_Exception( sprintf( 'Search configuration not available' ) );
		}

		if( ( $pos = strrpos( $entry['code'], '.' ) ) === false ) {
			throw new MShop_Exception( sprintf( 'Search configuration for "%1$s" not available', $entry['code'] ) );
		}

		if( ( $this->prefix = substr( $entry['code'], 0, $pos + 1 ) ) === false ) {
			throw new MShop_Exception( sprintf( 'Search configuration for "%1$s" not available', $entry['code'] ) );
		}
	}


	/**
	 * Instantiates a new common address item object.
	 *
	 * @return MShop_Common_Item_Address_Iface
	 */
	public function createItem()
	{
		$values = array( 'siteid' => $this->context->getLocale()->getSiteId() );
		return $this->createItemBase( $values );
	}


	/**
	 * Removes multiple items specified by ids in the array.
	 *
	 * @param array $ids List of IDs
	 */
	public function deleteItems( array $ids )
	{
		$path = $this->getConfigPath() . '/delete';
		$this->deleteItemsBase( $ids, $this->context->getConfig()->get( $path, $path ) );
	}


	/**
	 * Returns the common address item object specificed by its ID.
	 *
	 * @param integer $id Unique common address ID referencing an existing address
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return MShop_Common_Item_Address_Iface Returns the address item of the given id
	 * @throws MShop_Exception If address search configuration isn't available
	 */
	public function getItem( $id, array $ref = array() )
	{
		if( ( $conf = reset( $this->searchConfig ) ) === false ) {
			throw new MShop_Exception( sprintf( 'Address search configuration not available' ) );
		}

		return $this->getItemBase( $conf['code'], $id, $ref );
	}


	/**
	 * Saves a common address item object.
	 *
	 * @param MShop_Common_Item_Address_Iface $item common address item object
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Iface $item, $fetch = true )
	{
		$iface = 'MShop_Common_Item_Address_Iface';
		if( !( $item instanceof $iface ) ) {
			throw new MShop_Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		$dbm = $this->context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$id = $item->getId();
			$date = date( 'Y-m-d H:i:s' );

			if( $id === null ) {
				$type = 'insert';
			} else {
				$type = 'update';
			}

			$path = $this->getConfigPath() . '/' . $type;

			$sql = $this->context->getConfig()->get( $path, $path );
			$stmt = $this->getCachedStatement( $conn, $this->prefix . $type, $sql );

			$stmt->bind( 1, $this->context->getLocale()->getSiteId(), MW_DB_Statement_Base::PARAM_INT );
			$stmt->bind( 2, $item->getRefId() );
			$stmt->bind( 3, $item->getCompany() );
			$stmt->bind( 4, $item->getVatId() );
			$stmt->bind( 5, $item->getSalutation() );
			$stmt->bind( 6, $item->getTitle() );
			$stmt->bind( 7, $item->getFirstname() );
			$stmt->bind( 8, $item->getLastname() );
			$stmt->bind( 9, $item->getAddress1() );
			$stmt->bind( 10, $item->getAddress2());
			$stmt->bind( 11, $item->getAddress3() );
			$stmt->bind( 12, $item->getPostal() );
			$stmt->bind( 13, $item->getCity() );
			$stmt->bind( 14, $item->getState() );
			$stmt->bind( 15, $item->getCountryId() );
			$stmt->bind( 16, $item->getLanguageId() );
			$stmt->bind( 17, $item->getTelephone() );
			$stmt->bind( 18, $item->getEmail() );
			$stmt->bind( 19, $item->getTelefax() );
			$stmt->bind( 20, $item->getWebsite() );
			$stmt->bind( 21, $item->getFlag(), MW_DB_Statement_Base::PARAM_INT );
			$stmt->bind( 22, $item->getPosition(), MW_DB_Statement_Base::PARAM_INT );
			$stmt->bind( 23, $date ); //mtime
			$stmt->bind( 24, $this->context->getEditor() );

			if( $id !== null ) {
				$stmt->bind( 25, $id, MW_DB_Statement_Base::PARAM_INT );
				$item->setId( $id ); //is not modified anymore
			} else {
				$stmt->bind( 25, $date ); // ctime
			}

			$stmt->execute()->finish();

			if( $id === null && $fetch === true )
			{
				$path = $this->getConfigPath() . '/newid';
				$item->setId( $this->newId( $conn, $this->context->getConfig()->get( $path, $path ) ) );
			}

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}
	}


	/**
	 * Returns the item objects matched by the given search criteria.
	 *
	 * @param MW_Common_Criteria_Iface $search Search criteria object
	 * @param integer &$total Number of items that are available in total
	 * @return array List of items implementing MShop_Common_Item_Address_Iface
	 * @throws MShop_Common_Exception If creating items failed
	 */
	public function searchItems( MW_Common_Criteria_Iface $search, array $ref = array(), &$total = null )
	{
		$dbm = $this->context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );
		$items = array();

		try
		{
			$domain = explode( '.', $this->prefix );

			if( ( $topdomain = array_shift( $domain ) ) === null ) {
				throw new MShop_Exception( 'No configuration available.' );
			}

			$required = array( trim( $this->prefix, '.' ) );
			$level = MShop_Locale_Manager_Base::SITE_ALL;
			$cfgPathSearch = $this->getConfigPath() . '/search';
			$cfgPathCount = $this->getConfigPath() . '/count';

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== false ) {
				$items[$row['id']] = $this->createItemBase( $row );
			}

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return $items;
	}


	/**
	 * Returns a new manager for address extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return MShop_Common_Manager_Iface Manager for different extensions, e.g type, etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->getSubManagerBase( 'common', 'address/' . $manager, $name );
	}


	/**
	 * Returns the config path for retrieving the configuration values.
	 *
	 * @return string Configuration path
	 */
	abstract protected function getConfigPath();


	/**
	 * Returns the search configuration for searching items.
	 *
	 * @return array Associative list of search keys and search definitions
	 */
	abstract protected function getSearchConfig();


	/**
	 * Returns the search key and item prefix
	 *
	 * @return string Search key / item prefix
	 */
	protected function getPrefix()
	{
		return $this->prefix;
	}


	/**
	 * Creates a new address item
	 *
	 * @param array $values List of attributes for address item
	 * @return MShop_Common_Item_Address_Iface New address item
	 */
	protected function createItemBase( array $values = array( ) )
	{
		return new MShop_Common_Item_Address_Standard( $this->prefix, $values );
	}
}