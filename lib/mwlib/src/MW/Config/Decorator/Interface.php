<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Config
 * @version $Id: Interface.php 16606 2012-10-19 12:50:23Z nsendetzky $
 */


/**
 * Decorator interface for configuration setting classes
 *
 * @package MW
 * @subpackage Config
 */
interface MW_Config_Decorator_Interface extends MW_Config_Interface
{
	/**
	 * Initializes the decorator.
	 *
	 * @param MW_Config_Interface $object Config object or decorator
	 */
	public function __construct( MW_Config_Interface $object );
}
