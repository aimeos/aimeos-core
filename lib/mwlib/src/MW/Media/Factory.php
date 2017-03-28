<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2017
 * @package MW
 * @subpackage Media
 */


namespace Aimeos\MW\Media;


/**
 * Creates a new media object.
 *
 * @package MW
 * @subpackage Media
 */
class Factory
{
	/**
	 * Creates a new media object.
	 *
	 * Options for the factory are:
	 * - image: Associative list of image related options
	 * - application: Associative list of application related options
	 *
	 * @param resource|string $file File resource, path to the file or file content
	 * @param array $options Associative list of options for configuring the media class
	 * @return \Aimeos\MW\Media\Iface Media object
	 */
	public static function get( $file, array $options = [] )
	{
		$content = $file;

		if( @is_resource( $file ) && ( $content = stream_get_contents( $file ) ) === false ) {
			throw new \Aimeos\MW\Media\Exception( sprintf( 'Unable to read from stream' ) );
		}

		if( @is_file( $file ) && ( $content = @file_get_contents( $file ) ) === false ) {
			throw new \Aimeos\MW\Media\Exception( sprintf( 'Unable to read from file "%1$s"', $file ) );
		}


		$finfo = new \finfo( FILEINFO_MIME_TYPE );
		$mimetype = $finfo->buffer( $content );
		$mime = explode( '/', $mimetype );

		$type = ( $mime[0] === 'image' ? 'Image' : 'Application' );
		$name = ( isset( $options[ $mime[0] ]['name'] ) ? ucfirst( $options[ $mime[0] ]['name'] ) : 'Standard' );


		if( ctype_alnum( $name ) === false )
		{
			$classname = is_string( $name ) ? '\\Aimeos\\MW\\Media\\' . $name : '<not a string>';
			throw new \Aimeos\MW\Container\Exception( sprintf( 'Invalid characters in class name "%1$s"', $classname ) );
		}

		$iface = '\\Aimeos\\MW\\Media\\Iface';
		$classname = '\\Aimeos\\MW\\Media\\' . $type . '\\' . $name;

		if( class_exists( $classname ) === false ) {
			throw new \Aimeos\MW\Media\Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$object = new $classname( $content, $mimetype, $options );

		if( !( $object instanceof $iface ) ) {
			throw new \Aimeos\MW\Media\Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $iface ) );
		}

		return $object;
	}
}
