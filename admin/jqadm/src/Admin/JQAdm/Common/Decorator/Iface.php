<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Admin
 * @subpackage JQAdm
 */


namespace Aimeos\Admin\JQAdm\Common\Decorator;


/**
 * Decorator interface for JQAdm clients.
 *
 * @package Admin
 * @subpackage JQAdm
 */
interface Iface
	extends \Aimeos\Admin\JQAdm\Iface
{
	/**
	 * Initializes a new client decorator object.
	 *
	 * @param \Aimeos\Admin\JQAdm\Iface $client Admin object
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 * @param array $templatePaths Associative list of the file system paths to the core or the extensions as key
	 * 	and a list of relative paths inside the core or the extension as values
	 */
	public function __construct( \Aimeos\Admin\JQAdm\Iface $client,
		\Aimeos\MShop\Context\Item\Iface $context, array $templatePaths );
}