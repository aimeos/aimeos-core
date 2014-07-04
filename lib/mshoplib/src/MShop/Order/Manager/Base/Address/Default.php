<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Order
 */


/**
 * Default order address manager implementation.
 *
 * @package MShop
 * @subpackage Order
 */
class MShop_Order_Manager_Base_Address_Default
	extends MShop_Common_Manager_Abstract
	implements MShop_Order_Manager_Base_Address_Interface
{
	private $_searchConfig = array(
		'order.base.address.id' => array(
			'code' => 'order.base.address.id',
			'internalcode' => 'mordbaad."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_order_base_address" AS mordbaad ON ( mordba."id" = mordbaad."baseid" )' ),
			'label' => 'Order base address ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.base.address.baseid' => array(
			'code' => 'order.base.address.baseid',
			'internalcode' => 'mordbaad."baseid"',
			'label' => 'Order base ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.base.address.siteid' => array(
			'code' => 'order.base.address.siteid',
			'internalcode' => 'mordbaad."siteid"',
			'label' => 'Order base address site ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.base.address.addressid' => array(
			'code' => 'order.base.address.addressid',
			'internalcode' => 'mordbaad."addrid"',
			'label' => 'Order base customer address ID',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.address.type' => array(
			'code' => 'order.base.address.type',
			'internalcode' => 'mordbaad."type"',
			'label' => 'Order base address type',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.address.company' => array(
			'code' => 'order.base.address.company',
			'internalcode' => 'mordbaad."company"',
			'label' => 'Order base address company',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.address.salutation' => array(
			'label' => 'Order address salutation',
			'code' => 'order.base.address.salutation',
			'internalcode' => 'mordbaad."salutation"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.address.title' => array(
			'code' => 'order.base.address.title',
			'internalcode' => 'mordbaad."title"',
			'label' => 'Order base address title',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.address.firstname' => array(
			'code' => 'order.base.address.firstname',
			'internalcode' => 'mordbaad."firstname"',
			'label' => 'Order base address firstname',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.address.lastname' => array(
			'code' => 'order.base.address.lastname',
			'internalcode' => 'mordbaad."lastname"',
			'label' => 'Order base address lastname',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.address.address1' => array(
			'code' => 'order.base.address.address1',
			'internalcode' => 'mordbaad."address1"',
			'label' => 'Order base address part one',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.address.address2' => array(
			'code' => 'order.base.address.address2',
			'internalcode' => 'mordbaad."address2"',
			'label' => 'Order base address part two',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.address.address3' => array(
			'code' => 'order.base.address.address3',
			'internalcode' => 'mordbaad."address3"',
			'label' => 'Order base address part three',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.address.postal' => array(
			'code' => 'order.base.address.postal',
			'internalcode' => 'mordbaad."postal"',
			'label' => 'Order base address postal',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.address.city' => array(
			'code' => 'order.base.address.city',
			'internalcode' => 'mordbaad."city"',
			'label' => 'Order base address city',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.address.state' => array(
			'code' => 'order.base.address.state',
			'internalcode' => 'mordbaad."state"',
			'label' => 'Order base address state',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.address.countryid' => array(
			'code' => 'order.base.address.countryid',
			'internalcode' => 'mordbaad."countryid"',
			'label' => 'Order base address country ID',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.address.languageid' => array(
			'code' => 'order.base.address.languageid',
			'internalcode' => 'mordbaad."langid"',
			'label' => 'Order base address language ID',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.address.telephone' => array(
			'code' => 'order.base.address.telephone',
			'internalcode' => 'mordbaad."telephone"',
			'label' => 'Order base address telephone',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.address.email' => array(
			'code' => 'order.base.address.email',
			'internalcode' => 'mordbaad."email"',
			'label' => 'Order base address email',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.address.telefax' => array(
			'code' => 'order.base.address.telefax',
			'internalcode' => 'mordbaad."telefax"',
			'label' => 'Order base address telefax',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.address.website' => array(
			'code' => 'order.base.address.website',
			'internalcode' => 'mordbaad."website"',
			'label' => 'Order base address website',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.address.flag' => array(
			'code' => 'order.base.address.flag',
			'internalcode' => 'mordbaad."flag"',
			'label' => 'Order base address flag',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'order.base.address.mtime' => array(
			'code' => 'order.base.address.mtime',
			'internalcode' => 'mordbaad."mtime"',
			'label' => 'Order base address modification date/time',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.address.ctime'=> array(
			'code'=>'order.base.address.ctime',
			'internalcode'=>'mordbaad."ctime"',
			'label'=>'Order base address create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.address.editor'=> array(
			'code'=>'order.base.address.editor',
			'internalcode'=>'mordbaad."editor"',
			'label'=>'Order base address editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);


	/**
	 * Initializes the object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context );
		$this->_setResourceName( 'db-order' );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param array $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$path = 'classes/order/manager/base/address/submanagers';
		foreach( $this->_getContext()->getConfig()->get( $path, array() ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->_cleanup( $siteids, 'mshop/order/manager/base/address/default/item/delete' );
	}


	/**
	 * Creates new order base address item object.
	 *
	 * @return MShop_Order_Item_Base_Address_Interface New order address item object
	 */
	public function createItem()
	{
		$values = array('siteid'=> $this->_getContext()->getLocale()->getSiteId());
		return $this->_createItem($values);
	}


	/**
	 * Inserts the new order base address items
	 *
	 * @param MShop_Order_Item_Base_Address_Interface $item order address item which should be saved
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Order_Item_Base_Address_Interface';
		if( !( $item instanceof $iface ) ) {
			throw new MShop_Order_Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		if( !$item->isModified() ) { return; }

		$context = $this->_getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$id = $item->getId();

			$path = 'mshop/order/manager/base/address/default/item/';
			$path .= ( $id === null ) ? 'insert' : 'update';

			$stmt = $this->_getCachedStatement( $conn, $path );
			$stmt->bind( 1, $item->getBaseId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, $context->getLocale()->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 3, $item->getAddressId(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 4, $item->getType(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 5, $item->getCompany(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 6, $item->getSalutation(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 7, $item->getTitle(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 8, $item->getFirstname(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 9, $item->getLastname(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 10, $item->getAddress1(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 11, $item->getAddress2(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 12, $item->getAddress3(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 13, $item->getPostal(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 14, $item->getCity(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 15, $item->getState(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 16, $item->getCountryId(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 17, $item->getLanguageId(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 18, $item->getTelephone(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 19, $item->getEmail(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 20, $item->getTelefax(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 21, $item->getWebsite(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 22, $item->getFlag(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 23, date( 'Y-m-d H:i:s' ), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 24, $context->getEditor() );

			if ( $id !== null ) {
				$stmt->bind( 25, $id, MW_DB_Statement_Abstract::PARAM_INT );
			} else {
				$stmt->bind( 25, date( 'Y-m-d H:i:s' ), MW_DB_Statement_Abstract::PARAM_STR ); // ctime
			}

			$result = $stmt->execute()->finish();


			if( $fetch === true )
			{
				if( $id === null ) {
					$path = 'mshop/order/manager/base/address/default/item/newid';
					$item->setId( $this->_newId( $conn, $context->getConfig()->get( $path, $path ) ) );
				} else {
					$item->setId( $id );
				}
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
	 * Removes multiple items specified by ids in the array.
	 *
	 * @param array $ids List of IDs
	 */
	public function deleteItems( array $ids )
	{
		$path = 'mshop/order/manager/base/address/default/item/delete';
		$this->_deleteItems( $ids, $this->_getContext()->getConfig()->get( $path, $path ) );
	}


	/**
	 * Creates a order base address item object for the given item id.
	 *
	 * @param integer $id Id of the order base address item
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return MShop_Order_Item_Base_Address_Interface Returns order base address item of the given id
	 * @throws MShop_Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_getItem( 'order.base.address.id', $id, $ref );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array Returns a list of attribtes implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		/** classes/order/manager/base/address/submanagers
		 * List of manager names that can be instantiated by the order base address manager
		 *
		 * Managers provide a generic interface to the underlying storage.
		 * Each manager has or can have sub-managers caring about particular
		 * aspects. Each of these sub-managers can be instantiated by its
		 * parent manager using the getSubManager() method.
		 *
		 * The search keys from sub-managers can be normally used in the
		 * manager as well. It allows you to search for items of the manager
		 * using the search keys of the sub-managers to further limit the
		 * retrieved list of items.
		 *
		 * @param array List of sub-manager names
		 * @since 2014.03
		 * @category Developer
		 */
		$path = 'classes/order/manager/base/address/submanagers';

		return $this->_getSearchAttributes( $this->_searchConfig, $path, array(), $withsub );
	}


	/**
	 * Search for order base address items based on the given critera.
	 *
	 * @param MW_Common_Criteria_Interface $search Search object with search conditions
	 * @param integer &$total Number of items that are available in total
	 * @return array List of order base address items implementing MShop_Order_Item_Base_Address_Interface
	 * @throws MShop_Order_Exception if creating items failed
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$context = $this->_getContext();
		$logger = $context->getLogger();
		$config = $context->getConfig();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		$items = array();

		try
		{
			$sitelevel = MShop_Locale_Manager_Abstract::SITE_SUBTREE;
			$cfgPathSearch = 'mshop/order/manager/base/address/default/item/search';
			$cfgPathCount =  'mshop/order/manager/base/address/default/item/count';
			$required = array( 'order.base.address' );

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount,
				$required, $total, $sitelevel );

			try
			{
				while( ( $row = $results->fetch() ) !== false ) {
					$items[ $row['id'] ] = $this->_createItem( $row );
				}
			}
			catch( Exception $e )
			{
				$results->finish();
				throw $e;
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
	 * Creates a new manager for order
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions
	 * @throws MShop_Order_Exception If creating manager failed
	 */

	public function getSubManager( $manager, $name = null )
	{
		/** classes/order/manager/base/address/name
		 * Class name of the used order base address manager implementation
		 *
		 * Each default order base address manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  MShop_Order_Manager_Base_Address_Default
		 *
		 * and you want to replace it with your own version named
		 *
		 *  MShop_Order_Manager_Base_Address_Myaddress
		 *
		 * then you have to set the this configuration option:
		 *
		 *  classes/order/manager/base/address/name = Myaddress
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyAddress"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 */

		/** mshop/order/manager/base/address/decorators/excludes
		 * Excludes decorators added by the "common" option from the order base address manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the order base address manager.
		 *
		 *  mshop/order/manager/base/address/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("MShop_Common_Manager_Decorator_*") added via
		 * "mshop/common/manager/decorators/default" for the order base address manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/base/address/decorators/global
		 * @see mshop/order/manager/base/address/decorators/local
		 */

		/** mshop/order/manager/base/address/decorators/global
		 * Adds a list of globally available decorators only to the order base address manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("MShop_Common_Manager_Decorator_*") around the order base address manager.
		 *
		 *  mshop/order/manager/base/address/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "MShop_Common_Manager_Decorator_Decorator1" only to the order controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/base/address/decorators/excludes
		 * @see mshop/order/manager/base/address/decorators/local
		 */

		/** mshop/order/manager/base/address/decorators/local
		 * Adds a list of local decorators only to the order base address manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("MShop_Common_Manager_Decorator_*") around the order base address manager.
		 *
		 *  mshop/order/manager/base/address/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "MShop_Common_Manager_Decorator_Decorator2" only to the order
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/base/address/decorators/excludes
		 * @see mshop/order/manager/base/address/decorators/global
		 */

		return $this->_getSubManager( 'order', 'base/address/' . $manager, $name );
	}


	/**
	 * Creates new order base address item object.
	 *
	 * @see MShop_Order_Item_Base_Address_Default Default order base address item
	 * @param array $values Possible optional array keys can be given: id, type, firstname, lastname
	 * @return MShop_Order_Item_Base_Address_Default New order base address item object
	 */
	protected function _createItem( array $values = array() )
	{
		return new MShop_Order_Item_Base_Address_Default( $values );
	}
}
