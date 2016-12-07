<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Request;


/**
 * View helper class for accessing request data.
 *
 * @package MW
 * @subpackage View
 */
interface Iface extends \Aimeos\MW\View\Helper\Iface, \Psr\Http\Message\ServerRequestInterface
{
	/**
	 * Returns the request view helper.
	 *
	 * @return \Aimeos\MW\View\Helper\Request\Iface Request view helper
	 */
	public function transform();

	/**
	 * Returns the client IP address.
	 *
	 * @return string Client IP address
	 */
	public function getClientAddress();

	/**
	 * Returns the current page or route name
	 *
	 * @return string|null Current page or route name
	 */
	public function getTarget();
}
