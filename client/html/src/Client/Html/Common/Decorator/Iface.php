<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Common\Decorator;


/**
 * Decorator interface for html clients.
 *
 * @package Client
 * @subpackage Html
 */
interface Iface
	extends \Aimeos\Client\Html\Iface
{
	/**
	 * Initializes a new client decorator object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 * @param array $templatePaths Associative list of the file system paths to the core or the extensions as key
	 * 	and a list of relative paths inside the core or the extension as values
	 * @param \Aimeos\Client\Html\Iface $client Client object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, array $templatePaths, \Aimeos\Client\Html\Iface $client );
}