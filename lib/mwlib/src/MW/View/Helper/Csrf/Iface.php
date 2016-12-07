<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Csrf;


/**
 * View helper class for retrieving CSRF tokens.
 *
 * @package MW
 * @subpackage View
 */
interface Iface extends \Aimeos\MW\View\Helper\Iface
{
	/**
	 * Returns the CSRF partial object.
	 *
	 * @return \Aimeos\MW\View\Helper\Iface CSRF partial object
	 */
	public function transform();

	/**
	 * Returns the CSRF token name.
	 *
	 * @return string CSRF token name
	 */
	public function name();

	/**
	 * Returns the CSRF token value.
	 *
	 * @return string CSRF token value
	 */
	public function value();

	/**
	 * Returns the HTML form field for the CSRF token.
	 *
	 * @return string HTML form field code
	 */
	public function formfield();
}
