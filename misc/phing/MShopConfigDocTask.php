<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2020
 */


require_once 'phing/Task.php';


/**
 * Generate Markdown pages for the configuration documentation
 */
class MShopConfigDocTask extends Task
{
	private $file;
	private $outdir;
	private $optfile;
	private $filesets = [];
	private $keyparts = 1;
	private $prefix = '';


	/**
	 * Nested creator, creates a FileSet for this task
	 *
	 * @return FileSet The created fileset object
	 */
	public function createFileSet()
	{
		$num = array_push( $this->filesets, new FileSet() );

		return $this->filesets[$num - 1];
	}


	/**
	 * Initializes the object
	 */
	public function init()
	{
		return true;
	}


	/**
	 * Generates Markdown pages for the configuration documentation
	 */
	public function main()
	{
		$options = [];

		if( !isset( $this->file ) && empty( $this->filesets ) ) {
			throw new BuildException( "Missing either a nested fileset or attribute 'file' set" );
		}

		if( isset( $this->optfile )
			&& ( $string = file_get_contents( $this->optfile ) ) !== false
			&& ( $options = unserialize( $string ) ) === false
		) {
			throw new BuildException( sprintf( 'Unable to unserialize content of file "%1$s"', $this->optfile ) );
		}

		$options = $this->sanitize( $this->extract( $options ) );

		ksort( $options );
		$this->writeFiles( $this->createContent( $options ) );

		$this->log( 'Number of config options for ' . $this->prefix . ': ' . count( $options ) );
	}


	/**
	 * File to be performed syntax check on
	 *
	 * @param PhingFile $file
	 */
	public function setFile( PhingFile $file )
	{
		$this->file = $file;
	}


	/**
	 * File with serialized PHP array of extracted configuration options
	 *
	 * @param string $file
	 */
	public function setOptfile( $file )
	{
		$this->optfile = $file;
	}


	/**
	 * Directory that will contain the generated pages
	 *
	 * @param string $dir
	 */
	public function setOutdir( $dir )
	{
		$this->outdir = $dir;
	}


	/**
	 * The configuration options must start with this prefix
	 *
	 * All other config options are ignored
	 *
	 * @param string $prefix
	 */
	public function setPrefix( $prefix )
	{
		$this->prefix = $prefix;
	}


	/**
	 * Sets the number of grouped key parts
	 *
	 * @param string $keyparts
	 */
	public function setKeyParts( $keyparts )
	{
		$this->keyparts = (int) $keyparts;
	}


	/**
	 * Creates the Markdown files for the given options
	 *
	 * @param array $options Associative list of the keys and an array of
	 * 	"short", "long", "param", "default", "deprecated", "since" and "see" entries
	 */
	protected function createContent( array $options )
	{
		$prefixlen = strlen( $this->prefix ) + 1;
		$header = '';
		$files = [];

		foreach( $options as $key => $list )
		{
			$data = $short = $type = $since = $deprecated = '';
			$parts = explode( '/', substr( $key, $prefixlen ) );

			if( isset( $list['param'] ) )
			{
				$matches = [];

				if( preg_match( '/([^\t ]+)[\t ]+(.*)/u', $list['param'], $matches ) === false ) {
					throw new BuildException( 'Invalid match pattern' );
				}

				$type = "* Type: ${matches[1]} - ${matches[2]}" . "\n";
			}

			if( isset( $list['since'] ) )
			{
				foreach( $list['since'] as $text ) {
					$since .= "* Since: " . $text. "\n";
				}
			}

			if( isset( $list['deprecated'] ) ) {
				$deprecated = "* Deprecated: " . $list['deprecated'] . "\n";
			}

			if( isset( $list['short'] ) )
			{
				$short = "\n" . trim( $list['short'] ) . "\n";
				$options[$key]['short'] = $short;
			}

			if( $header !== join( '/', array_slice( $parts, 0, $this->keyparts + 1 ) ) ) {
				$data .= "\n# " . join( '/', array_slice( $parts, $this->keyparts, 1 ) );
			}

			if( count( $parts ) > $this->keyparts + 1 ) {
				$data .= "\n## " . join( '/', array_slice( $parts, $this->keyparts + 1 ) );
			}

			$header = join( '/', array_slice( $parts, 0, $this->keyparts + 1 ) );

			$keyvalue = print_r( str_replace( ["\n\t"], ["\n "], $list['value'] ), true );
			$defvalue = $list['default'];
			$matches = [];

			if( preg_match( "/([\t]+)/", $keyvalue, $matches ) === 1 )
			{
				$keyvalue = str_replace( $matches[1], '', $keyvalue );
				$defvalue = str_replace( $matches[1], '', $defvalue );
			}

			$default = "* Default: " . str_replace( ["\n\t"], ["\n "], $defvalue ) . "\n";

			$data .= "\n$short";
			$data .= "\n```";
			$data .= "\n$key = " . rtrim( str_replace( ["\n(", "\n)"], ["\n(", "\n)"], $keyvalue ) );
			$data .= "\n```\n";
			$data .= "\n${deprecated}${default}${type}${since}";

			if( isset( $list['long'] ) )
			{
				foreach( $list['long'] as $desc )
				{
					if( ( $desc = preg_replace( '/\{\@link ([^ ]+) ([^\}]+)\}/', '[$2]($1)', $desc ) ) === null ) {
						throw new BuildException( 'Unable to compile link regex' );
					}

					if( ( $desc = preg_replace( "/^ (.+)\n/sm", "```\n \$1\n```\n", $desc ) ) === null ) {
						throw new BuildException( 'Unable to compile code regex' );
					}

					$data .= "\n" . $desc;
				}
			}

			if( isset( $list['see'] ) )
			{
				$data .= "\nSee also:\n";

				foreach( $list['see'] as $see ) {
					$data .= "\n* $see";
				}
			}

			if( count( $parts ) > $this->keyparts )
			{
				$filename = implode( '-', array_slice( $parts, 0, $this->keyparts ) );
				$files[$filename][] = $data;
			}
		}

		return $files;
	}


