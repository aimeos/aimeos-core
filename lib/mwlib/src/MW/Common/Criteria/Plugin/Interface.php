<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Common
 * @version $Id: Interface.php 16606 2012-10-19 12:50:23Z nsendetzky $
 */


/**
 * Interface for criteria plugin objects.
 *
 * @package MW
 * @subpackage Common
 */
interface MW_Common_Criteria_Plugin_Interface
{
	/**
	 * Translates a value to another one.
	 *
	 * @param mixed $value Value to translate
	 * @return mixed Translated value
	 */
	public function translate( $value );
}
