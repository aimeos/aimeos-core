<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	 * @param \Psr\Http\Message\StreamInterface|resource|string $file File resource, path to the file or file content
	 * @param array $options Associative list of options for configuring the media class
	 * @return \Aimeos\MW\Media\Iface Media object
	 */
	public static function get( $file, array $options = [] )
	{
		if( is_resource( $file ) )
		{
			if( ( $content = stream_get_contents( $file ) ) === false ) {
				throw new \Aimeos\MW\Media\Exception( sprintf( 'Unable to read from stream' ) );
			}
		}
		elseif( $file instanceof \Psr\Http\Message\StreamInterface )
		{
			$content = $file->getContents();
		}
		elseif( is_string( $file ) )
		{
			if( strpos( $file, "\0" ) === false && is_file( $file ) )
			{
				if( ( $content = file_get_contents( $file ) ) === false ) {
					throw new \Aimeos\MW\Media\Exception( sprintf( 'Unable to read from file "%1$s"', $file ) );
				}
			}
			else
			{
				$content = $file;
			}
		}
		else
		{
			throw new \Aimeos\MW\Media\Exception( 'Unsupported file parameter type' );
		}


		$finfo = new \finfo( FILEINFO_MIME_TYPE );
		$mimetype = $finfo->buffer( $content );
		$mime = explode( '/', $mimetype );

		$type = $mime[0] === 'image' ? 'Image' : 'Application';
		$name = $type === 'Image' && extension_loaded( 'imagick' ) ? 'Imagick' : 'Standard';
		$name = ucfirst( $options[$mime[0]]['name'] ?? $name );

		if( in_array( $mimetype, ['image/svg', 'image/svg+xml'] )
			|| in_array( $mimetype, ['application/gzip', 'application/x-gzip'] )
			&& is_string( $file ) && in_array( pathinfo( $file, PATHINFO_EXTENSION ), ['svg', 'svgz'] )
		) {
			$mimetype = 'image/svg+xml';
			$type = 'Image';
			$name = 'Svg';
		}


		if( ctype_alnum( $name ) === false )
		{
			$classname = is_string( $name ) ? '\Aimeos\MW\Media\\' . $type . '\\' . $name : '<not a string>';
			throw new \Aimeos\MW\Container\Exception( sprintf( 'Invalid characters in class name "%1$s"', $classname ) );
		}

		$iface = \Aimeos\MW\Media\Iface::class;
		$classname = '\Aimeos\MW\Media\\' . $type . '\\' . $name;

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
