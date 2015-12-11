<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Client
 * @subpackage JQAdm
 */


namespace Aimeos\Client\JQAdm\Common\Client\Factory;


/**
 * Common factory interface for all JQAdm client classes.
 *
 * @package Client
 * @subpackage JQAdm
 */
interface Iface
	extends \Aimeos\Client\JQAdm\Iface
{
	/**
	 * Initializes the class instance.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 * @param array $templatePaths Associative list of the file system paths to the core or the extensions as key
	 * 	and a list of relative paths inside the core or the extension as values
	 * @return void
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, array $templatePaths );
}
