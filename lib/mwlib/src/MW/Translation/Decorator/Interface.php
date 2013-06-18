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
interface MW_Translation_Decorator_Interface extends MW_Translation_Interface
{
	/**
	 * Initializes the decorator.
	 *
	 * @param MW_Translation_Interface $object Translation object or decorator
	 * @param MW_Config_Interface $config Configuration object
	 */
	public function __construct( MW_Translation_Interface $object, MW_Config_Interface $config );
}
