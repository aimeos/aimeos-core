<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2023
 * @package MW
 * @subpackage Tree
 */


namespace Aimeos\MW\Tree\Manager;


/**
 * Abstract tree manager class with basic methods.
 *
 * @package MW
 * @subpackage Tree
 */
abstract class Base implements \Aimeos\MW\Tree\Manager\Iface
{
	/**
	 * Returns only the requested node
	 */
	const LEVEL_ONE = 1;

	/**
	 * Returns the requested node and its children
	 */
	const LEVEL_LIST = 2;

	/**
	 * Returns all subnodes including the requested one
	 */
	const LEVEL_TREE = 3;


	private bool $readOnly = false;


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
				throw new \Aimeos\MW\Tree\Exception( sprintf( 'Invalid attribute at position "%1$d"', $key ) );
			}
		}

		return $list;
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
				throw new \Aimeos\MW\Tree\Exception( sprintf( 'Invalid attribute at position "%1$d"', $key ) );
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
				throw new \Aimeos\MW\Tree\Exception( sprintf( 'Invalid attribute at position "%1$d"', $key ) );
			}
		}

		return $types;
	}


	/**
	 * Checks, whether a tree is read only.
	 *
	 * @return bool True if tree is read-only, false if not
	 */
	public function isReadOnly() : bool
	{
		return $this->readOnly;
	}


	/**
	 * Sets this manager to read only.
	 *
	 * @param bool $flag True if tree is read-only, false if not
	 */
	protected function setReadOnly( bool $flag = true ) : Iface
	{
		$this->readOnly = (bool) $flag;
		return $this;
	}
}
