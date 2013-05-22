<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Logger
 */


/**
 * Log messages.
 *
 * @package MW
 * @subpackage Logger
 */
class MW_Logger_File extends MW_Logger_Abstract implements MW_Logger_Interface
{
	/**
	 * @var priorities mapping from number to name
	 */
	private $_priorities = array(
		0 => 'EMERG',
		1 => 'ALERT',
		2 => 'CRIT',
		3 => 'ERR',
		4 => 'WARN',
		5 => 'NOTICE',
		6 => 'INFO',
		7 => 'DEBUG'
	);

	/**
	 * @var filters
	 */
	private $_loglevel = MW_Logger_Abstract::ERR;

	/**
	 * @var stream handle
	 */
	private $_stream;


	/**
	 * Initializes the logger object.
	 *
	 * @param string $prefix Prefix specified by site code
	 * @param integer $priority Default priority
	 */
	public function __construct( $prefix, $filterPriority )
	{
		if ( !$this->_stream = @fopen( $prefix, 'a', false ) ) {
			throw new MW_Logger_Exception( sprintf( '"%1$s" cannot be opened with mode "a"' ), $prefix );
		}

		$this->_loglevel = $filterPriority;
	}


	/**
	 * Writes a message to the configured log facility.
	 *
	 * @param string $message Message text that should be written to the log facility
	 * @param integer $priority Priority of the message for filtering
	 * @param string $facility Facility for logging different types of messages (e.g. message, auth, user, changelog)
	 * @throws MW_Logger_Exception If an error occurs in Zend_Log
	 * @see MW_Logger_Abstract for available log level constants
	 */
	public function log( $message, $priority = MW_Logger_Abstract::ERR, $facility = 'message' )
	{
		if( $priority <= $this->_loglevel )
		{

			$this->_checkLogLevel( $priority );

			if( !is_scalar( $message ) ) {
				$message = json_encode( $message );
			}

			$formatedMsg = $this->_format( '<' . $facility . '> ' . $message, $priority );

			if ( false === @fwrite( $this->_stream, $formatedMsg ) ) {
				throw new MW_Logger_Exception( 'Unable to write to stream' );
			}
		}

	}


	/**
	 * Formatting message.
	 *
	 * @param string $message Message to log
	 * @param integer $priority Priority of the message
	 * @param string $format Format for the message
	 */
	protected function _format( $message, $priority, $format = MW_Logger_Abstract::DEFAULT_FORMAT )
	{
		$msg = array(
			'timestamp' => date( 'c' ),
			'message' => $message,
			'priority' => $priority,
			'priorityName' => $this->_priorities[ $priority ]
		);

		foreach ( $msg as $name => $value ) {
			$format = str_replace( '%'.$name.'%', $value, $format );
		}

		return $format;
	}
}