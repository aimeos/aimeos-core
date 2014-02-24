<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Media
 */


/**
 * Creates a new media object.
 *
 * @package MW
 * @subpackage Media
 */
class MW_Media_Factory
{
	/**
	 * Creates a new media object.
	 *
	 * Options for the factory are:
	 * - file: "file" command to determine the mime type (default: 'file -b --mime-type %1$s')
	 * - image: Associative list of image related options
	 * - application: Associative list of application related options
	 *
	 * @param string|null $filename Path to media file or null for new files
	 * @param array $options Associative list of options for configuring the media class
	 * @return MW_Media_Interface Media object
	 */
	public static function get( $filename, array $options = array() )
	{
		$value = 0;
		$msg = array();
		$cmd = ( isset( $options['file'] ) ? $options['file'] : 'file -b --mime-type %1$s' );

		$cmdline = sprintf( $cmd, escapeshellarg( $filename ) );
		$mimetype = exec( $cmdline, $msg, $value );

		if( $value != 0 ) {
			throw new MW_Media_Exception( sprintf( 'Error executing "%1$s": %2$s', $cmdline, print_r( $msg, true ) ) );
		}

		$mimeparts = explode( '/', $mimetype );

		switch( $mimeparts[0] )
		{
			case 'image':
				return new MW_Media_Image_Default( $filename, $mimetype, $options );
		}

		return new MW_Media_Application_Default( $filename, $mimetype, $options );
	}
}
