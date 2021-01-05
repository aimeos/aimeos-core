<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage Logger
 */


namespace Aimeos\MW\Logger;


/**
 * Log messages.
 *
 * @package MW
 * @subpackage Logger
 */
class File extends \Aimeos\MW\Logger\Base implements \Aimeos\MW\Logger\Iface
{
	private $loglevel;
	private $filename;
	private $facilities;
	private $requestid;


	/**
	 * Initializes the logger object.
	 *
	 * @param string $filename Log file name
	 * @param int $priority Minimum priority for logging
	 * @param string[]|null $facilities Facilities for which messages should be logged
	 * @param string|null $requestid Unique identifier to identify multiple log entries for the same request faster
	 */
	public function __construct( string $filename, int $priority = Base::ERR, array $facilities = null, string $requestid = null )
	{
		$this->filename = $filename;
		$this->loglevel = $priority;
		$this->facilities = $facilities;

		if( $requestid === null ) {
			$requestid = substr( md5( php_uname( 'n' ) . getmypid() . date( 'Y-m-d H:i:s' ) ), 24 );
		}
		$this->requestid = $requestid;
	}


	/**
	 * Writes a message to the configured log facility.
	 *
	 * @param string|array|object $message Message text that should be written to the log facility
	 * @param int $prio Priority of the message for filtering
	 * @param string $facility Facility for logging different types of messages (e.g. message, auth, user, changelog)
	 * @return \Aimeos\MW\Logger\Iface Logger object for method chaining
	 * @throws \Aimeos\MW\Logger\Exception If an error occurs in Zend_Log
	 * @see \Aimeos\MW\Logger\Base for available log level constants
	 */
	public function log( $message, int $prio = Base::ERR, string $facility = 'message' ) : Iface
	{
		if( $prio <= $this->loglevel && ( $this->facilities === null || in_array( $facility, $this->facilities ) ) )
		{
			$level = $this->getLogLevel( $prio );

			if( !is_scalar( $message ) ) {
				$message = json_encode( $message );
			}

			$date = date( 'Y-m-d H:i:s' );
			$msg = '[' . $date . '] <' . $facility . '> [' . $level . '] [' . $this->requestid . '] ' . $message . PHP_EOL;

			if( file_put_contents( $this->filename, $msg, FILE_APPEND ) === false ) {
				throw new \Aimeos\MW\Logger\Exception( sprintf( 'Unable to write to file "%1$s', $this->filename ) );
			}
		}

		return $this;
	}
}
