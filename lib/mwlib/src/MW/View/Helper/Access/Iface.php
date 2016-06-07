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
interface Iface extends \Aimeos\MW\View\Helper\Iface
{
	/**
	 * Checks the access level
	 *
	 * @param string|array $groups Group names that are allowed
	 * @return boolean True if access is allowed, false if not
	 */
	public function transform( $groups );
}
