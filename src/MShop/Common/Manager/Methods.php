<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2023
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager;


/**
 * Method trait for managers
 *
 * @package MShop
 * @subpackage Common
 */
trait Methods
{
	private ?\Aimeos\MShop\Common\Manager\Iface $object = null;
	private array $filterFcn = [];
	private string $domain;
	private string $subpath;


	protected function init()
	{
		$parts = explode( '\\', strtolower( get_class( $this ) ) );
		array_shift( $parts ); array_shift( $parts ); // remove "Aimeos\MShop"
		array_pop( $parts );

		$this->domain = array_shift( $parts ) ?: '';
		array_shift( $parts ); // remove "manager"
		$this->subpath = join( '/', $parts );
	}


	/**
	 * Adds a filter callback for an item type
	 *
	 * @param string $iface Interface name of the item to apply the filter to
	 * @param \Closure $fcn Anonymous function receiving the item to check as first parameter
	 */
	public function addFilter( string $iface, \Closure $fcn )
	{
		if( !isset( $this->filterFcn[$iface] ) ) {
			$this->filterFcn[$iface] = [];
		}

		$this->filterFcn[$iface][] = $fcn;
	}


	/**
	 * Returns the class names of the manager and used decorators.
	 *
	 * @return array List of class names
	 */
	public function classes() : array
	{
		return [get_class( $this )];
	}


	/**
	 * Injects the reference of the outmost object
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $object Reference to the outmost manager or decorator
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function setObject( \Aimeos\MShop\Common\Manager\Iface $object ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$this->object = $object;
		return $this;
	}


	/**
	 * Applies the filters for the item type to the item
	 *
	 * @param object $item Item to apply the filter to
	 * @return object|null Object if the item should be used, null if not
	 */
	protected function applyFilter( $item )
	{
		foreach( $this->filterFcn as $iface => $fcnList )
		{
			if( is_object( $item ) && $item instanceof $iface )
			{
				foreach( $fcnList as $fcn )
				{
					if( $fcn( $item ) === null ) {
						return null;
					}
				}
			}
		}

		return $item;
	}


	/**
	 * Creates the criteria attribute items from the list of entries
	 *
	 * @param array $list Associative array of code as key and array with properties as values
	 * @return \Aimeos\Base\Criteria\Attribute\Standard[] List of criteria attribute items
	 */
	protected function createAttributes( array $list ) : array
	{
		$attr = [];

		foreach( $list as $key => $fields )
		{
			$fields['code'] = $fields['code'] ?? $key;
			$attr[$key] = new \Aimeos\Base\Criteria\Attribute\Standard( $fields );
		}

		return $attr;
	}


	/**
	 * Returns the full configuration key for the passed last part
	 *
	 * @param string $name Configuration last part
	 * @return string Full configuration key
	 */
	protected function getConfigKey( string $name ) : string
	{
		return 'mshop/' . $this->domain . '/manager/' . ( $this->subpath ? $this->subpath . '/' : '' ) . $name;
	}


	/**
	 * Returns the manager domain
	 *
	 * @return string Manager domain e.g. "product"
	 */
	protected function getDomain() : string
	{
		return $this->domain;
	}


	/**
	 * Returns the manager path
	 *
	 * @return string Manager path e.g. "product/lists/type"
	 */
	protected function getManagerPath() : string
	{
		return $this->domain . ( $this->subpath ? '/' . $this->subpath : '' );
	}


	/**
	 * Returns the attribute helper functions for searching defined by the manager.
	 *
	 * @param \Aimeos\Base\Criteria\Attribute\Iface[] $attributes List of search attribute items
	 * @return array Associative array of attribute code and helper function
	 */
	protected function getSearchFunctions( array $attributes ) : array
	{
		$list = [];
		$iface = \Aimeos\Base\Criteria\Attribute\Iface::class;

		foreach( $attributes as $key => $item )
		{
			if( $item instanceof $iface ) {
				$list[$item->getCode()] = $item->getFunction();
			} else if( isset( $item['code'] ) ) {
				$list[$item['code']] = $item['function'] ?? null;
			} else {
				throw new \Aimeos\MShop\Exception( sprintf( 'Invalid attribute at position "%1$d"', $key ) );
			}
		}

		return $list;
	}


	/**
	 * Returns the item search key for the passed name
	 *
	 * @return string Item prefix e.g. "product.lists.type.id"
	 */
	protected function getSearchKey( string $name = '' ) : string
	{
		return $this->domain . ( $this->subpath ? '.' . $this->subpath : '' ) . ( $name ? '.' . $name : '' );
	}


	/**
	 * Returns the attribute translations for searching defined by the manager.
	 *
	 * @param \Aimeos\Base\Criteria\Attribute\Iface[] $attributes List of search attribute items
	 * @return array Associative array of attribute code and internal attribute code
	 */
	protected function getSearchTranslations( array $attributes ) : array
	{
		$translations = [];
		$iface = \Aimeos\Base\Criteria\Attribute\Iface::class;

		foreach( $attributes as $key => $item )
		{
			if( $item instanceof $iface ) {
				$translations[$item->getCode()] = $item->getInternalCode();
			} else if( isset( $item['code'] ) ) {
				$translations[$item['code']] = $item['internalcode'];
			} else {
				throw new \Aimeos\MShop\Exception( sprintf( 'Invalid attribute at position "%1$d"', $key ) );
			}
		}

		return $translations;
	}


	/**
	 * Returns the attribute types for searching defined by the manager.
	 *
	 * @param \Aimeos\Base\Criteria\Attribute\Iface[] $attributes List of search attribute items
	 * @return array Associative array of attribute code and internal attribute type
	 */
	protected function getSearchTypes( array $attributes ) : array
	{
		$types = [];
		$iface = \Aimeos\Base\Criteria\Attribute\Iface::class;

		foreach( $attributes as $key => $item )
		{
			if( $item instanceof $iface ) {
				$types[$item->getCode()] = $item->getInternalType();
			} else if( isset( $item['code'] ) ) {
				$types[$item['code']] = $item['internaltype'];
			} else {
				throw new \Aimeos\MShop\Exception( sprintf( 'Invalid attribute at position "%1$d"', $key ) );
			}
		}

		return $types;
	}


	/**
	 * Returns the name of the used table
	 *
	 * @return string Table name e.g. "mshop_product_lists_type"
	 */
	protected function getTable() : string
	{
		return 'mshop_' . $this->domain . ( $this->subpath ? '_' . str_replace( '/', '_', $this->subpath ) : '' );
	}


	/**
	 * Returns the outmost decorator of the decorator stack
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Outmost decorator object
	 */
	protected function object() : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this->object ?? $this;
	}
}
