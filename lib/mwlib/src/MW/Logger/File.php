<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
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
	private $stream;
	private $loglevel;
	private $facilities;


	/**
	 * Initializes the logger object.
	 *
	 * @param string $filename Log file name
	 * @param integer $priority Minimum priority for logging
	 * @param array|null $facilities Facilities for which messages should be logged
	 */
	public function __construct( $filename, $priority = \Aimeos\MW\Logger\Base::ERR, array $facilities = null )
	{
		if ( !$this->stream = fopen( $filename, 'a', false ) ) {
			throw new \Aimeos\MW\Logger\Exception( sprintf( 'Unable to open file "%1$s" for appending' ), $filename );
		}

		$this->loglevel = $priority;
		$this->facilities = $facilities;
	}


	/**
	 * Writes a message to the configured log facility.
	 *
	 * @param string|array|object $message Message text that should be written to the log facility
	 * @param integer $priority Priority of the message for filtering
	 * @param string $facility Facility for logging different types of messages (e.g. message, auth, user, changelog)
	 * @throws \Aimeos\MW\Logger\Exception If an error occurs in Zend_Log
	 * @see \Aimeos\MW\Logger\Base for available log level constants
	 */
	public function log( $message, $priority = \Aimeos\MW\Logger\Base::ERR, $facility = 'message' )
	{
		if( $priority <= $this->loglevel
			&& ( $this->facilities === null || in_array( $facility, $this->facilities ) ) )
		{
			$this->checkLogLevel( $priority );

			if( !is_scalar( $message ) ) {
				$message = json_encode( $message );
			}

			$message = '<' . $facility . '> ' . date( 'Y-m-d H:i:s' ) . ' ' . $priority . ' ' . $message;

			if ( false === fwrite( $this->stream, $message ) ) {
				throw new \Aimeos\MW\Logger\Exception( 'Unable to write to stream' );
			}
		}
	}
}