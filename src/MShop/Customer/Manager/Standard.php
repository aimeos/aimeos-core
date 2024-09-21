<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
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
	/**
	 * Removes old entries from the storage.
	 *
	 * @param iterable $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MShop\Customer\Manager\Iface Manager object for chaining method calls
	 */
	public function clear( iterable $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		foreach( $this->context()->config()->get( 'mshop/customer/manager/submanagers', [] ) as $domain ) {
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

		$address = new \Aimeos\MShop\Common\Item\Address\Standard( 'customer.', $values );
		return new \Aimeos\MShop\Customer\Item\Standard( $address, 'customer.', $values, $this->context()->password() );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\Base\Criteria\Attribute\Iface List of search attribute items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
		$level = $this->context()->config()->get( 'mshop/customer/manager/sitemode', $level );

		return array_replace( parent::getSearchAttributes( $withsub ), $this->createAttributes( [
			'customer.id' => [
				'label' => 'ID',
				'internalcode' => 'id',
				'type' => 'int',
				'public' => false,
			],
			'customer.siteid' => [
				'label' => 'Customer site ID',
				'internalcode' => 'siteid',
				'public' => false,
			],
			'customer.code' => [
				'label' => 'Username',
				'internalcode' => 'code',
			],
			'customer.label' => [
				'label' => 'Label',
				'internalcode' => 'label',
			],
			'customer.salutation' => [
				'label' => 'Salutation',
				'internalcode' => 'salutation',
			],
			'customer.company' => [
				'label' => 'Company',
				'internalcode' => 'company',
			],
			'customer.vatid' => [
				'label' => 'Vat ID',
				'internalcode' => 'vatid',
			],
			'customer.title' => [
				'label' => 'Title',
				'internalcode' => 'title',
			],
			'customer.firstname' => [
				'label' => 'Firstname',
				'internalcode' => 'firstname',
			],
			'customer.lastname' => [
				'label' => 'Lastname',
				'internalcode' => 'lastname',
			],
			'customer.address1' => [
				'label' => 'Address part one',
				'internalcode' => 'address1',
			],
			'customer.address2' => [
				'label' => 'Address part two',
				'internalcode' => 'address2',
			],
			'customer.address3' => [
				'label' => 'Address part three',
				'internalcode' => 'address3',
			],
			'customer.postal' => [
				'label' => 'Postal',
				'internalcode' => 'postal',
			],
			'customer.city' => [
				'label' => 'City',
				'internalcode' => 'city',
			],
			'customer.state' => [
				'label' => 'State',
				'internalcode' => 'state',
			],
			'customer.languageid' => [
				'label' => 'Language',
				'internalcode' => 'langid',
			],
			'customer.countryid' => [
				'label' => 'Country',
				'internalcode' => 'countryid',
			],
			'customer.telephone' => [
				'label' => 'Telephone',
				'internalcode' => 'telephone',
			],
			'customer.telefax' => [
				'label' => 'Facsimile',
				'internalcode' => 'telefax',
			],
			'customer.mobile' => [
				'label' => 'Mobile number',
				'internalcode' => 'mobile',
			],
			'customer.email' => [
				'label' => 'E-mail',
				'internalcode' => 'email',
			],
			'customer.website' => [
				'label' => 'Web site',
				'internalcode' => 'website',
			],
			'customer.longitude' => [
				'label' => 'Longitude',
				'internalcode' => 'longitude',
				'public' => false,
			],
			'customer.latitude' => [
				'label' => 'Latitude',
				'internalcode' => 'latitude',
				'public' => false,
			],
			'customer.birthday' => [
				'label' => 'Birthday',
				'internalcode' => 'birthday',
			],
			'customer.status' => [
				'label' => 'Status',
				'internalcode' => 'status',
				'type' => 'int',
			],
			'customer.dateverified' => [
				'label' => 'Verification date',
				'internalcode' => 'vdate',
				'type' => 'date',
				'public' => false,
			],
			'customer.password' => [
				'label' => 'Password',
				'internalcode' => 'password',
				'public' => false,
			],
			'customer.ctime' => [
				'label' => 'Create date/time',
				'internalcode' => 'ctime',
				'type' => 'datetime',
				'public' => false,
			],
			'customer.mtime' => [
				'label' => 'Modify date/time',
				'internalcode' => 'mtime',
				'type' => 'datetime',
				'public' => false,
			],
			'customer.editor' => [
				'label' => 'Editor',
				'internalcode' => 'editor',
				'public' => false,
			],
			'customer:has' => [
				'code' => 'customer:has()',
				'internalcode' => ':site AND :key AND mcusli."id"',
				'internaldeps' => ['LEFT JOIN "mshop_customer_list" AS mcusli ON ( mcusli."parentid" = mcus."id" )'],
				'label' => 'Customer has list item, parameter(<domain>[,<list type>[,<reference ID>)]]',
				'type' => 'null',
				'public' => false,
				'function' => function( &$source, array $params ) use ( $level ) {
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
				}
			],
			'customer:prop' => [
				'code' => 'customer:prop()',
				'internalcode' => ':site AND :key AND mcuspr."id"',
				'internaldeps' => ['LEFT JOIN "mshop_customer_property" AS mcuspr ON ( mcuspr."parentid" = mcus."id" )'],
				'label' => 'Customer has property item, parameter(<property type>[,<language code>[,<property value>]])',
				'type' => 'null',
				'public' => false,
				'function' => function( &$source, array $params ) use ( $level ) {
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
				}
			],
		] ) );
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

		if( !$item->isModified() ) {
			return $this->object()->saveRefs( $item, $fetch );
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
			 * @since 2015.10
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
			 * @since 2015.10
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
			 * @since 2015.10
			 * @see mshop/customer/manager/insert/ansi
			 * @see mshop/customer/manager/update/ansi
			 * @see mshop/customer/manager/delete/ansi
			 * @see mshop/customer/manager/search/ansi
			 * @see mshop/customer/manager/count/ansi
			 */
			$path = 'mshop/customer/manager/newid';
			$id = $this->newId( $conn, $path );
		}

		return $this->object()->saveRefs( $item->setId( $id ), $fetch );
	}


	/**
	 * Returns the prefix for the item properties and search keys.
	 *
	 * @return string Prefix for the item properties and search keys
	 */
	protected function prefix() : string
	{
		return 'customer.';
	}


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
	 * @since 2015.10
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
	 * @since 2015.10
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
	 * @since 2015.10
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
	 * @since 2015.10
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/customer/manager/decorators/excludes
	 * @see mshop/customer/manager/decorators/global
	 */

	/** mshop/customer/manager/resource
	 * Name of the database connection resource to use
	 *
	 * You can configure a different database connection for each data domain
	 * and if no such connection name exists, the "db" connection will be used.
	 * It's also possible to use the same database connection for different
	 * data domains by configuring the same connection name using this setting.
	 *
	 * @param string Database connection name
	 * @since 2023.04
	 */

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
	 * @since 2015.10
	 */

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
	 * @since 2015.10
	 * @see mshop/customer/manager/insert/ansi
	 * @see mshop/customer/manager/update/ansi
	 * @see mshop/customer/manager/newid/ansi
	 * @see mshop/customer/manager/search/ansi
	 * @see mshop/customer/manager/count/ansi
	 */

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
	 * @since 2018.01
	 * @see mshop/locale/manager/sitelevel
	 */

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
	 * @since 2015.10
	 * @see mshop/customer/manager/insert/ansi
	 * @see mshop/customer/manager/update/ansi
	 * @see mshop/customer/manager/newid/ansi
	 * @see mshop/customer/manager/delete/ansi
	 * @see mshop/customer/manager/count/ansi
	 */

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
	 * @since 2015.10
	 * @see mshop/customer/manager/insert/ansi
	 * @see mshop/customer/manager/update/ansi
	 * @see mshop/customer/manager/newid/ansi
	 * @see mshop/customer/manager/delete/ansi
	 * @see mshop/customer/manager/search/ansi
	 */
}
