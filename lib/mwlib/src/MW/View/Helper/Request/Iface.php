<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	public function transform() : Iface;

	/**
	 * Returns the client IP address.
	 *
	 * @return string|null Client IP address
	 */
	public function getClientAddress() : ?string;

	/**
	 * Returns the current page or route name
	 *
	 * @return string|null Current page or route name
	 */
	public function getTarget() : ?string;
}
