<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Job controller for CSV product imports.
 *
 * @package Controller
 * @subpackage Jobs
 */
class Controller_Jobs_Product_Import_Csv_Default
	extends Controller_Common_Product_Import_Csv_Base
	implements Controller_Jobs_Iface
{
	/**
	 * Returns the localized name of the job.
	 *
	 * @return string Name of the job
	 */
	public function getName()
	{
		return $this->getContext()->getI18n()->dt( 'controller/jobs', 'Product import CSV' );
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription()
	{
		return $this->getContext()->getI18n()->dt( 'controller/jobs', 'Imports new and updates existing products from CSV files' );
	}


	/**
	 * Executes the job.
	 *
	 * @throws Controller_Jobs_Exception If an error occurs
	 */
	public function run()
	{
		$total = $errors = 0;
		$context = $this->getContext();
		$config = $context->getConfig();
		$logger = $context->getLogger();
		$domains = array( 'attribute', 'media', 'price', 'product', 'text' );
		$mappings = $this->getDefaultMapping();


		/** controller/common/product/import/csv/domains
		 * List of item domain names that should be retrieved along with the product items
		 *
		 * For efficient processing, the items associated to the products can be
		 * fetched to, minimizing the number of database queries required. To be
		 * most effective, the list of item domain names should be used in the
		 * mapping configuration too, so the retrieved items will be used during
		 * the import.
		 *
		 * @param array Associative list of MShop item domain names
		 * @since 2015.05
		 * @category Developer
		 * @see controller/common/product/import/csv/mapping
		 * @see controller/common/product/import/csv/converter
		 * @see controller/common/product/import/csv/max-size
		 */
		$domains = $config->get( 'controller/common/product/import/csv/domains', $domains );

		/** controller/jobs/product/import/csv/domains
		 * List of item domain names that should be retrieved along with the product items
		 *
		 * This configuration setting overwrites the shared option
		 * "controller/common/product/import/csv/domains" if you need a
		 * specific setting for the job controller. Otherwise, you should
		 * use the shared option for consistency.
		 *
		 * @param array Associative list of MShop item domain names
		 * @since 2015.05
		 * @category Developer
		 * @see controller/jobs/product/import/csv/mapping
		 * @see controller/jobs/product/import/csv/skip-lines
		 * @see controller/jobs/product/import/csv/converter
		 * @see controller/jobs/product/import/csv/strict
		 * @see controller/jobs/product/import/csv/backup
		 * @see controller/common/product/import/csv/max-size
		 */
		$domains = $config->get( 'controller/jobs/product/import/csv/domains', $domains );


		/** controller/common/product/import/csv/mapping
		 * List of mappings between the position in the CSV file and item keys
		 *
		 * The importer have to know which data is at which position in the CSV
		 * file. Therefore, you need to specify a mapping between each position
		 * and the MShop domain item key (e.g. "product.code") it represents.
		 *
		 * You can use all domain item keys which are used in the fromArray()
		 * methods of the item classes. The "*.type" item keys will be
		 * automatically converted to their "*.typeid" representation. You only
		 * need to make sure that the corresponding type is available in the
		 * database.
		 *
		 * These mappings are grouped together by their processor names, which
		 * are responsible for importing the data, e.g. all mappings in "item"
		 * will be processed by the base product importer while the mappings in
		 * "text" will be imported by the text processor.
		 *
		 * @param array Associative list of processor names and lists of key/position pairs
		 * @since 2015.05
		 * @category Developer
		 * @see controller/common/product/import/csv/domains
		 * @see controller/common/product/import/csv/converter
		 * @see controller/common/product/import/csv/max-size
		 */
		$mappings = $config->get( 'controller/common/product/import/csv/mapping', $mappings );

		/** controller/jobs/product/import/csv/mapping
		 * List of mappings between the position in the CSV file and item keys
		 *
		 * This configuration setting overwrites the shared option
		 * "controller/common/product/import/csv/mapping" if you need a
		 * specific setting for the job controller. Otherwise, you should
		 * use the shared option for consistency.
		 *
		 * @param array Associative list of processor names and lists of key/position pairs
		 * @since 2015.05
		 * @category Developer
		 * @see controller/jobs/product/import/csv/domains
		 * @see controller/jobs/product/import/csv/skip-lines
		 * @see controller/jobs/product/import/csv/converter
		 * @see controller/jobs/product/import/csv/strict
		 * @see controller/jobs/product/import/csv/backup
		 * @see controller/common/product/import/csv/max-size
		 */
		$mappings = $config->get( 'controller/jobs/product/import/csv/mapping', $mappings );


		/** controller/common/product/import/csv/converter
		 * List of converter names for the values at the position in the CSV file
		 *
		 * Not all data in the CSV file is already in the required format. Maybe
		 * the text encoding isn't UTF-8, the date is not in ISO format or something
		 * similar. In order to convert the data before it's imported, you can
		 * specify a list of converter objects that should be applied to the data
		 * from the CSV file.
		 *
		 * To each field in the CSV file, you can apply one or more converters,
		 * e.g. to encode a Latin text to UTF8 for the second CSV field:
		 *
		 *  array( 1 => 'Text/LatinUTF8' )
		 *
		 * Similarly, you can also apply several converters at once to the same
		 * field:
		 *
		 *  array( 1 => array( 'Text/LatinUTF8', 'DateTime/EnglishISO' ) )
		 *
		 * It would convert the data of the second CSV field first to UTF-8 and
		 * afterwards try to translate it to an ISO date format.
		 *
		 * The available converter objects are named "MW_Convert_<type>_<conversion>"
		 * where <type> is the data type and <conversion> the way of the conversion.
		 * In the configuration, the type and conversion must be separated by a
		 * slash (<type>/<conversion>).
		 *
		 * '''Note:''' Keep in mind that the position of the CSV fields start at
		 * zero (0). If you only need to convert a few fields, you don't have to
		 * configure all fields. Only specify the positions in the array you
		 * really need!
		 *
		 * @param array Associative list of position/converter name (or list of names) pairs
		 * @since 2015.05
		 * @category Developer
		 * @see controller/common/product/import/csv/domains
		 * @see controller/common/product/import/csv/mapping
		 * @see controller/common/product/import/csv/max-size
		 */
		$converters = $config->get( 'controller/common/product/import/csv/converter', array() );

		/** controller/jobs/product/import/csv/converter
		 * List of converter names for the values at the position in the CSV file
		 *
		 * This configuration setting overwrites the shared option
		 * "controller/common/product/import/csv/converter" if you need a
		 * specific setting for the job controller. Otherwise, you should
		 * use the shared option for consistency.
		 *
		 * @param array Associative list of position/converter name (or list of names) pairs
		 * @since 2015.05
		 * @category Developer
		 * @see controller/jobs/product/import/csv/domains
		 * @see controller/jobs/product/import/csv/mapping
		 * @see controller/jobs/product/import/csv/skip-lines
		 * @see controller/jobs/product/import/csv/strict
		 * @see controller/jobs/product/import/csv/backup
		 * @see controller/common/product/import/csv/max-size
		 */
		$converters = $config->get( 'controller/jobs/product/import/csv/converter', $converters );


		/** controller/common/product/import/csv/max-size
		 * Maximum number of CSV rows to import at once
		 *
		 * It's more efficient to read and import more than one row at a time
		 * to speed up the import. Usually, the bigger the chunk that is imported
		 * at once, the less time the importer will need. The downside is that
		 * the amount of memory required by the import process will increase as
		 * well. Therefore, it's a trade-off between memory consumption and
		 * import speed.
		 *
		 * @param integer Number of rows
		 * @since 2015.05
		 * @category Developer
		 * @see controller/common/product/import/csv/domains
		 * @see controller/common/product/import/csv/mapping
		 * @see controller/common/product/import/csv/converter
		 */
		$maxcnt = (int) $config->get( 'controller/common/product/import/csv/max-size', 1000 );


		/** controller/jobs/product/import/csv/skip-lines
		 * Number of rows skipped in front of each CSV files
		 *
		 * Some CSV files contain header information describing the content of
		 * the column values. These data is for informational purpose only and
		 * can't be imported into the database. Using this option, you can
		 * define the number of lines that should be left out before the import
		 * begins.
		 *
		 * @param integer Number of rows
		 * @since 2015.08
		 * @category Developer
		 * @see controller/jobs/product/import/csv/domains
		 * @see controller/jobs/product/import/csv/mapping
		 * @see controller/jobs/product/import/csv/converter
		 * @see controller/jobs/product/import/csv/strict
		 * @see controller/jobs/product/import/csv/backup
		 * @see controller/common/product/import/csv/max-size
		 */
		$skiplines = (int) $config->get( 'controller/jobs/product/import/csv/skip-lines', 0 );


		/** controller/jobs/product/import/csv/strict
		 * Log all columns from the file that are not mapped and therefore not imported
		 *
		 * Depending on the mapping, there can be more columns in the CSV file
		 * than those which will be imported. This can be by purpose if you want
		 * to import only selected columns or if you've missed to configure one
		 * or more columns. This configuration option will log all columns that
		 * have not been imported if set to true. Otherwise, the left over fields
		 * in the imported line will be silently ignored.
		 *
		 * @param boolen True if not imported columns should be logged, false if not
		 * @since 2015.08
		 * @category User
		 * @category Developer
		 * @see controller/jobs/product/import/csv/domains
		 * @see controller/jobs/product/import/csv/mapping
		 * @see controller/jobs/product/import/csv/skip-lines
		 * @see controller/jobs/product/import/csv/converter
		 * @see controller/jobs/product/import/csv/backup
		 * @see controller/common/product/import/csv/max-size
		 */
		$strict = (bool) $config->get( 'controller/jobs/product/import/csv/strict', true );


		/** controller/jobs/product/import/csv/backup
		 * Name of the backup for sucessfully imported files
		 *
		 * After a CSV file was imported successfully, you can move it to another
		 * location, so it won't be imported again and isn't overwritten by the
		 * next file that is stored at the same location in the file system.
		 *
		 * You should use an absolute path to be sure but can be relative path
		 * if you absolutely know from where the job will be executed from. The
		 * name of the new backup location can contain placeholders understood
		 * by the PHP strftime() function to create dynamic paths, e.g. "backup/%Y-%m-%d"
		 * which would create "backup/2000-01-01". For more information about the
		 * strftime() placeholders, please have a look into the PHP documentation of
		 * the {@link http://php.net/manual/en/function.strftime.php strftime() function}.
		 *
		 * '''Note:''' If no backup name is configured, the file or directory
		 * won't be moved away. Please make also sure that the parent directory
		 * and the new directory are writable so the file or directory could be
		 * moved.
		 *
		 * @param integer Name of the backup file, optionally with date/time placeholders
		 * @since 2015.05
		 * @category Developer
		 * @see controller/jobs/product/import/csv/domains
		 * @see controller/jobs/product/import/csv/mapping
		 * @see controller/jobs/product/import/csv/skip-lines
		 * @see controller/jobs/product/import/csv/converter
		 * @see controller/jobs/product/import/csv/strict
		 * @see controller/common/product/import/csv/max-size
		 */
		$backup = $config->get( 'controller/jobs/product/import/csv/backup' );


		if( !isset( $mappings['item'] ) || !is_array( $mappings['item'] ) )
		{
			$msg = sprintf( 'Required mapping key "%1$s" is missing or contains no array', 'item' );
			throw new Controller_Jobs_Exception( $msg );
		}

		try
		{
			$procMappings = $mappings;
			unset( $procMappings['item'] );

			$convlist = $this->getConverterList( $converters );
			$processor = $this->getProcessors( $procMappings );
			$container = $this->getContainer();
			$path = $container->getName();

			$msg = sprintf( 'Started product import from "%1$s" (%2$s)', $path, __CLASS__ );
			$logger->log( $msg, MW_Logger_Base::NOTICE );

			foreach( $container as $content )
			{
				$name = $content->getName();

				for( $i = 0; $i < $skiplines; $i++ ) {
					$content->next();
				}

				while( ( $data = $this->getData( $content, $maxcnt ) ) !== array() )
				{
					$data = $this->convertData( $convlist, $data );
					$products = $this->getProducts( array_keys( $data ), $domains );
					$errcnt = $this->import( $products, $data, $mappings['item'], $processor, $strict );
					$chunkcnt = count( $data );

					$msg = 'Imported product lines from "%1$s": %2$d/%3$d (%4$s)';
					$logger->log( sprintf( $msg, $name, $chunkcnt - $errcnt, $chunkcnt, __CLASS__ ), MW_Logger_Base::NOTICE );

					$errors += $errcnt;
					$total += $chunkcnt;
					unset( $products, $data );
				}
			}

			$container->close();
		}
		catch( Exception $e )
		{
			$logger->log( 'Product import error: ' . $e->getMessage() );
			$logger->log( $e->getTraceAsString() );

			throw new Controller_Jobs_Exception( $e->getMessage() );
		}

		$msg = 'Finished product import from "%1$s": %2$d successful, %3$s errors, %4$s total (%5$s)';
		$logger->log( sprintf( $msg, $path, $total - $errors, $errors, $total, __CLASS__ ), MW_Logger_Base::NOTICE );

		if( $errors > 0 )
		{
			$msg = sprintf( 'Invalid product lines in "%1$s": %2$d/%3$d', $path, $errors, $total );
			throw new Controller_Jobs_Exception( $msg );
		}

		if( !empty( $backup ) && @rename( $path, strftime( $backup ) ) === false ) {
			throw new Controller_Jobs_Exception( sprintf( 'Unable to move imported file' ) );
		}
	}


	/**
	 * Opens and returns the container which includes the product data
	 *
	 * @return MW_Container_Iface Container object
	 */
	protected function getContainer()
	{
		$config = $this->getContext()->getConfig();

		/** controller/jobs/product/import/csv/location
		 * File or directory where the content is stored which should be imported
		 *
		 * You need to configure the file or directory that acts as container
		 * for the CSV files that should be imported. It should be an absolute
		 * path to be sure but can be relative path if you absolutely know from
		 * where the job will be executed from.
		 *
		 * The path can point to any supported container format as long as the
		 * content is in CSV format, e.g.
		 * * Directory container / CSV file
		 * * Zip container / compressed CSV file
		 * * PHPExcel container / PHPExcel sheet
		 *
		 * @param string Absolute file or directory path
		 * @since 2015.05
		 * @category Developer
		 * @category User
		 * @see controller/jobs/product/import/csv/container/type
		 * @see controller/jobs/product/import/csv/container/content
		 * @see controller/jobs/product/import/csv/container/options
		*/
		$location = $config->get( 'controller/jobs/product/import/csv/location', '.' );

		/** controller/jobs/product/import/csv/container/type
		 * Nave of the container type to read the data from
		 *
		 * The container type tells the importer how it should retrieve the data.
		 * There are currently three container types that support the necessary
		 * CSV content:
		 * * Directory
		 * * Zip
		 * * PHPExcel
		 *
		 * '''Note:''' For the PHPExcel container, you need to install the
		 * "ai-container" extension.
		 *
		 * @param string Container type name
		 * @since 2015.05
		 * @category Developer
		 * @category User
		 * @see controller/jobs/product/import/csv/location
		 * @see controller/jobs/product/import/csv/container/content
		 * @see controller/jobs/product/import/csv/container/options
		*/
		$container = $config->get( 'controller/jobs/product/import/csv/container/type', 'Directory' );

		/** controller/jobs/product/import/csv/container/content
		 * Name of the content type inside the container to read the data from
		 *
		 * The content type must always be a CSV-like format and there are
		 * currently two format types that are supported:
		 * * CSV
		 * * PHPExcel
		 *
		 * '''Note:''' for the PHPExcel content type, you need to install the
		 * "ai-container" extension.
		 *
		 * @param array Content type name
		 * @since 2015.05
		 * @category Developer
		 * @category User
		 * @see controller/jobs/product/import/csv/location
		 * @see controller/jobs/product/import/csv/container/type
		 * @see controller/jobs/product/import/csv/container/options
		*/
		$content = $config->get( 'controller/jobs/product/import/csv/container/content', 'CSV' );

		/** controller/jobs/product/import/csv/container/options
		 * List of file container options for the product import files
		 *
		 * Some container/content type allow you to hand over additional settings
		 * for configuration. Please have a look at the article about
		 * {@link http://aimeos.org/docs/Developers/Utility/Create_and_read_files container/content files}
		 * for more information.
		 *
		 * @param array Associative list of option name/value pairs
		 * @since 2015.05
		 * @category Developer
		 * @category User
		 * @see controller/jobs/product/import/csv/location
		 * @see controller/jobs/product/import/csv/container/content
		 * @see controller/jobs/product/import/csv/container/type
		*/
		$options = $config->get( 'controller/jobs/product/import/csv/container/options', array() );

		return MW_Container_Factory::getContainer( $location, $container, $content, $options );
	}


	/**
	 * Imports the CSV data and creates new products or updates existing ones
	 *
	 * @param array $products List of products items implementing MShop_Product_Item_Iface
	 * @param array $data Associative list of import data as index/value pairs
	 * @param array $mappings Associative list of positions and domain item keys
	 * @param Controller_Common_Product_Import_Csv_Processor_Iface $processor Processor object
	 * @param boolean $strict Log columns not mapped or silently ignore them
	 * @return integer Number of products that couldn't be imported
	 * @throws Controller_Jobs_Exception
	 */
	protected function import( array $products, array $data, array $mapping,
		Controller_Common_Product_Import_Csv_Processor_Iface $processor, $strict )
	{
		$errors = 0;
		$context = $this->getContext();
		$manager = MShop_Factory::createManager( $context, 'product' );

		foreach( $data as $code => $list )
		{
			$remaining = array();

			$manager->begin();

			try
			{
				if( isset( $products[$code] ) ) {
					$product = $products[$code];
				} else {
					$product = $manager->createItem();
				}

				$map = $this->getMappedData( $mapping, $list );

				$typecode = ( isset( $map['product.type'] ) ? $map['product.type'] : 'default' );
				$map['product.typeid'] = $this->getTypeId( 'product/type', 'product', $typecode );

				$product->fromArray( $this->addItemDefaults( $map ) );
				$manager->saveItem( $product );

				$remaining = $processor->process( $product, $list );

				$manager->commit();
			}
			catch( Exception $e )
			{
				$manager->rollback();

				$msg = sprintf( 'Unable to import product with code "%1$s": %2$s', $code, $e->getMessage() );
				$context->getLogger()->log( $msg );

				$errors++;
			}

			if( $strict && !empty( $remaining ) ) {
				$context->getLogger()->log( 'Not imported: ' . print_r( $remaining, true ) );
			}
		}

		return $errors;
	}


	/**
	 * Adds the product item default values and returns the resulting array
	 *
	 * @param array $list Associative list of domain item keys and their values, e.g. "product.status" => 1
	 * @return array Given associative list enriched by default values if they were not already set
	 */
	protected function addItemDefaults( array $list )
	{
		if( !isset( $list['product.status'] ) ) {
			$list['product.status'] = 1;
		}

		return $list;
	}
}
