<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Logger
 */


/**
 * Base logger class defining required error level constants
 *
 * @package MW
 * @subpackage Logger
 */
abstract class MW_Logger_Abstract
{
	/**
	 * Emergency (0): system is unusable
	 */
	const EMERG = 0;

	/**
	 * Alert (1): action must be taken immediately
	 */
	const ALERT = 1;

	/**
	 * Critical (2): critical conditions
	 */
	const CRIT = 2;

	/**
	 * Error (3): error conditions
	 */
	const ERR = 3;

	/**
	 * Warning (4): warning conditions
	 */
	const WARN = 4;

	/**
	 * Notice (5): normal but significant condition
	 */
	const NOTICE = 5;

	/**
	 * Informational (6): informational messages
	 */
	const INFO = 6;

	/**
	 * Debug (7): debug messages
	 */
	const DEBUG = 7;


	protected function _checkLogLevel( $level )
	{
		switch( $level )
		{
			case MW_Logger_Abstract::EMERG:
			case MW_Logger_Abstract::ALERT:
			case MW_Logger_Abstract::CRIT:
			case MW_Logger_Abstract::ERR:
			case MW_Logger_Abstract::WARN:
			case MW_Logger_Abstract::NOTICE:
			case MW_Logger_Abstract::INFO:
			case MW_Logger_Abstract::DEBUG:
				break;
			default:
				throw new MW_Logger_Exception( sprintf( 'Invalid log level constant "%1$d"', $level ) );
		}
	}
}
