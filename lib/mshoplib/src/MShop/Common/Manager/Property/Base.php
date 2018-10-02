<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2018
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\Property;


/**
 * Abstract property manager implementation.
 *
 * @package MShop
 * @subpackage Common
 */
abstract class Base
	extends \Aimeos\MShop\Common\Manager\Base
{
	private $prefix;
	private $searchConfig;
	private $languageId;


	/**
	 * Initializes the object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context );

		$this->languageId = $context->getLocale()->getLanguageId();
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
	 * Creates a new empty item instance
	 *
	 * @param string|null Type the item should be created with
	 * @param string|null Domain of the type the item should be created with
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Common\Item\Property\Iface New property item object
	 */
	public function createItem( $type = null, $domain = null, array $values = [] )
	{
		$values[$this->prefix . 'siteid'] = $this->getContext()->getLocale()->getSiteId();

		if( $type !== null )
		{
			$values[$this->prefix . 'typeid'] = $this->getTypeId( $type, $domain );
			$values[$this->prefix . 'type'] = $type;
		}

		return $this->createItemBase( $values );
	}


	/**
	 * Creates a search object and optionally sets base criteria.
	 *
	 * @param boolean $default Add default criteria
	 * @return \Aimeos\MW\Criteria\Iface Criteria object
	 */
	public function createSearch( $default = false )
	{
		$object = parent::createSearch();

		if( $default === true )
		{
			$langid = $this->getContext()->getLocale()->getLanguageId();

			$expr = array(
				$object->compare( '==', $this->prefix . 'languageid', null ),
				$object->compare( '==', $this->prefix . 'languageid', $langid ),
			);

			$object->setConditions( $object->combine( '||', $expr ) );
		}

		return $object;
	}


	/**
	 * Inserts the new property items for product item
	 *
	 * @param \Aimeos\MShop\Common\Item\Property\Iface $item Property item which should be saved
	 * @param boolean $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Common\Item\Iface $item Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Common\Item\Iface $item, $fetch = true )
	{
		self::checkClass( '\\Aimeos\\MShop\\Common\\Item\\Property\\Iface', $item );

		if( !$item->isModified() ) {
			return $item;
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

			$stmt = $conn->create( $this->getSqlConfig( $this->getConfigPath() . $type ) );

			$stmt->bind( 1, $item->getParentId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 2, $item->getTypeId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 3, $item->getLanguageId() );
			$stmt->bind( 4, $item->getValue() );
			$stmt->bind( 5, $date ); //mtime
			$stmt->bind( 6, $context->getEditor() );
			$stmt->bind( 7, $context->getLocale()->getSiteId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );

			if( $id !== null ) {
				$stmt->bind( 8, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$item->setId( $id ); //is not modified anymore
			} else {
				$stmt->bind( 8, $date ); //ctime
			}

			$stmt->execute()->finish();

			if( $fetch === true )
			{
				if( $id === null ) {
					$item->setId( $this->newId( $conn, $this->getConfigPath() . 'newid' ) );
				} else {
					$item->setId( $id ); // modified false
				}
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return $item;
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
	 * Returns product property item with given Id.
	 *
	 * @param integer $id Id of the product property item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param boolean $default Add default criteria
	 * @return \Aimeos\MShop\Common\Item\Property\Iface Returns the product property item of the given id
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = [], $default = false )
	{
		return $this->getItemBase( $this->prefix . 'id', $id, $ref, $default );
	}


	/**
	 * Search for all property items based on the given critera.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param integer|null &$total Number of items that are available in total
	 * @return array List of property items implementing \Aimeos\MShop\Common\Item\Property\Iface
	 */
	public function searchItems( \Aimeos\MW\Criteria\Iface $search, array $ref = [], &$total = null )
	{
		$items = $map = $typeIds = [];
		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$domain = explode( '.', $this->prefix );

			if( ( $topdomain = array_shift( $domain ) ) === null ) {
				throw new \Aimeos\MShop\Exception( 'No configuration available.' );
			}

			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
			$cfgPathSearch = $this->getConfigPath() . 'search';
			$cfgPathCount = $this->getConfigPath() . 'count';
			$required = array( trim( $this->prefix, '.' ) );

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );
			while( ( $row = $results->fetch() ) !== false )
			{
				$map[ $row[$this->prefix . 'id'] ] = $row;
				$typeIds[ $row[$this->prefix . 'typeid'] ] = null;
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		if( !empty( $typeIds ) )
		{
			$typeManager = $this->getObject()->getSubManager( 'type' );
			$typeSearch = $typeManager->createSearch();
			$typeSearch->setConditions( $typeSearch->compare( '==', $this->prefix . 'type.id', array_keys( $typeIds ) ) );
			$typeSearch->setSlice( 0, $search->getSliceSize() );
			$typeItems = $typeManager->searchItems( $typeSearch );

			foreach( $map as $id => $row )
			{
				if( isset( $typeItems[ $row[$this->prefix . 'typeid'] ] ) )
				{
					$row[$this->prefix . 'type'] = $typeItems[ $row[$this->prefix . 'typeid'] ]->getCode();
					$row[$this->prefix . 'typename'] = $typeItems[ $row[$this->prefix . 'typeid'] ]->getName();
				}

				$items[$id] = $this->createItemBase( $row );
			}
		}

		return $items;
	}


	/**
	 * Returns a new manager for product extensions
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from
	 * configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g property types, property lists etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->getSubManagerBase( 'common', 'property/' . $manager, $name );
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
	 * Creates new property item object.
	 *
	 * @param array $values Associative list of key/value pairs
	 * @return \Aimeos\MShop\Common\Item\Property\Standard New property item object
	 */
	protected function createItemBase( array $values = [] )
	{
		$values['languageid'] = $this->languageId;

		return new \Aimeos\MShop\Common\Item\Property\Standard( $this->prefix, $values );
	}


	/**
	 * Returns the prefix used for the item keys.
	 *
	 * @return string Item key prefix
	 */
	protected function getPrefix()
	{
		return $this->prefix;
	}
}
