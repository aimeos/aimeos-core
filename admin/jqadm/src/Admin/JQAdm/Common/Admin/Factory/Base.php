<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Client
 * @subpackage JQAdm
 */


namespace Aimeos\Admin\JQAdm\Common\Admin\Factory;


/**
 * Base class for JQAdm clients
 *
 * @package Client
 * @subpackage JQAdm
 */
abstract class Base
	extends \Aimeos\Admin\JQAdm\Base
	implements \Aimeos\Admin\JQAdm\Common\Admin\Factory\Iface
{
	/**
	 * Initializes the object instance
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 * @param array $templatePaths Associative list of the file system paths to the core or the extensions as key
	 * 	and a list of relative paths inside the core or the extension as values
	 * @param \Aimeos\Admin\JQAdm\Iface $client Client object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, array $templatePaths )
	{
		parent::__construct( $context, $templatePaths );
	}
}