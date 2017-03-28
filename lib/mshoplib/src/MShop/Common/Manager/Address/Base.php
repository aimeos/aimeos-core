<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\Address;


/**
 * Common abstract address manager implementation.
 *
 * @package MShop
 * @subpackage Common
 */
abstract class Base
	extends \Aimeos\MShop\Common\Manager\Base
	implements \Aimeos\MShop\Common\Manager\Address\Iface
{
	private $prefix;
	private $searchConfig;


	/**
	 * Initializes a new common address manager object using the given context object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 *
	 * @throws \Aimeos\MShop\Exception if no configuration is available
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context );

		$this->searchConfig = $this->getSearchConfig();

		if( ( $entry = reset( $this->searchConfig ) ) === false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Search configuration not available' ) );
		}

		if( ( $pos = strrpos( $entry['code'], '.' ) ) === false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Search configuration for "%1$s" not available', $entry['code'] ) );
		}

		if( ( $this->prefix = substr( $entry['code'], 0, $pos + 1 ) ) === false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Search configuration for "%1$s" not available', $entry['code'] ) );
		}
	}


	/**
	 * Instantiates a new common address item object.
	 *
	 * @return \Aimeos\MShop\Common\Item\Address\Iface
	 */
	public function createItem()
	{
		$values = array( 'siteid' => $this->getContext()->getLocale()->getSiteId() );
		return $this->createItemBase( $values );
	}


	/**
	 * Removes multiple items specified by ids in the array.
	 *
	 * @param array $ids List of IDs
	 */
	public function deleteItems( array $ids )
	{
		$this->deleteItemsBase( $ids, $this->getConfigPath() . 'delete' );
	}


	/**
	 * Returns the common address item object specificed by its ID.
	 *
	 * @param integer $id Unique common address ID referencing an existing address
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param boolean $default Add default criteria
	 * @return \Aimeos\MShop\Common\Item\Address\Iface Returns the address item of the given id
	 * @throws \Aimeos\MShop\Exception If address search configuration isn't available
	 */
	public function getItem( $id, array $ref = [], $default = false )
	{
		if( ( $conf = reset( $this->searchConfig ) ) === false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Address search configuration not available' ) );
		}

		return $this->getItemBase( $conf['code'], $id, $ref, $default );
	}


	/**
	 * Saves a common address item object.
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $item common address item object
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( \Aimeos\MShop\Common\Item\Iface $item, $fetch = true )
	{
		$iface = '\\Aimeos\\MShop\\Common\\Item\\Address\\Iface';
		if( !( $item instanceof $iface ) ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
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

			$stmt = $this->getCachedStatement( $conn, $this->getConfigPath() . $type );

			$stmt->bind( 1, $context->getLocale()->getSiteId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 2, $item->getParentId() );
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
			$stmt->bind( 21, $item->getLongitude() );
			$stmt->bind( 22, $item->getLatitude() );
			$stmt->bind( 23, $item->getFlag(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 24, $item->getPosition(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 25, $date ); //mtime
			$stmt->bind( 26, $context->getEditor() );

			if( $id !== null ) {
				$stmt->bind( 27, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$item->setId( $id ); //is not modified anymore
			} else {
				$stmt->bind( 27, $date ); // ctime
			}

			$stmt->execute()->finish();

			if( $id === null && $fetch === true )
			{
				$path = $this->getConfigPath() . 'newid';
				$item->setId( $this->newId( $conn, $path ) );
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}
	}


	/**
	 * Returns the item objects matched by the given search criteria.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param integer|null &$total Number of items that are available in total
	 * @return array List of items implementing \Aimeos\MShop\Common\Item\Address\Iface
	 */
	public function searchItems( \Aimeos\MW\Criteria\Iface $search, array $ref = [], &$total = null )
	{
		$dbm = $this->getContext()->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );
		$items = [];

		try
		{
			$domain = explode( '.', $this->prefix );

			if( ( $topdomain = array_shift( $domain ) ) === null ) {
				throw new \Aimeos\MShop\Exception( 'No configuration available.' );
			}

			$required = array( trim( $this->prefix, '.' ) );
			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
			$cfgPathSearch = $this->getConfigPath() . 'search';
			$cfgPathCount = $this->getConfigPath() . 'count';

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== false ) {
				$items[$row[$this->prefix . 'id']] = $this->createItemBase( $row );
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
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
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g type, etc.
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
	 * @return \Aimeos\MShop\Common\Item\Address\Iface New address item
	 */
	protected function createItemBase( array $values = [] )
	{
		return new \Aimeos\MShop\Common\Item\Address\Standard( $this->prefix, $values );
	}
}