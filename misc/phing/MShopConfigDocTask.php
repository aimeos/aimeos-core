<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


require_once 'phing/Task.php';

/**
 * Generating Mediawiki pages for the configuration documentation.
 */
class MShopConfigDocTask extends Task
{
	private $_file;
	private $_optfile;
	private $_outfile;
	private $_filesets = array();
	private $_keyprefix = '';
	private $_wikiprefix = '';
	private $_keyparts = 1;


	/**
	 * Nested creator, creates a FileSet for this task
	 *
	 * @return FileSet The created fileset object
	 */
	public function createFileSet()
	{
		$num = array_push( $this->_filesets, new FileSet() );

		return $this->_filesets[$num-1];
	}


	/**
	 * Initializes the object.
	 */
	public function init()
	{
		return true;
	}

	/**
	 * Generates Mediawiki pages for the configuration documentation.
	 */
	public function main()
	{
		$result = array();

		if( !isset( $this->_file ) && count( $this->_filesets ) == 0 ) {
			throw new BuildException( "Missing either a nested fileset or attribute 'file' set" );
		}

		if( !isset( $this->_optfile ) || ( $string = file_get_contents( $this->_optfile ) ) === false ) {
			throw new BuildException( sprintf( 'Unable to open optfile "%1$s"', $this->_optfile ) );
		}

		if( ( $options = unserialize( $string ) ) === false ) {
			throw new BuildException( sprintf( 'Unable to unserialize content of file "%1$s"', $this->_optfile ) );
		}

		if( $this->_file instanceof PhingFile )
		{
			$this->_extract( $this->_file->getPath(), $options );
		}
		else // process filesets
		{
			$project = $this->getProject();

			foreach( $this->_filesets as $fs )
			{
				$files = $fs->getDirectoryScanner( $project )->getIncludedFiles();
				$dir = $fs->getDir( $this->project )->getPath();

				foreach( $files as $file ) {
					$this->_extract( $dir . DIRECTORY_SEPARATOR . $file, $options );
				}
			}
		}

		$len = strlen( $this->_keyprefix );

		foreach( $options as $key => $values )
		{
			if( strncmp( $key, $this->_keyprefix, $len ) !== 0 ) {
				unset( $options[$key] );
			}
		}

		ksort( $options );
		$this->log( 'Number of config options for ' . $this->_keyprefix . ': ' . count( $options ) );

		$this->_createWikiPages( $options );
	}


	/**
	 * File to be performed syntax check on.
	 *
	 * @param PhingFile $file
	 */
	public function setFile( PhingFile $file )
	{
		$this->_file = $file;
	}


	/**
	 * File with serialized PHP array of extracted configuration options.
	 *
	 * @param string $file
	 */
	public function setOptfile( $file )
	{
		$this->_optfile = $file;
	}


	/**
	 * File that will contain the generated pages.
	 *
	 * @param string $file
	 */
	public function setOutfile( $file )
	{
		$this->_outfile = $file;
	}


	/**
	 * The configuration options must start with this prefix.
	 *
	 * All other config options are ignored
	 *
	 * @param string $prefix
	 */
	public function setKeyPrefix( $prefix )
	{
		$this->_keyprefix = $prefix;
	}


	/**
	 * Sets the number of grouped key parts.
	 *
	 * @param string $keyparts
	 */
	public function setKeyParts( $keyparts )
	{
		$this->_keyparts = $keyparts;
	}


	/**
	 * Title prefix for the wiki pages.
	 *
	 * @param string $prefix
	 */
	public function setWikiPrefix( $prefix )
	{
		$this->_wikiprefix = $prefix;
	}


