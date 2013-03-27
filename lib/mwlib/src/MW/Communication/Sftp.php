<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Communication
 */


/**
 * Common class for communication with delivery and payment providers.
 *
 * @package MW
 * @subpackage Communication
 */
class MW_Communication_Sftp implements MW_Communication_Interface
{
	private $_sftp;


	/**
	 * Initializes a new connection to the remote host with sftp.
	 *
	 * @param array $config Configuration with required connection datas
	 */
	public function __construct( array $config )
	{
		register_shutdown_function( array( $this, '__destruct' ) );

		$this->_sftp = new Net_SFTP( $config['remotehost'] );
		$loginResult = $this->_sftp->login( $config['user'], $config['password'] );
		if ( !$loginResult ) {
			throw new MW_Communication_Exception( 'Login failed!' );
		}
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
		if( $method !== 'NET_SFTP_STRING' && !file_exists( $payload ) )
		{
			$msg = sprintf( 'File "%1$s" does not exist.', $payload );
			throw new MW_Communication_Exception( $msg );
		}

		$destFile = trim( $target, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . basename( $payload );

		$upload = $sftp->put( $destFile, $payload, $method );

		if ( $upload === false )
		{
			$msg = sprintf( 'Could not upload file: "%1$s"', $payload );
			throw new MW_Communication_Exception( $msg );
		}
	}


	/**
	 * Disconnects from remote host.
	 *
	 */
	public function __destruct()
	{
		$this->_sftp->disconnect();
	}
}