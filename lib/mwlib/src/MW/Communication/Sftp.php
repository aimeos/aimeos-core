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
	private $_config;

	public function __construct( array $config )
	{
		$this->_config = $config;
	}

	/**
	 * Sends request parameters to the providers interface.
	 *
	 * @param string $target Receivers address e.g. url.
	 * @param string $method Initial method (e.g. post or get)
	 * @param mixed $payload Update information whose format depends on the payment provider
	 * @return string response body of a http request
	 */
	public function transmit( $target, $method, $payload )
	{
		if( !file_exists( $payload ) )
		{
			$msg = sprintf( 'File "%1$s" does not exist.', $payload );
			throw new MW_Communication_Exception( $msg );
		}


		$destFile = trim( $this->_config['targetdir'], DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . basename( $payload );

		$sftp = new Net_SFTP( $target );

		$loginResult = $sftp->login( $this->_config['user'], $this->_config['password'] );
		if ( !$loginResult ) {
			throw new MW_Communication_Exception( 'Login failed!' );
		}

		$upload = $sftp->put( $destFile, $payload, $method );

		if ( $upload === false )
		{
			$msg = sprintf( 'Could not upload file: "%1$s"', $payload );
			throw new MW_Communication_Exception( $msg );
		}

		$sftp->disconnect();
	}
}