<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
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
abstract class Base extends \Aimeos\MW\Common\Manager\Base implements \Aimeos\MW\Tree\Manager\Iface
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


	private $readOnly = false;


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
