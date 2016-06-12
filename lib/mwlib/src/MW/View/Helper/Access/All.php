<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Access;


/**
 * View helper class for checking access levels
 *
 * @package MW
 * @subpackage View
 */
class All extends \Aimeos\MW\View\Helper\Base implements Iface
{
	/**
	 * Checks the access level of the current user
	 *
	 * @param string|array $groups Group names that are allowed
	 * @return boolean True if access is allowed, false if not
	 */
	public function transform( $groups )
	{
		return true;
	}
}
