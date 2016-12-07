<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
	 * @return boolean True if tree is read-only, false if not
	 */
	public function isReadOnly()
	{
		return $this->readOnly;
	}


	/**
	 * Sets this manager to read only.
	 *
	 * @param boolean $flag True if tree is read-only, false if not
	 */
	protected function setReadOnly( $flag = true )
	{
		$this->readOnly = (bool) $flag;
	}
}
