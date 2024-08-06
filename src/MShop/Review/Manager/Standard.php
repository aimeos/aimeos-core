<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2024
 * @package MShop
 * @subpackage Review
 */


namespace Aimeos\MShop\Review\Manager;


/**
 * Default review manager implementation
 *
 * @package MShop
 * @subpackage Review
 */
class Standard
	extends \Aimeos\MShop\Common\Manager\Base
	implements \Aimeos\MShop\Review\Manager\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	/** mshop/review/manager/name
	 * Class name of the used review manager implementation
	 *
	 * Each default manager can be replace by an alternative imlementation.
	 * To use this implementation, you have to set the last part of the class
	 * name as configuration value so the manager factory knows which class it
	 * has to instantiate.
	 *
	 * For example, if the name of the default class is
	 *
	 *  \Aimeos\MShop\Review\Manager\Standard
	 *
	 * and you want to replace it with your own version named
	 *
	 *  \Aimeos\MShop\Review\Manager\Mymanager
	 *
	 * then you have to set the this configuration option:
	 *
	 *  mshop/review/manager/name = Mymanager
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
	 * @since 2020.10
	 * @category Developer
	 */

	/** mshop/review/manager/decorators/excludes
	 * Excludes decorators added by the "common" option from the review manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to remove a decorator added via
	 * "mshop/common/manager/decorators/default" before they are wrapped
	 * around the review manager.
	 *
	 *  mshop/review/manager/decorators/excludes = array( 'decorator1' )
	 *
	 * This would remove the decorator named "decorator1" from the list of
	 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
	 * "mshop/common/manager/decorators/default" for the review manager.
	 *
	 * @param array List of decorator names
	 * @since 2020.10
	 * @category Developer
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/review/manager/decorators/global
	 * @see mshop/review/manager/decorators/local
	 */

	/** mshop/review/manager/decorators/global
	 * Adds a list of globally available decorators only to the review manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap global decorators
	 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the review
	 * manager.
	 *
	 *  mshop/review/manager/decorators/global = array( 'decorator1' )
	 *
	 * This would add the decorator named "decorator1" defined by
	 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the
	 * review manager.
	 *
	 * @param array List of decorator names
	 * @since 2020.10
	 * @category Developer
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/review/manager/decorators/excludes
	 * @see mshop/review/manager/decorators/local
	 */

	/** mshop/review/manager/decorators/local
	 * Adds a list of local decorators only to the review manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap local decorators
	 * ("\Aimeos\MShop\Review\Manager\Decorator\*") around the review
	 * manager.
	 *
	 *  mshop/review/manager/decorators/local = array( 'decorator2' )
	 *
	 * This would add the decorator named "decorator2" defined by
	 * "\Aimeos\MShop\Review\Manager\Decorator\Decorator2" only to the
	 * review manager.
	 *
	 * @param array List of decorator names
	 * @since 2020.10
	 * @category Developer
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/review/manager/decorators/excludes
	 * @see mshop/review/manager/decorators/global
	 */


	private array $searchConfig = array(
		'customerid' => array(
			'code' => 'review.customerid',
			'internalcode' => 'mrev."customerid"',
			'label' => 'Customer ID',
			'public' => false,
		),
		'ordprodid' => array(
			'code' => 'review.orderproductid',
			'internalcode' => 'mrev."ordprodid"',
			'label' => 'Order product ID',
			'public' => false,
		),
		'domain' => array(
			'code' => 'review.domain',
			'internalcode' => 'mrev."domain"',
			'label' => 'Domain',
		),
		'refid' => array(
			'code' => 'review.refid',
			'internalcode' => 'mrev."refid"',
			'label' => 'ID from the referenced domain',
		),
		'name' => array(
			'code' => 'review.name',
			'internalcode' => 'mrev."name"',
			'label' => 'Name',
		),
		'comment' => array(
			'code' => 'review.comment',
			'internalcode' => 'mrev."comment"',
			'label' => 'Comment',
		),
		'response' => array(
			'code' => 'review.response',
			'internalcode' => 'mrev."response"',
			'label' => 'Response',
		),
		'rating' => array(
			'code' => 'review.rating',
			'internalcode' => 'mrev."rating"',
			'label' => 'Rating',
			'type' => 'int',
		),
		'status' => array(
			'code' => 'review.status',
			'internalcode' => 'mrev."status"',
			'label' => 'Review status',
			'type' => 'int',
		),
	);


	/**
	 * Counts the number items that are available for the values of the given key.
	 *
	 * @param \Aimeos\Base\Criteria\Iface $search Search criteria
	 * @param array|string $key Search key or list of keys to aggregate items for
	 * @param string|null $value Search key for aggregating the value column
	 * @param string|null $type Type of the aggregation, empty string for count or "sum" or "avg" (average)
	 * @return \Aimeos\Map List of the search keys as key and the number of counted items as value
	 */
	public function aggregate( \Aimeos\Base\Criteria\Iface $search, $key, string $value = null, string $type = null ) : \Aimeos\Map
	{
		/** mshop/review/manager/aggregate/mysql
		 * Counts the number of records grouped by the values in the key column and matched by the given criteria
		 *
		 * @see mshop/review/manager/aggregate/ansi
		 */

		/** mshop/review/manager/aggregate/ansi
		 * Counts the number of records grouped by the values in the key column and matched by the given criteria
		 *
		 * Groups all records by the values in the key column and counts their
		 * occurence. The matched records can be limited by the given criteria
		 * from the review database. The records must be from one of the sites
		 * that are configured via the context item. If the current site is part
		 * of a tree of sites, the statement can count all records from the
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
		 * This statement doesn't return any records. Instead, it returns pairs
		 * of the different values found in the key column together with the
		 * number of records that have been found for that key values.
		 *
		 * The SQL statement should conform to the ANSI standard to be
		 * compatible with most relational database systems. This also
		 * includes using double quotes for table and column names.
		 *
		 * @param string SQL statement for aggregating review items
		 * @since 2020.10
		 * @category Developer
		 * @see mshop/review/manager/insert/ansi
		 * @see mshop/review/manager/update/ansi
		 * @see mshop/review/manager/newid/ansi
		 * @see mshop/review/manager/delete/ansi
		 * @see mshop/review/manager/search/ansi
		 * @see mshop/review/manager/count/ansi
		 */
		$cfgkey = 'mshop/review/manager/aggregate';
		$cfgkey .= ( $type === 'rate' ? 'rate' : '' );

		return $this->aggregateBase( $search, $key, $cfgkey, [], $value, $type );
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Review\Item\Iface New review item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$values['review.siteid'] = $values['review.siteid'] ?? $this->context()->locale()->getSiteId();
		return new \Aimeos\MShop\Review\Item\Standard( 'review.', $values );
	}


	/**
	 * Creates a filter object.
	 *
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @param bool $site TRUE for adding site criteria to limit items by the site of related items
	 * @return \Aimeos\Base\Criteria\Iface Returns the filter object
	 */
	public function filter( ?bool $default = false, bool $site = false ) : \Aimeos\Base\Criteria\Iface
	{
		return $this->filterBase( 'review', $default );
	}


	/**
	 * Returns the additional column/search definitions
	 *
	 * @return array Associative list of column names as keys and items implementing \Aimeos\Base\Criteria\Attribute\Iface
	 */
	public function getSaveAttributes() : array
	{
		return $this->createAttributes( $this->searchConfig );
	}


	/**
	 * Returns the prefix for the item properties and search keys.
	 *
	 * @return string Prefix for the item properties and search keys
	 */
	protected function getPrefix() : string
	{
		return 'review.';
	}
}
