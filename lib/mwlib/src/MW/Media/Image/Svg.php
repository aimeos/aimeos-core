<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 * @package MW
 * @subpackage Media
 */


namespace Aimeos\MW\Media\Image;

use enshrined\svgSanitize\Sanitizer;


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
	 * @throws \Aimeos\MW\Media\Exception If image couldn't be retrieved from the given file name
	 */
	public function __construct( string $content, string $mimetype )
	{
		parent::__construct( $mimetype );

		if( ( $string = @gzdecode( $content ) ) !== false ) {
			$content = $string;
		}

		$sanitizer = new Sanitizer();
		$sanitizer->removeRemoteReferences( true );

		if( ( $content = $sanitizer->sanitize( $content ) ) === false ) {
			throw new \Aimeos\MW\Media\Exception( 'Invalid SVG file: ' . print_r( $sanitizer->getXmlIssues(), true ) );
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
	 * @param int $fit "0" keeps image ratio, "1" adds padding while "2" crops image to enforce image size
	 * @return \Aimeos\MW\Media\Image\Iface Self object for method chaining
	 */
	public function scale( int $width = null, int $height = null, int $fit = 0 ) : Iface
	{
		if( $width == null && $height == null ) {
			return $this;
		}

		$w = $this->getWidth();
		$h = $this->getHeight();
		$newMedia = clone $this;

		if( $fit === 2 ) {
			$newMedia->svg['viewBox'] = $this->box( $w, $h, $width / $height, $height / $width );
		} else {
			$newMedia->svg['viewBox'] = $this->box( $w, $h, $w / $h, $h / $w );
		}

		$newMedia->svg['width'] = $width;
		$newMedia->svg['height'] = $height;

		return $newMedia;
	}


	/**
	 * Returns the fitted width and height.
	 *
	 * @param int $srcWidth Width of the image
	 * @param int $srcHeight Height of the image
	 * @param int|null $destWidth New width of the image
	 * @param int|null $destHeight New height of the image
	 * @return array Array containing the new width at position 0 and the new height as position 1
	 */
	protected function box( int $srcWidth, int $srcHeight, float $wRatio, float $hRatio ) : string
	{
		$newWidth = $srcWidth;
		$newHeight = $srcHeight;

		if( $wRatio > $hRatio ) {
			$newHeight = (int) round( $srcHeight / $wRatio );
		} else {
			$newWidth = (int) round( $srcWidth / $hRatio );
		}

		$x = round( ( $srcWidth - $newWidth ) / 2 );
		$y = round( ( $srcHeight - $newHeight ) / 2 );

		return $x . ' ' . $y . ' ' . $newWidth . ' ' . $newHeight;
	}
}
