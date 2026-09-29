<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2023
 * @package MShop
 * @subpackage Media
 */


namespace Aimeos\MShop\Media\Manager;

use \Intervention\Image\Interfaces\ImageInterface;


/**
 * Media preview trait
 *
 * @package MShop
 * @subpackage Media
 */
trait Preview
{
	use \Aimeos\Macro\Macroable;


	private ?\Intervention\Image\ImageManager $driver = null;


	/**
	 * Returns the context object.
	 *
	 * @return \Aimeos\MShop\ContextIface Context object
	 */
	abstract protected function context() : \Aimeos\MShop\ContextIface;


	/**
	 * Creates scaled images according to the configuration settings
	 *
	 * @param \Intervention\Image\Interfaces\ImageInterface $image Media object
	 * @param array $sizes List of entries with "maxwidth" (int or null), "maxheight" (int or null), "force-size" (0: scale, 1: pad, 2: cover) and "background" (hex color) values
	 * @return \Intervention\Image\Interfaces\ImageInterface[] Associative list of image width as keys and scaled media object as values
	 */
	protected function createPreviews( ImageInterface $image, array $sizes ) : array
	{
		$list = [];

		foreach( $sizes as $entry )
		{
			$force = $entry['force-size'] ?? 0;
			$maxwidth = isset( $entry['maxwidth'] ) ? (int) $entry['maxwidth'] : null;
			$maxheight = isset( $entry['maxheight'] ) ? (int) $entry['maxheight'] : null;
			$bg = ltrim( (string) ( $entry['background'] ?? 'ffffff00' ), '#' );

			if( $this->call( 'filterPreviews', $image, $maxwidth, $maxheight, $force ) )
			{
				$file = match( $force ) {
					0 => (clone $image)->scaleDown( $maxwidth, $maxheight ),
					1 => (clone $image)->pad( $maxwidth ?? 0, $maxheight ?? 0, $bg, 'center' ),
					2 => (clone $image)->cover( $maxwidth ?? 0, $maxheight ?? 0 ),
					default => (clone $image)->scaleDown( $maxwidth, $maxheight )
				};

				$list[$file->width()] = $file;
			}
		}

		return $list;
	}


	/**
	 * Removes the previes images from the storage
	 *
	 * @param \Aimeos\MShop\Media\Item\Iface $item Media item which will contains the image URLs afterwards
	 * @param array $paths List of preview paths to remove
	 * @return \Aimeos\MShop\Media\Item\Iface Media item with preview images removed
	 */
	protected function deletePreviews( \Aimeos\MShop\Media\Item\Iface $item, array $paths ) : \Aimeos\MShop\Media\Item\Iface
	{
		if( !empty( $paths = $this->call( 'removePreviews', $item, $paths ) ) )
		{
			$fs = $this->context()->fs( $item->getFileSystem() );

			foreach( $paths as $preview )
			{
				if( $preview && $fs->has( (string) $preview ) ) {
					$fs->rm( (string) $preview );
				}
			}
		}

		return $item;
	}


	/**
	 * Tests if the preview image should be created
	 *
	 * @param \Intervention\Image\Interfaces\ImageInterface $image Media object
	 * @param int|null $maxwidth New width of the image or null for automatic calculation
	 * @param int|null $maxheight New height of the image or null for automatic calculation
	 * @param int $force "0" keeps image ratio, "1" adds padding while "2" crops image to enforce image size
	 */
	protected function filterPreviews( ImageInterface $image, ?int $maxwidth, ?int $maxheight, int $force ) : bool
	{
		return true;
	}


	/**
	 * Returns the image object for the given file name
	 *
	 * @param string $file URL or relative path to the file
	 * @param string $fsname File system name where the file is stored
	 * @return \Intervention\Image\Interfaces\ImageInterface Image object
	 */
	protected function image( string $file, string $fsname = 'fs-media' ) : ImageInterface
	{
		if( !isset( $this->driver ) )
		{
			if( class_exists( '\Intervention\Image\Vips\Driver' ) ) {
				$driver = new \Intervention\Image\Vips\Driver();
			} elseif( class_exists( '\Imagick' ) ) {
				$driver = new \Intervention\Image\Drivers\Imagick\Driver();
			} else {
				$driver = new \Intervention\Image\Drivers\Gd\Driver();
			}

			// @phpstan-ignore argument.type
			$this->driver = new \Intervention\Image\ImageManager( $driver );
		}

		if( preg_match( '#^https?://#', $file ) === 1 )
		{
			$fh = $this->remote( $file );
		}
		else
		{
			$fh = $this->context()->fs( $fsname )->reads( $file );
		}

		$image = $this->driver->read( $fh );
		fclose( $fh );

		return $image;
	}


