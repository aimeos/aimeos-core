<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2019
 * @package MW
 * @subpackage Observer
 */


namespace Aimeos\MW\Observer\Publisher;


/**
 * Default implementation of a publisher in the observer pattern
 *
 * @package MW
 * @subpackage Observer
 * @deprecated 2020.01 Use trait directly
 */
abstract class Base implements \Aimeos\MW\Observer\Publisher\Iface
{
	use Traits;
}