	/**
	 * Extracts the configuration documentation
	 *
	 * @param array $options Associative list of extracted configuration options
	 * @return array Map of config key and associative list with
	 * 	"short", "long", "param", "default", "deprecated", "since" and "see" entries
	 */
	protected function extract( array $options )
	{
		$project = $this->getProject();

		foreach( $this->filesets as $fs )
		{
			$files = $fs->getDirectoryScanner( $project )->getIncludedFiles();
			$dir = $fs->getDir( $project )->getPath();

			foreach( $files as $file ) {
				$this->extractFile( $dir . DIRECTORY_SEPARATOR . $file, $options );
			}
		}

		return $options;
	}


	/**
	 * Extracts the configuration documentation from the file and adds it to the options array.
	 *
	 * @param string $filename Absolute name of the file
	 * @param array &$options Associative list of extracted configuration options
	 */
	protected function extractFile( $filename, array &$options )
	{
		$matches = array();

		if( ( $text = file_get_contents( $filename ) ) === false ) {
			throw new BuildException( sprintf( 'Unable to get file content from "%1$s"', $filename ) );
		}

		if( preg_match_all( '#/\*\*[\t ]+([a-zA-Z0-9/\-\._]+)[\t ]*$.*\*/#smuU', $text, $matches ) === false ) {
			throw new BuildException( 'Invalid extract pattern' );
		}

		foreach( (array) $matches[1] as $pos => $key )
		{
			if( ( $list = preg_split( '/$/smu', $matches[0][$pos], -1, PREG_SPLIT_NO_EMPTY ) ) === false ) {
				throw new BuildException( 'Invalid split pattern' );
			}

			if( ( $list = preg_replace( '#^[^*/]*\*[\t ]?#u', '', $list ) ) === false ) {
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
					$options[$key]['since'][] = trim( substr( $line, 6 ) );
				} else if( strncmp( '@category', $line, 9 ) === 0 ) {
					$options[$key]['category'][] = trim( substr( $line, 9 ) );
				} else if( strncmp( '@deprecated', $line, 11 ) === 0 ) {
					$options[$key]['deprecated'] = trim( substr( $line, 11 ) );
				} else if( !isset( $desc[$num] ) ) {
					$desc[$num] = $line . "\n";
				} else {
					$desc[$num] .= $line . "\n";
				}
			}

			if( isset( $desc[0] ) )
			{
				$options[$key]['short'] = str_replace( '\\/', '/', $desc[0] );
				unset( $desc[0] );
			}

			if( !empty( $desc ) ) {
				$options[$key]['long'] = str_replace( '\\/', '/', $desc );
			}
		}
	}


	/**
	 * Removes invalid keys from options map
	 *
	 * @param array $options Map of config key and associative list with
	 * 	"short", "long", "param", "default", "deprecated", "since" and "see" entries
	 * @return array Sanitized map of config key and associative list of option pairs
	 */
	protected function sanitize( array $options )
	{
		$len = strlen( $this->prefix );

		foreach( $options as $key => $values )
		{
			if( strncmp( $key, $this->prefix, $len ) !== 0 ) {
				unset( $options[$key] );
			} else if( strpos( $key, 'unknown' ) !== false ) {
				unset( $options[$key] );
			} else if( !isset( $values['short'] ) && $key[0] !== "\n" ) {
				$this->log( 'No doc: ' . $key );
			}
		}

		return $options;
	}


	/**
	 * Write the file content to the disc
	 *
	 * @param array $files Map of file name and file content
	 */
	protected function writeFiles( array $files )
	{
		$dir = $this->outdir . '/' . str_replace( '/', '-', $this->prefix );

		if( !file_exists( $dir ) ) {
			mkdir( $dir );
		}

		foreach( $files as $filename => $list )
		{
			$filepath = $dir . '/' . $filename . '.md';

			if( file_put_contents( $filepath, join( "\n", $list ) ) === false ) {
				throw new BuildException( sprintf( 'Unable to write to file "%1$s"', $filepath ) );
			}
		}
	}
}
