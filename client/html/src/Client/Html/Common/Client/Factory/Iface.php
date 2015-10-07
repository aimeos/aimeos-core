<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Common\Client\Factory;


/**
 * Common factory interface for all HTML client classes.
 *
 * @package Client
 * @subpackage Html
 */
interface Iface
	extends \Aimeos\Client\Html\Iface
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
