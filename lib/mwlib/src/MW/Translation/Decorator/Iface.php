<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Translation
 */


/**
 * Decorator interface for translation classes
 *
 * @package MW
 * @subpackage Translation
 */
interface MW_Translation_Decorator_Iface extends MW_Translation_Iface
{
	/**
	 * Initializes the decorator.
	 *
	 * @param MW_Translation_Iface $object Translation object or decorator
	 * @return void
	 */
	public function __construct( MW_Translation_Iface $object );
}
