<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */

if( isset( $this->downloadItem ) )
{
	$value = $this->downloadItem->getValue();

	if( isset( $this->downloadFilesystem ) && $this->downloadFilesystem->has( $value ) )
	{
		$name = $this->downloadItem->getName();

		if( pathinfo( $name, PATHINFO_EXTENSION ) == null
			&& ( $ext = pathinfo( $value, PATHINFO_EXTENSION ) ) != null
		) {
			$name .= '.' . $ext;
		}

		@header( 'Content-Description: File Transfer' );
		@header( 'Content-Type: application/octet-stream' );
		@header( 'Content-Disposition: attachment; filename="' . $name . '"' );
		@header( 'Content-Length: ' . $this->downloadFilesystem->size( $value ) );
		@header( 'Cache-Control: must-revalidate' );
		@header( 'Pragma: private' );
		@header( 'Expires: 0' );

		$fh = $this->downloadFilesystem->reads( $value );
		fpassthru( $fh );
		fclose( $fh );
	}
	elseif( filter_var( $value, FILTER_VALIDATE_URL ) !== false )
	{
		@header( 'Location: ' . $value, true, 303 );
	}
	else
	{
		@header( 'HTTP/1.0 404 Not Found', true, 404 );
	}
}
else
{
	@header( 'HTTP/1.0 403 Forbidden', true, 403 );
}

?>