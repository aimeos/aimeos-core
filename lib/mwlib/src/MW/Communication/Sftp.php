<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Communication
 */


/**
 * Common class for general SFTP transfer.
 *
 * @package MW
 * @subpackage Communication
 */
class MW_Communication_Sftp implements MW_Communication_Interface
{
	private $_sftp;
	private $_config;


	/**
	 * Initializes a new connection to the remote host with sftp.
	 *
	 * @param array $config Configuration with required connection datas
	 */
	public function __construct( array $config )
	{
		$this->_config = $config;
		$this->_sftp = new Net_SFTP( $config['host'] );

		if( $this->_sftp->login( $config['username'], $config['password'] ) === false ) {
			throw new MW_Communication_Exception( sprintf( 'Login to "%1$s" with user "%2$s" failed', $config['host'], $config['username'] ) );
		}
	}


	/**
	 * Disconnects from remote host.
	 */
	public function __destruct()
	{
		$this->_sftp->disconnect();
	}


	/**
	 * Sends request parameters to the providers interface.
	 *
	 * @param string $target Target directory of the remote host
	 * @param string $method Initial method:<br>
	 *		NET_SFTP_LOCAL_FILE reads data from local file,<br>
	 *		NET_SFTP_STRING reads data from string,<br>
	 *		NET_SFTP_RESUME resumes an upload
	 * @param mixed $payload Filename or string to transmit to remote host
	 */
	public function transmit( $target, $method, $payload )
	{
		if ( $this->_sftp->put( $target, $payload, $method ) === false )
		{
			$msg = sprintf( 'Could not upload payload to "%2$s:%3$s"', $this->_config['host'], $target );
			throw new MW_Communication_Exception( $msg );
		}
	}
}
