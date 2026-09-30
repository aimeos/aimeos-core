<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2026
 * @package MShop
 * @subpackage Media
 */


namespace Aimeos\MShop\Media\Manager;


/**
 * Trait for fetching remote files securely
 *
 * @package MShop
 * @subpackage Media
 */
trait Remote
{
	/**
	 * Tests if the IP address is a public one
	 *
	 * @param string $ip IPv4 or IPv6 address
	 * @return bool TRUE if the address is public, FALSE if it is private, reserved or invalid
	 */
	protected function publicIp( string $ip ) : bool
	{
		if( !filter_var( $ip, FILTER_VALIDATE_IP ) ) {
			return false;
		}

		$bin = (string) inet_pton( $ip );

		// IPv4-mapped (::ffff:0:0/96) addresses aren't reserved before PHP 8.3, IPv4-compatible (::/96) ones are deprecated
		if( strlen( $bin ) === 16 && strncmp( $bin, str_repeat( "\x00", 10 ), 10 ) === 0 ) {
			return substr( $bin, 10, 2 ) === "\xff\xff" && $this->publicIp( (string) inet_ntop( substr( $bin, 12 ) ) );
		}

		if( !filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
			return false;
		}

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
}