	/**
	 * Creates the wiki pages for the given options.
	 *
	 * @param array $options Associative list of the keys and an array of
	 * 	"short", "desc", "param", "since" and "see" entries
	 */
	protected function _createWikiPages( array $options )
	{
		if( ( $fh = fopen( $this->_outfile, 'w' ) ) === false ) {
			throw new BuildException( sprintf( 'Unable to open file "%1$s"', $this->_outfile ) );
		}

		$date = date( 'c' );
		$keyprefix = $this->_keyprefix;
		$wikiprefix = $this->_wikiprefix;
		$prefixlen = strlen( $this->_keyprefix ) + 1;
		$sections = array( 'global' => array() );
		$matches = array();

		$this->_writeFile( $fh, '<mediawiki xmlns="http://www.mediawiki.org/xml/export-0.4/"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 xsi:schemaLocation="http://www.mediawiki.org/xml/export-0.4/ http://www.mediawiki.org/xml/export-0.4.xsd" version="0.4"
 xml:lang="en"><siteinfo><namespaces><namespace key="0" case="first-letter" /></namespaces></siteinfo>' . "\n" );

		foreach( $options as $key => $list )
		{
			$type = $since = '';
			$parts = explode( '/', substr( $key, $prefixlen ) );

			if( count( $parts ) > $this->_keyparts )
			{
				$first = implode( '/', array_slice( $parts, 0, $this->_keyparts ) );
				$sections[$first][ $parts[$this->_keyparts] ][] = $key;
			}

			if( isset( $list['param'] ) )
			{
				if( preg_match( '/([^\t ]+)[\t ]+(.*)/u', $list['param'], $matches ) === false ) {
					throw new BuildException( 'Invalid match pattern' );
				}

				$type = "* Type: ${matches[1]} (${matches[2]})";
			}

			if( isset( $list['since'] ) ) {
				$since = "* Since: " . $list['since'];
			}

			$default = "* Default: " . str_replace( "\n\t", "\n ", $list['default'] );
			$value = str_replace( array( '<', '>', "\n\t" ), array( '&lt;', '&gt;', "\n " ), $list['value'] );
			$short = trim( str_replace( array( '<', '>' ), array( '&lt;', '&gt;' ), $list['short'] ) );
			$options[$key]['short'] = $short;

			$data = "<page>\n<title>$wikiprefix/$key</title>\n<ns>0</ns><revision><timestamp>$date</timestamp>";
			$data .= "<contributor></contributor>\n<comment>Generated by MShopConfigDocTask</comment><text>\n";
			$data .= "== Summary ==\n\n$short\n\n";
			$data .= " $key = " . str_replace( array( '(', ')' ), array( ' (', ' )' ), print_r( $value, true ) );
			$data .= "\n\n$default\n$type\n$since\n";

			if( isset( $list['long'] ) )
			{
				$data .= "\n== Description ==\n";

				foreach( $list['long'] as $desc ) {
					$data .= "\n" . str_replace( array( '<', '>' ), array( '&lt;', '&gt;' ), $list['long'] );
				}
			}

			if( isset( $list['see'] ) )
			{
				$data .= "\n== See also ==\n\n";

				foreach( $list['see'] as $see ) {
					$data .= "* [[$wikiprefix/$see|$see]]\n";
				}
			}

			if( isset( $list['category'] ) )
			{
				$last = end( $parts );

				foreach( $list['category'] as $category ) {
					$data .= "\n[[Category:$category|$last]]\n";
				}
			}

			$data .= "</text><model>wikitext</model><format>text/x-wiki</format></revision>\n</page>\n";

			$this->_writeFile( $fh, $data );
		}


		$data = "<page>\n<title>$wikiprefix/$keyprefix</title>\n<ns>0</ns><revision><timestamp>$date</timestamp>
<contributor></contributor>\n<comment>Generated by MShopConfigDocTask</comment>
<text xml:space=\"preserve\">__TOC__\n\n\n";

		foreach( $sections as $name => $list )
		{
			$data .= "== $name ==\n";

			foreach( $list as $subname => $keys )
			{
				$data .= "=== $subname ===\n";

				foreach( $keys as $key )
				{
					$desc = $options[$key]['short'];
					$data .= "\n; [[$wikiprefix/$key|$key]] : $desc\n";
				}
			}
		}

		$data .= "</text><model>wikitext</model><format>text/x-wiki</format></revision>\n</page>\n";

		$this->_writeFile( $fh, $data );
		$this->_writeFile( $fh, '</mediawiki>' );
	}


	/**
	 * Extracts the configuration documentation from the file and adds it to the options array.
	 *
	 * @param string $filename Absolute name of the file
	 * @param array &$options Associative list of extracted configuration options
	 */
	protected function _extract( $filename, array &$options )
	{
		$matches = $result = array();

		if( ( $text = file_get_contents( $filename ) ) === false ) {
			throw new BuildException( sprintf( 'Unable to get file content from "%1$s"', $filename ) );
		}

		if( preg_match_all( '#/\*\*[\t ]+([a-zA-Z0-9/\-_]+)[\t ]*$.*\*/#smuU', $text, $matches ) === false ) {
			throw new BuildException( 'Invalid extract pattern' );
		}

		foreach( (array) $matches[1] as $pos => $key )
		{
			if( ( $list = preg_split( '/$/smu', $matches[0][$pos], -1, PREG_SPLIT_NO_EMPTY ) ) === false ) {
				throw new BuildException( 'Invalid split pattern' );
			}

			if( ( $list = preg_replace( '#^[^*/]*\*[\t ]*#u', '', $list ) ) === false ) {
				throw new BuildException( 'Invalid replace pattern' );
			}

			$num = 0;
			$desc = array();

			foreach( $list as $line )
			{
				if( $line === '' ) {
					$num++;
				} else if( $line[0] === '/' ) {
					continue;
				} else if( strncmp( '@see', $line, 4 ) === 0 ) {
					$options[$key]['see'][] = trim( substr( $line, 4 ) );
				} else if( strncmp( '@param', $line, 6 ) === 0 ) {
					$options[$key]['param'] = trim( substr( $line, 6 ) );
				} else if( strncmp( '@since', $line, 6 ) === 0 ) {
					$options[$key]['since'] = trim( substr( $line, 6 ) );
				} else if( strncmp( '@category', $line, 9 ) === 0 ) {
					$options[$key]['category'][] = trim( substr( $line, 9 ) );
				} else if( !isset( $desc[$num] ) ) {
					$desc[$num] = $line . "\n";
				} else {
					$desc[$num] .= $line . "\n";
				}
			}

			if( isset( $desc[0] ) )
			{
				$options[$key]['short'] = $desc[0];
				unset( $desc[0] );
			}

			$options[$key]['long'] = $desc;
		}

		return $result;
	}


	protected function _writeFile( $handle, $data )
	{
		if( fwrite( $handle, $data ) === false ) {
			throw new BuildException( sprintf( 'Unable to write to file "%1$s"', $this->_outfile ) );
		}
	}
}
