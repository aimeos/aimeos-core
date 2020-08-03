<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2020
 * @package MW
 * @subpackage Media
 */


namespace Aimeos\MW\Media\Image;


/**
 * Image class for SVG files
 *
 * @package MW
 * @subpackage Media
 */
class Svg
	extends \Aimeos\MW\Media\Image\Base
	implements \Aimeos\MW\Media\Image\Iface
{
	private $svg;


	/**
	 * Initializes the new image object.
	 *
	 * @param string $content File content
	 * @param string $mimetype Mime type of the media data
	 * @param array $options Associative list of configuration options
	 * @throws \Aimeos\MW\Media\Exception If image couldn't be retrieved from the given file name
	 */
	public function __construct( string $content, string $mimetype, array $options )
	{
		parent::__construct( $mimetype );

		if( ( $string = @gzdecode( $content ) ) !== false ) {
			$content = $string;
		}

		if( ( $this->svg = @simplexml_load_string( $content ) ) === false ) {
			throw new \Aimeos\MW\Media\Exception( 'Invalid SVG file' );
		}
	}


	/**
	 * Clone resources
	 */
	public function __clone()
	{
		$this->svg = clone $this->svg;
	}


	/**
	 * Returns the height of the image
	 *
	 * @return int Height in pixel
	 */
	public function getHeight() : int
	{
		return ( isset( $this->svg['height'] ) ? (int) preg_replace( '/[^0-9.]/', '', $this->svg['height'] ) : 1 );
	}


	/**
	 * Returns the width of the image
	 *
	 * @return int Width in pixel
	 */
	public function getWidth() : int
	{
		return ( isset( $this->svg['width'] ) ? (int) preg_replace( '/[^0-9.]/', '', $this->svg['width'] ) : 1 );
	}


	/**
	 * Stores the media data at the given file name.
	 *
	 * @param string|null $filename File name to save the data into or null to return the data
	 * @param string|null $mimetype Mime type to save the content as or null to leave the mime type unchanged
	 * @return string|null File content if file name is null or null if data is saved to the given file name
	 * @throws \Aimeos\MW\Media\Exception If image couldn't be saved to the given file name
	 */
	public function save( string $filename = null, string $mimetype = null ) : ?string
	{
		if( ( $content = $this->svg->asXml() ) === false ) {
			throw new \Aimeos\MW\Media\Exception( 'Could not create SVG file' );
		}

		if( $filename === null ) {
			return $content;
		}

		if( file_put_contents( $filename, $content ) === false ) {
			throw new \Aimeos\MW\Media\Exception( 'Could not save SVG file' );
		}

		return null;
	}


	/**
	 * Scales the image to the given width and height.
	 *
	 * @param int|null $width New width of the image or null for automatic calculation
	 * @param int|null $height New height of the image or null for automatic calculation
	 * @param bool $fit True to keep the width/height ratio of the image
	 * @return \Aimeos\MW\Media\Iface Self object for method chaining
	 */
	public function scale( int $width = null, int $height = null, bool $fit = true ) : Iface
	{
		if( $width == null && $height == null ) {
			return $this;
		}

		$fit = (bool) $fit;
		$w = $this->getWidth();
		$h = $this->getHeight();

		if( $fit === true )
		{
			list( $width, $height ) = $this->getSizeFitted( $w, $h, $width, $height );

			if( $w <= $width && $h <= $height ) {
				return $this;
			}
		}

		$newWidth = $width;
		$newHeight = $height;
		$newMedia = clone $this;

		if( $fit === false )
		{
			$ratio = ( $w < $h ? $width / $w : $height / $h );
			$newHeight = (int) $h * $ratio;
			$newWidth = (int) $w * $ratio;

			$width = ( $width ?: $newWidth );
			$height = ( $height ?: $newHeight );

			$x = (int) ( $newWidth / 2 - $width / 2 );
			$y = (int) ( $newHeight / 2 - $height / 2 );

			$newMedia->svg['preserveAspectRatio'] = 'xMinYMin slice';
			$newMedia->svg['viewBox'] = $x . ' ' . $y . ' ' . $w . ' ' . $h;
		}

		$newMedia->svg['width'] = $width . 'px';
		$newMedia->svg['height'] = $height . 'px';

		return $newMedia;
	}
}
