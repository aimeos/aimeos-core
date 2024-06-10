<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2024
 * @package MShop
 * @subpackage Customer
 */


namespace Aimeos\MShop\Customer\Manager;


/**
 * Default implementation of the customer class.
 *
 * @package MShop
 * @subpackage Customer
 */
class Standard
	extends \Aimeos\MShop\Customer\Manager\Base
	implements \Aimeos\MShop\Customer\Manager\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	/** mshop/customer/manager/name
	 * Class name of the used customer manager implementation
	 *
	 * Each default manager can be replace by an alternative imlementation.
	 * To use this implementation, you have to set the last part of the class
	 * name as configuration value so the manager factory knows which class it
	 * has to instantiate.
	 *
	 * For example, if the name of the default class is
	 *
	 *  \Aimeos\MShop\Customer\Manager\Standard
	 *
	 * and you want to replace it with your own version named
	 *
	 *  \Aimeos\MShop\Customer\Manager\Mymanager
	 *
	 * then you have to set the this configuration option:
	 *
	 *  mshop/customer/manager/name = Mymanager
	 *
	 * The value is the last part of your own class name and it's case sensitive,
	 * so take care that the configuration value is exactly named like the last
	 * part of the class name.
	 *
	 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
	 * characters are possible! You should always start the last part of the class
	 * name with an upper case character and continue only with lower case characters
	 * or numbers. Avoid chamel case names like "MyManager"!
	 *
	 * @param string Last part of the class name
	 * @since 2014.03
	 * @category Developer
	 */

	/** mshop/customer/manager/decorators/excludes
	 * Excludes decorators added by the "common" option from the customer manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to remove a decorator added via
	 * "mshop/common/manager/decorators/default" before they are wrapped
	 * around the customer manager.
	 *
	 *  mshop/customer/manager/decorators/excludes = array( 'decorator1' )
	 *
	 * This would remove the decorator named "decorator1" from the list of
	 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
	 * "mshop/common/manager/decorators/default" for the customer manager.
	 *
	 * @param array List of decorator names
	 * @since 2014.03
	 * @category Developer
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/customer/manager/decorators/global
	 * @see mshop/customer/manager/decorators/local
	 */

	/** mshop/customer/manager/decorators/global
	 * Adds a list of globally available decorators only to the customer manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap global decorators
	 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the customer manager.
	 *
	 *  mshop/customer/manager/decorators/global = array( 'decorator1' )
	 *
	 * This would add the decorator named "decorator1" defined by
	 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the customer
	 * manager.
	 *
	 * @param array List of decorator names
	 * @since 2014.03
	 * @category Developer
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/customer/manager/decorators/excludes
	 * @see mshop/customer/manager/decorators/local
	 */

	/** mshop/customer/manager/decorators/local
	 * Adds a list of local decorators only to the customer manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap local decorators
	 * ("\Aimeos\MShop\Customer\Manager\Decorator\*") around the customer manager.
	 *
	 *  mshop/customer/manager/decorators/local = array( 'decorator2' )
	 *
	 * This would add the decorator named "decorator2" defined by
	 * "\Aimeos\MShop\Customer\Manager\Decorator\Decorator2" only to the customer
	 * manager.
	 *
	 * @param array List of decorator names
	 * @since 2014.03
	 * @category Developer
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/customer/manager/decorators/excludes
	 * @see mshop/customer/manager/decorators/global
	 */


	private array $searchConfig = array(
		'customer.id' => array(
			'label' => 'ID',
			'code' => 'customer.id',
			'internalcode' => 'mcus."id"',
			'type' => 'int',
			'public' => false,
		),
		'customer.siteid' => array(
			'code' => 'customer.siteid',
			'internalcode' => 'mcus."siteid"',
			'label' => 'Customer site ID',
			'public' => false,
		),
		'customer.code' => array(
			'label' => 'Username',
			'code' => 'customer.code',
			'internalcode' => 'mcus."code"',
		),
		'customer.label' => array(
			'label' => 'Label',
			'code' => 'customer.label',
			'internalcode' => 'mcus."label"',
		),
		'customer.salutation' => array(
			'label' => 'Salutation',
			'code' => 'customer.salutation',
			'internalcode' => 'mcus."salutation"',
		),
		'customer.company' => array(
			'label' => 'Company',
			'code' => 'customer.company',
			'internalcode' => 'mcus."company"',
		),
		'customer.vatid' => array(
			'label' => 'Vat ID',
			'code' => 'customer.vatid',
			'internalcode' => 'mcus."vatid"',
		),
		'customer.title' => array(
			'label' => 'Title',
			'code' => 'customer.title',
			'internalcode' => 'mcus."title"',
		),
		'customer.firstname' => array(
			'label' => 'Firstname',
			'code' => 'customer.firstname',
			'internalcode' => 'mcus."firstname"',
		),
		'customer.lastname' => array(
			'label' => 'Lastname',
			'code' => 'customer.lastname',
			'internalcode' => 'mcus."lastname"',
		),
		'customer.address1' => array(
			'label' => 'Address part one',
			'code' => 'customer.address1',
			'internalcode' => 'mcus."address1"',
		),
		'customer.address2' => array(
			'label' => 'Address part two',
			'code' => 'customer.address2',
			'internalcode' => 'mcus."address2"',
		),
		'customer.address3' => array(
			'label' => 'Address part three',
			'code' => 'customer.address3',
			'internalcode' => 'mcus."address3"',
		),
		'customer.postal' => array(
			'label' => 'Postal',
			'code' => 'customer.postal',
			'internalcode' => 'mcus."postal"',
		),
		'customer.city' => array(
			'label' => 'City',
			'code' => 'customer.city',
			'internalcode' => 'mcus."city"',
		),
		'customer.state' => array(
			'label' => 'State',
			'code' => 'customer.state',
			'internalcode' => 'mcus."state"',
		),
		'customer.languageid' => array(
			'label' => 'Language',
			'code' => 'customer.languageid',
			'internalcode' => 'mcus."langid"',
		),
		'customer.countryid' => array(
			'label' => 'Country',
			'code' => 'customer.countryid',
			'internalcode' => 'mcus."countryid"',
		),
		'customer.telephone' => array(
			'label' => 'Telephone',
			'code' => 'customer.telephone',
			'internalcode' => 'mcus."telephone"',
		),
		'customer.telefax' => array(
			'label' => 'Facsimile',
			'code' => 'customer.telefax',
			'internalcode' => 'mcus."telefax"',
		),
		'customer.mobile' => array(
			'label' => 'Mobile number',
			'code' => 'customer.mobile',
			'internalcode' => 'mcus."mobile"',
		),
		'customer.email' => array(
			'label' => 'E-mail',
			'code' => 'customer.email',
			'internalcode' => 'mcus."email"',
		),
		'customer.website' => array(
			'label' => 'Web site',
			'code' => 'customer.website',
			'internalcode' => 'mcus."website"',
		),
		'customer.longitude' => array(
			'label' => 'Longitude',
			'code' => 'customer.longitude',
			'internalcode' => 'mcus."longitude"',
			'public' => false,
		),
		'customer.latitude' => array(
			'label' => 'Latitude',
			'code' => 'customer.latitude',
			'internalcode' => 'mcus."latitude"',
			'public' => false,
		),
		'customer.birthday' => array(
			'label' => 'Birthday',
			'code' => 'customer.birthday',
			'internalcode' => 'mcus."birthday"',
		),
		'customer.status' => array(
			'label' => 'Status',
			'code' => 'customer.status',
			'internalcode' => 'mcus."status"',
			'type' => 'int',
		),
		'customer.dateverified' => array(
			'label' => 'Verification date',
			'code' => 'customer.dateverified',
			'internalcode' => 'mcus."vdate"',
			'type' => 'date',
			'public' => false,
		),
		'customer.password' => array(
			'label' => 'Password',
			'code' => 'customer.password',
			'internalcode' => 'mcus."password"',
			'public' => false,
		),
		'customer.ctime' => array(
			'label' => 'Create date/time',
			'code' => 'customer.ctime',
			'internalcode' => 'mcus."ctime"',
			'type' => 'datetime',
			'public' => false,
		),
		'customer.mtime' => array(
			'label' => 'Modify date/time',
			'code' => 'customer.mtime',
			'internalcode' => 'mcus."mtime"',
			'type' => 'datetime',
			'public' => false,
		),
		'customer.editor' => array(
			'label' => 'Editor',
			'code' => 'customer.editor',
			'internalcode' => 'mcus."editor"',
			'public' => false,
		),
		'customer:has' => array(
			'code' => 'customer:has()',
			'internalcode' => ':site AND :key AND mcusli."id"',
			'internaldeps' => ['LEFT JOIN "mshop_customer_list" AS mcusli ON ( mcusli."parentid" = mcus."id" )'],
			'label' => 'Customer has list item, parameter(<domain>[,<list type>[,<reference ID>)]]',
			'type' => 'null',
			'public' => false,
		),
		'customer:prop' => array(
			'code' => 'customer:prop()',
			'internalcode' => ':site AND :key AND mcuspr."id"',
			'internaldeps' => ['LEFT JOIN "mshop_customer_property" AS mcuspr ON ( mcuspr."parentid" = mcus."id" )'],
			'label' => 'Customer has property item, parameter(<property type>[,<language code>[,<property value>]])',
			'type' => 'null',
			'public' => false,
		),
	);


	/**
	 * Initializes the object.
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object
	 */
	public function __construct( \Aimeos\MShop\ContextIface $context )
	{
		parent::__construct( $context );

		$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
		$level = $context->config()->get( 'mshop/customer/manager/sitemode', $level );


		$this->searchConfig['customer:has']['function'] = function( &$source, array $params ) use ( $level ) {

			$keys = [];

			foreach( (array) ( $params[1] ?? '' ) as $type ) {
				foreach( (array) ( $params[2] ?? '' ) as $id ) {
					$keys[] = $params[0] . '|' . ( $type ? $type . '|' : '' ) . $id;
				}
			}

			$sitestr = $this->siteString( 'mcusli."siteid"', $level );
			$keystr = $this->toExpression( 'mcusli."key"', $keys, ( $params[2] ?? null ) ? '==' : '=~' );
			$source = str_replace( [':site', ':key'], [$sitestr, $keystr], $source );

			return $params;
		};


		$this->searchConfig['customer:prop']['function'] = function( &$source, array $params ) use ( $level ) {

			$keys = [];
			$langs = array_key_exists( 1, $params ) ? ( $params[1] ?? 'null' ) : '';

			foreach( (array) $langs as $lang ) {
				foreach( (array) ( $params[2] ?? '' ) as $val ) {
					$keys[] = substr( $params[0] . '|' . ( $lang === null ? 'null|' : ( $lang ? $lang . '|' : '' ) ) . $val, 0, 255 );
				}
			}

			$sitestr = $this->siteString( 'mcuspr."siteid"', $level );
			$keystr = $this->toExpression( 'mcuspr."key"', $keys, ( $params[2] ?? null ) ? '==' : '=~' );
			$source = str_replace( [':site', ':key'], [$sitestr, $keystr], $source );

			return $params;
		};
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param iterable $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MShop\Customer\Manager\Iface Manager object for chaining method calls
	 */
	public function clear( iterable $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$path = 'mshop/customer/manager/submanagers';
		$default = ['address', 'lists', 'property'];

		foreach( $this->context()->config()->get( $path, $default ) as $domain ) {
			$this->object()->getSubManager( $domain )->clear( $siteids );
		}

		return $this->clearBase( $siteids, 'mshop/customer/manager/clear' );
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Customer\Item\Iface New customer item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$values['customer.siteid'] = $values['customer.siteid'] ?? $this->context()->locale()->getSiteId();
		return $this->createItemBase( $values );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param bool $withsub Return also the resource type of sub-managers if true
	 * @return string[] Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( bool $withsub = true ) : array
	{
		$path = 'mshop/customer/manager/submanagers';
		$default = ['address', 'lists', 'property'];

		return $this->getResourceTypeBase( 'customer', $path, $default, $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\Base\Criteria\Attribute\Iface List of search attribute items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		/** mshop/customer/manager/submanagers
		 * List of manager names that can be instantiated by the customer manager
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
		$path = 'mshop/customer/manager/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, ['address'], $withsub );
	}


	/**
	 * Removes multiple items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|string[] $itemIds List of item objects or IDs of the items
	 * @return \Aimeos\MShop\Customer\Manager\Iface Manager object for chaining method calls
	 */
	public function delete( $itemIds ) : \Aimeos\MShop\Common\Manager\Iface
	{
		/** mshop/customer/manager/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/customer/manager/delete/ansi
		 */

		/** mshop/customer/manager/delete/ansi
		 * Deletes the items matched by the given IDs from the database
		 *
		 * Removes the records specified by the given IDs from the customer database.
		 * The records must be from the site that is configured via the
		 * context item.
		 *
		 * The ":cond" placeholder is replaced by the name of the ID column and
		 * the given ID or list of IDs while the site ID is bound to the question
		 * mark.
		 *
		 * The SQL statement should conform to the ANSI standard to be
		 * compatible with most relational database systems. This also
		 * includes using double quotes for table and column names.
		 *
		 * @param string SQL statement for deleting items
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/customer/manager/insert/ansi
		 * @see mshop/customer/manager/update/ansi
		 * @see mshop/customer/manager/newid/ansi
		 * @see mshop/customer/manager/search/ansi
		 * @see mshop/customer/manager/count/ansi
		 */
		$path = 'mshop/customer/manager/delete';

		return $this->deleteItemsBase( $itemIds, $path )->deleteRefItems( $itemIds );
	}


	/**
	 * Saves a customer item object.
	 *
	 * @param \Aimeos\MShop\Customer\Item\Iface $item Customer item object
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Customer\Item\Iface $item Updated item including the generated ID
	 */
	protected function saveItem( \Aimeos\MShop\Customer\Item\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Customer\Item\Iface
	{
		$item = $this->addGroups( $item );

		if( !$item->isModified() )
		{
			$item = $this->savePropertyItems( $item, 'customer', $fetch );
			$item = $this->saveAddressItems( $item, 'customer', $fetch );
			return $this->saveListItems( $item, 'customer', $fetch );
		}

		$context = $this->context();
		$conn = $context->db( $this->getResourceName() );

		$id = $item->getId();
		$billingAddress = $item->getPaymentAddress();
		$columns = $this->object()->getSaveAttributes();

		if( $id === null )
		{
			/** mshop/customer/manager/insert/mysql
			 * Inserts a new customer record into the database table
			 *
			 * @see mshop/customer/manager/insert/ansi
			 */

			/** mshop/customer/manager/insert/ansi
			 * Inserts a new customer record into the database table
			 *
			 * Items with no ID yet (i.e. the ID is NULL) will be created in
			 * the database and the newly created ID retrieved afterwards
			 * using the "newid" SQL statement.
			 *
			 * The SQL statement must be a string suitable for being used as
			 * prepared statement. It must include question marks for binding
			 * the values from the customer item to the statement before they are
			 * sent to the database server. The number of question marks must
			 * be the same as the number of columns listed in the INSERT
			 * statement. The order of the columns must correspond to the
			 * order in the save() method, so the correct values are
			 * bound to the columns.
			 *
			 * The SQL statement should conform to the ANSI standard to be
			 * compatible with most relational database systems. This also
			 * includes using double quotes for table and column names.
			 *
			 * @param string SQL statement for inserting records
			 * @since 2014.03
			 * @category Developer
			 * @see mshop/customer/manager/update/ansi
			 * @see mshop/customer/manager/newid/ansi
			 * @see mshop/customer/manager/delete/ansi
			 * @see mshop/customer/manager/search/ansi
			 * @see mshop/customer/manager/count/ansi
			 */
			$path = 'mshop/customer/manager/insert';
			$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ) );
		}
		else
		{
			/** mshop/customer/manager/update/mysql
			 * Updates an existing customer record in the database
			 *
			 * @see mshop/customer/manager/update/ansi
			 */

			/** mshop/customer/manager/update/ansi
			 * Updates an existing customer record in the database
			 *
			 * Items which already have an ID (i.e. the ID is not NULL) will
			 * be updated in the database.
			 *
			 * The SQL statement must be a string suitable for being used as
			 * prepared statement. It must include question marks for binding
			 * the values from the customer item to the statement before they are
			 * sent to the database server. The order of the columns must
			 * correspond to the order in the save() method, so the
			 * correct values are bound to the columns.
			 *
			 * The SQL statement should conform to the ANSI standard to be
			 * compatible with most relational database systems. This also
			 * includes using double quotes for table and column names.
			 *
			 * @param string SQL statement for updating records
			 * @since 2014.03
			 * @category Developer
			 * @see mshop/customer/manager/insert/ansi
			 * @see mshop/customer/manager/newid/ansi
			 * @see mshop/customer/manager/delete/ansi
			 * @see mshop/customer/manager/search/ansi
			 * @see mshop/customer/manager/count/ansi
			 */
			$path = 'mshop/customer/manager/update';
			$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ), false );
		}

		$idx = 1;
		$stmt = $this->getCachedStatement( $conn, $path, $sql );

		foreach( $columns as $name => $entry ) {
			$stmt->bind( $idx++, $item->get( $name ), \Aimeos\Base\Criteria\SQL::type( $entry->getType() ) );
		}

		$stmt->bind( $idx++, $item->getLabel() );
		$stmt->bind( $idx++, $item->getCode() );
		$stmt->bind( $idx++, $billingAddress->getCompany() );
		$stmt->bind( $idx++, $billingAddress->getVatID() );
		$stmt->bind( $idx++, $billingAddress->getSalutation() );
		$stmt->bind( $idx++, $billingAddress->getTitle() );
		$stmt->bind( $idx++, $billingAddress->getFirstname() );
		$stmt->bind( $idx++, $billingAddress->getLastname() );
		$stmt->bind( $idx++, $billingAddress->getAddress1() );
		$stmt->bind( $idx++, $billingAddress->getAddress2() );
		$stmt->bind( $idx++, $billingAddress->getAddress3() );
		$stmt->bind( $idx++, $billingAddress->getPostal() );
		$stmt->bind( $idx++, $billingAddress->getCity() );
		$stmt->bind( $idx++, $billingAddress->getState() );
		$stmt->bind( $idx++, $billingAddress->getCountryId() );
		$stmt->bind( $idx++, $billingAddress->getLanguageId() );
		$stmt->bind( $idx++, $billingAddress->getTelephone() );
		$stmt->bind( $idx++, $billingAddress->getMobile() );
		$stmt->bind( $idx++, $billingAddress->getEmail() );
		$stmt->bind( $idx++, $billingAddress->getTelefax() );
		$stmt->bind( $idx++, $billingAddress->getWebsite() );
		$stmt->bind( $idx++, $billingAddress->getLongitude() );
		$stmt->bind( $idx++, $billingAddress->getLatitude() );
		$stmt->bind( $idx++, $billingAddress->getBirthday() );
		$stmt->bind( $idx++, $item->getStatus(), \Aimeos\Base\DB\Statement\Base::PARAM_INT );
		$stmt->bind( $idx++, $item->getDateVerified() );
		$stmt->bind( $idx++, $item->getPassword() );
		$stmt->bind( $idx++, $context->datetime() ); // Modification time
		$stmt->bind( $idx++, $context->editor() );

		if( $id !== null ) {
			$stmt->bind( $idx++, $context->locale()->getSiteId() . '%' );
			$stmt->bind( $idx++, $this->getUser()?->getSiteId() );
			$stmt->bind( $idx, $id, \Aimeos\Base\DB\Statement\Base::PARAM_INT );
			$billingAddress->setId( $id ); // enforce ID to be present
		} else {
			$stmt->bind( $idx++, $this->siteId( $item->getSiteId(), \Aimeos\MShop\Locale\Manager\Base::SITE_SUBTREE ) );
			$stmt->bind( $idx, $context->datetime() ); // Creation time
		}

		$stmt->execute()->finish();

		if( $id === null )
		{
			/** mshop/customer/manager/newid/mysql
			 * Retrieves the ID generated by the database when inserting a new record
			 *
			 * @see mshop/customer/manager/newid/ansi
			 */

			/** mshop/customer/manager/newid/ansi
			 * Retrieves the ID generated by the database when inserting a new record
			 *
			 * As soon as a new record is inserted into the database table,
			 * the database server generates a new and unique identifier for
			 * that record. This ID can be used for retrieving, updating and
			 * deleting that specific record from the table again.
			 *
			 * For MySQL:
			 *  SELECT LAST_INSERT_ID()
			 * For PostgreSQL:
			 *  SELECT currval('seq_mcus_id')
			 * For SQL Server:
			 *  SELECT SCOPE_IDENTITY()
			 * For Oracle:
			 *  SELECT "seq_mcus_id".CURRVAL FROM DUAL
			 *
			 * There's no way to retrive the new ID by a SQL statements that
			 * fits for most database servers as they implement their own
			 * specific way.
			 *
			 * @param string SQL statement for retrieving the last inserted record ID
			 * @since 2014.03
			 * @category Developer
			 * @see mshop/customer/manager/insert/ansi
			 * @see mshop/customer/manager/update/ansi
			 * @see mshop/customer/manager/delete/ansi
			 * @see mshop/customer/manager/search/ansi
			 * @see mshop/customer/manager/count/ansi
			 */
			$path = 'mshop/customer/manager/newid';
			$id = $this->newId( $conn, $path );
		}

		$item->setId( $id );

		$item = $this->savePropertyItems( $item, 'customer', $fetch );
		$item = $this->saveAddressItems( $item, 'customer', $fetch );
		return $this->saveListItems( $item, 'customer', $fetch );
	}


	/**
	 * Returns the item objects matched by the given search criteria.
	 *
	 * @param \Aimeos\Base\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int|null &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Customer\Item\Iface with ids as keys
	 */
	public function search( \Aimeos\Base\Criteria\Iface $search, array $ref = [], int &$total = null ) : \Aimeos\Map
	{
		$map = [];
		$context = $this->context();
		$conn = $context->db( $this->getResourceName() );

		$required = array( 'customer' );

		/** mshop/customer/manager/sitemode
		 * Mode how items from levels below or above in the site tree are handled
		 *
		 * By default, only items from the current site are fetched from the
		 * storage. If the ai-sites extension is installed, you can create a
		 * tree of sites. Then, this setting allows you to define for the
		 * whole customer domain if items from parent sites are inherited,
		 * sites from child sites are aggregated or both.
		 *
		 * Available constants for the site mode are:
		 * * 0 = only items from the current site
		 * * 1 = inherit items from parent sites
		 * * 2 = aggregate items from child sites
		 * * 3 = inherit and aggregate items at the same time
		 *
		 * You also need to set the mode in the locale manager
		 * (mshop/locale/manager/sitelevel) to one of the constants.
		 * If you set it to the same value, it will work as described but you
		 * can also use different modes. For example, if inheritance and
		 * aggregation is configured the locale manager but only inheritance
		 * in the domain manager because aggregating items makes no sense in
		 * this domain, then items wil be only inherited. Thus, you have full
		 * control over inheritance and aggregation in each domain.
		 *
		 * @param int Constant from Aimeos\MShop\Locale\Manager\Base class
		 * @category Developer
		 * @since 2018.01
		 * @see mshop/locale/manager/sitelevel
		 */
		$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
		$level = $context->config()->get( 'mshop/customer/manager/sitemode', $level );

		/** mshop/customer/manager/search/mysql
		 * Retrieves the records matched by the given criteria in the database
		 *
		 * @see mshop/customer/manager/search/ansi
		 */

		/** mshop/customer/manager/search/ansi
		 * Retrieves the records matched by the given criteria in the database
		 *
		 * Fetches the records matched by the given criteria from the customer
		 * database. The records must be from one of the sites that are
		 * configured via the context item. If the current site is part of
		 * a tree of sites, the SELECT statement can retrieve all records
		 * from the current site and the complete sub-tree of sites.
		 *
		 * As the records can normally be limited by criteria from sub-managers,
		 * their tables must be joined in the SQL context. This is done by
		 * using the "internaldeps" property from the definition of the ID
		 * column of the sub-managers. These internal dependencies specify
		 * the JOIN between the tables and the used columns for joining. The
		 * ":joins" placeholder is then replaced by the JOIN strings from
		 * the sub-managers.
		 *
		 * To limit the records matched, conditions can be added to the given
		 * criteria object. It can contain comparisons like column names that
		 * must match specific values which can be combined by AND, OR or NOT
		 * operators. The resulting string of SQL conditions replaces the
		 * ":cond" placeholder before the statement is sent to the database
		 * server.
		 *
		 * If the records that are retrieved should be ordered by one or more
		 * columns, the generated string of column / sort direction pairs
		 * replaces the ":order" placeholder. Columns of
		 * sub-managers can also be used for ordering the result set but then
		 * no index can be used.
		 *
		 * The number of returned records can be limited and can start at any
		 * number between the begining and the end of the result set. For that
		 * the ":size" and ":start" placeholders are replaced by the
		 * corresponding values from the criteria object. The default values
		 * are 0 for the start and 100 for the size value.
		 *
		 * The SQL statement should conform to the ANSI standard to be
		 * compatible with most relational database systems. This also
		 * includes using double quotes for table and column names.
		 *
		 * @param string SQL statement for searching items
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/customer/manager/insert/ansi
		 * @see mshop/customer/manager/update/ansi
		 * @see mshop/customer/manager/newid/ansi
		 * @see mshop/customer/manager/delete/ansi
		 * @see mshop/customer/manager/count/ansi
		 */
		$cfgPathSearch = 'mshop/customer/manager/search';

		/** mshop/customer/manager/count/mysql
		 * Counts the number of records matched by the given criteria in the database
		 *
		 * @see mshop/customer/manager/count/ansi
		 */

		/** mshop/customer/manager/count/ansi
		 * Counts the number of records matched by the given criteria in the database
		 *
		 * Counts all records matched by the given criteria from the customer
		 * database. The records must be from one of the sites that are
		 * configured via the context item. If the current site is part of
		 * a tree of sites, the statement can count all records from the
		 * current site and the complete sub-tree of sites.
		 *
		 * As the records can normally be limited by criteria from sub-managers,
		 * their tables must be joined in the SQL context. This is done by
		 * using the "internaldeps" property from the definition of the ID
		 * column of the sub-managers. These internal dependencies specify
		 * the JOIN between the tables and the used columns for joining. The
		 * ":joins" placeholder is then replaced by the JOIN strings from
		 * the sub-managers.
		 *
		 * To limit the records matched, conditions can be added to the given
		 * criteria object. It can contain comparisons like column names that
		 * must match specific values which can be combined by AND, OR or NOT
		 * operators. The resulting string of SQL conditions replaces the
		 * ":cond" placeholder before the statement is sent to the database
		 * server.
		 *
		 * Both, the strings for ":joins" and for ":cond" are the same as for
		 * the "search" SQL statement.
		 *
		 * Contrary to the "search" statement, it doesn't return any records
		 * but instead the number of records that have been found. As counting
		 * thousands of records can be a long running task, the maximum number
		 * of counted records is limited for performance reasons.
		 *
		 * The SQL statement should conform to the ANSI standard to be
		 * compatible with most relational database systems. This also
		 * includes using double quotes for table and column names.
		 *
		 * @param string SQL statement for counting items
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/customer/manager/insert/ansi
		 * @see mshop/customer/manager/update/ansi
		 * @see mshop/customer/manager/newid/ansi
		 * @see mshop/customer/manager/delete/ansi
		 * @see mshop/customer/manager/search/ansi
		 */
		$cfgPathCount = 'mshop/customer/manager/count';

		$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

		while( $row = $results->fetch() ) {
			$map[$row['customer.id']] = $row;
		}

		$addrItems = [];
		if( in_array( 'customer/address', $ref, true ) ) {
			$addrItems = $this->getAddressItems( array_keys( $map ), 'customer' );
		}

		$propItems = []; $name = 'customer/property';
		if( isset( $ref[$name] ) || in_array( $name, $ref, true ) )
		{
			$propTypes = isset( $ref[$name] ) && is_array( $ref[$name] ) ? $ref[$name] : null;
			$propItems = $this->getPropertyItems( array_keys( $map ), 'customer', $propTypes );
		}

		return $this->buildItems( $map, $ref, 'customer', $addrItems, $propItems );
	}


	/**
	 * Returns a new manager for customer extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g stock, tags, locations, etc.
	 */
	public function getSubManager( string $manager, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this->getSubManagerBase( 'customer', $manager, $name );
	}
}