	/**
	 * Tests if the IP address is a public one
	 *
	 * @param string $ip IPv4 or IPv6 address
	 * @return bool TRUE if the address is public, FALSE if it is private, reserved or invalid
	 */
	protected function publicIp( string $ip ) : bool
	{
		if( !filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
			return false;
		}

		$bin = (string) inet_pton( $ip );

		if( strlen( $bin ) === 4 ) {
			return ( ord( $bin[0] ) !== 100 || ( ord( $bin[1] ) & 0xc0 ) !== 64 ); // 100.64.0.0/10 (CGNAT)
		}

		// NAT64 prefixes 64:ff9b::/96 and 64:ff9b:1::/48 can map to internal IPv4 addresses
		return strncmp( $bin, "\x00\x64\xff\x9b", 4 ) !== 0;
	}


	/**
	 * Opens a remote http(s) URL for reading
	 *
	 * Private and reserved addresses are rejected, the resolved IP address is pinned
	 * to prevent DNS rebinding and each redirect target is validated the same way.
	 *
	 * @param string $url Remote URL starting with "http://" or "https://"
	 * @return resource File handle of the downloaded content
	 * @throws \RuntimeException If the URL is invalid, not allowed or can't be fetched
	 */
	protected function remote( string $url )
	{
		/** mshop/media/manager/private
		 * Allows fetching remote images from private and reserved IP addresses
		 *
		 * Remote image URLs are fetched when creating the preview images. To
		 * prevent server-side request forgery, only hosts resolving to public IP
		 * addresses are allowed by default. Enable this setting only if you need
		 * to fetch images from hosts in your internal network and all users
		 * allowed to add media URLs are trusted.
		 *
		 * @param bool TRUE to allow private and reserved IP addresses, FALSE to deny
		 * @since 2026.10
		 */
		$private = (bool) $this->context()->config()->get( 'mshop/media/manager/private', false );
		$msg = $this->context()->translate( 'mshop', 'Unable to open file "%1$s"' );
		$orig = $url;

		for( $redirects = 0; $redirects <= 3; $redirects++ )
		{
			$parts = parse_url( $url );
			$host = $parts['host'] ?? '';
			$scheme = strtolower( $parts['scheme'] ?? '' );

			if( !in_array( $scheme, ['http', 'https'], true ) || !filter_var( $host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME ) ) {
				throw new \RuntimeException( sprintf( $msg, $orig ) );
			}

			if( filter_var( $host, FILTER_VALIDATE_IP ) )
			{
				$ips = [$host];
			}
			else
			{
				$ips = @gethostbynamel( $host ) ?: [];

				foreach( @dns_get_record( $host, DNS_AAAA ) ?: [] as $record ) {
					$ips[] = $record['ipv6'] ?? '';
				}
			}

			// reject if any address is internal so the pinned IP can't be chosen by the resolver order
			if( empty( $ips ) || !$private && in_array( false, array_map( [$this, 'publicIp'], $ips ), true ) ) {
				throw new \RuntimeException( sprintf( $msg, $orig ) );
			}

			$ip = str_contains( $ips[0], ':' ) ? '[' . $ips[0] . ']' : $ips[0];
			$port = $parts['port'] ?? ( $scheme === 'https' ? 443 : 80 );

			if( ( $fh = fopen( 'php://temp', 'w+' ) ) === false || ( $ch = curl_init( $url ) ) === false ) {
				throw new \RuntimeException( sprintf( $msg, $orig ) );
			}

			curl_setopt_array( $ch, [
				CURLOPT_FILE => $fh,
				CURLOPT_FOLLOWLOCATION => false,
				CURLOPT_RESOLVE => [$host . ':' . $port . ':' . $ip],
				CURLOPT_CONNECTTIMEOUT => 10,
				CURLOPT_TIMEOUT => 60,
			] );

			$result = curl_exec( $ch );
			$status = curl_getinfo( $ch, CURLINFO_RESPONSE_CODE );
			$location = curl_getinfo( $ch, CURLINFO_REDIRECT_URL );

			if( $result === true && $status >= 200 && $status < 300 )
			{
				rewind( $fh );
				return $fh;
			}

			fclose( $fh );

			if( $result !== true || !in_array( $status, [301, 302, 303, 307, 308] ) || !$location ) {
				break;
			}

			$url = $location;
		}

		throw new \RuntimeException( sprintf( $msg, $orig ) );
	}


	/**
	 * Returns the preview images to be deleted
	 *
	 * @param \Aimeos\MShop\Media\Item\Iface $item Media item with new preview URLs
	 * @param array $paths List of preview paths to remove
	 * @return iterable List of preview URLs to remove
	 */
	protected function removePreviews( \Aimeos\MShop\Media\Item\Iface $item, array $paths ) : iterable
	{
		$previews = $item->getPreviews();

		// don't delete first (smallest) image because it may be referenced in past orders
		if( $item->getDomain() === 'product' && in_array( key( $previews ), $paths ) ) {
			return array_slice( $paths, 1 );
		}

		return $paths;
	}
}
