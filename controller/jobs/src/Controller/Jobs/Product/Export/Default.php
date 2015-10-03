<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Job controller for product exports.
 *
 * @package Controller
 * @subpackage Jobs
 */
class Controller_Jobs_Product_Export_Default
	extends Controller_Jobs_Base
	implements Controller_Jobs_Interface
{
	/**
	 * Returns the localized name of the job.
	 *
	 * @return string Name of the job
	 */
	public function getName()
	{
		return $this->getContext()->getI18n()->dt( 'controller/jobs', 'Product export' );
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription()
	{
		return $this->getContext()->getI18n()->dt( 'controller/jobs', 'Exports all available products' );
	}


	/**
	 * Executes the job.
	 *
	 * @throws Controller_Jobs_Exception If an error occurs
	 */
	public function run()
	{
		$container = $this->createContainer();
		$this->export( $container );
		$container->close();
	}


	/**
	 * Adds the given products to the content object for the site map file
	 *
	 * @param MW_Container_Content_Interface $content File content object
	 * @param MShop_Product_Item_Interface[] $items List of product items
	 */
	protected function addItems( MW_Container_Content_Interface $content, array $items )
	{
		$config = $this->getContext()->getConfig();

		/** controller/jobs/product/export/default/template-items
		 * Relative path to the XML items template of the product site map job controller.
		 *
		 * The template file contains the XML code and processing instructions
		 * to generate the site map files. The configuration string is the path
		 * to the template file relative to the layouts directory (usually in
		 * controller/jobs/layouts).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "default" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "default"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating XML code for the site map items
		 * @since 2015.01
		 * @category Developer
		 * @see client/html/account/favorite/default/template-header
		 * @see controller/jobs/product/export/default/template-footer
		 * @see controller/jobs/product/export/default/template-index
		 */
		$tplconf = 'controller/jobs/product/export/default/template-items';
		$default = 'product/export/items-body-default.xml';

		$view = $this->getContext()->getView();

		$view->exportItems = $items;

		$content->add( $view->render( $this->getTemplate( $tplconf, $default ) ) );
	}


	/**
	 * Creates a new container for the site map file
	 *
	 * @return MW_Container_Interface Container object
	 */
	protected function createContainer()
	{
		$config = $this->getContext()->getConfig();

		/** controller/jobs/product/export/location
		 * Directory where the generated site maps should be placed into
		 *
		 * You have to configure a directory for the generated files on your
		 * server that is writeable by the process generating the files, e.g.
		 *
		 * /var/www/your/export/path
		 *
		 * @param string Absolute directory to store the exported files into
		 * @since 2015.01
		 * @category Developer
		 * @category User
		 * @see controller/jobs/product/export/default/container/options
		 * @see controller/jobs/product/export/max-items
		 * @see controller/jobs/product/export/max-query
		 */
		$location = $config->get( 'controller/jobs/product/export/location', sys_get_temp_dir() );

		/** controller/jobs/product/export/default/container/type
		 * List of file container options for the export files
		 *
		 * The generated files are stored using container/content objects from
		 * the core.
		 *
		 * @param string Container name
		 * @since 2015.01
		 * @category Developer
		 * @see controller/jobs/product/export/default/container/content
		 * @see controller/jobs/product/export/default/container/options
		 * @see controller/jobs/product/export/location
		 * @see controller/jobs/product/export/max-items
		 * @see controller/jobs/product/export/max-query
		*/
		$container = $config->get( 'controller/jobs/product/export/default/container/type', 'Directory' );

		/** controller/jobs/product/export/default/container/content
		 * List of file container options for the export files
		 *
		 * The generated files are stored using container/content objects from
		 * the core.
		 *
		 * @param array Associative list of option name/value pairs
		 * @since 2015.01
		 * @category Developer
		 * @see controller/jobs/product/export/default/container/type
		 * @see controller/jobs/product/export/default/container/options
		 * @see controller/jobs/product/export/location
		 * @see controller/jobs/product/export/max-items
		 * @see controller/jobs/product/export/max-query
		 */
		$content = $config->get( 'controller/jobs/product/export/default/container/content', 'Binary' );

		/** controller/jobs/product/export/default/container/options
		 * List of file container options for the export files
		 *
		 * The generated files are stored using container/content objects from
		 * the core.
		 *
		 * @param array Associative list of option name/value pairs
		 * @since 2015.01
		 * @category Developer
		 * @see controller/jobs/product/export/default/container/type
		 * @see controller/jobs/product/export/default/container/content
		 * @see controller/jobs/product/export/location
		 * @see controller/jobs/product/export/max-items
		 * @see controller/jobs/product/export/max-query
		 */
		$options = $config->get( 'controller/jobs/product/export/default/container/options', array() );

		return MW_Container_Factory::getContainer( $location, $container, $content, $options );
	}


	/**
	 * Creates a new site map content object
	 *
	 * @param MW_Container_Interface $container Container object
	 * @param integer $filenum New file number
	 * @return MW_Container_Content_Interface New content object
	 */
	protected function createContent( MW_Container_Interface $container, $filenum )
	{
		$config = $this->getContext()->getConfig();

		/** controller/jobs/product/export/default/template-header
		 * Relative path to the XML site map header template of the product site map job controller.
		 *
		 * The template file contains the XML code and processing instructions
		 * to generate the site map header. The configuration string is the path
		 * to the template file relative to the layouts directory (usually in
		 * controller/jobs/layouts).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "default" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "default"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating XML code for the site map header
		 * @since 2015.01
		 * @category Developer
		 * @see controller/jobs/product/export/default/template-items
		 * @see controller/jobs/product/export/default/template-footer
		 * @see controller/jobs/product/export/default/template-index
		 */
		$tplconf = 'controller/jobs/product/export/default/template-header';
		$default = 'product/export/items-header-default.xml';

		$view = $this->getContext()->getView();

		$content = $container->create( $this->getFilename( $filenum ) );
		$content->add( $view->render( $this->getTemplate( $tplconf, $default ) ) );
		$container->add( $content );

		return $content;
	}


	/**
	 * Closes the site map content object
	 *
	 * @param MW_Container_Content_Interface $content
	 */
	protected function closeContent( MW_Container_Content_Interface $content )
	{
		$config = $this->getContext()->getConfig();

		/** controller/jobs/product/export/default/template-footer
		 * Relative path to the XML site map footer template of the product site map job controller.
		 *
		 * The template file contains the XML code and processing instructions
		 * to generate the site map footer. The configuration string is the path
		 * to the template file relative to the layouts directory (usually in
		 * controller/jobs/layouts).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "default" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "default"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating XML code for the site map footer
		 * @since 2015.01
		 * @category Developer
		 * @see controller/jobs/product/export/default/template-header
		 * @see controller/jobs/product/export/default/template-items
		 * @see controller/jobs/product/export/default/template-index
		 */
		$tplconf = 'controller/jobs/product/export/default/template-footer';
		$default = 'product/export/items-footer-default.xml';

		$view = $this->getContext()->getView();

		$content->add( $view->render( $this->getTemplate( $tplconf, $default ) ) );
	}


	/**
	 * Exports the products into the given container
	 *
	 * @param MW_Container_Interface $container Container object
	 * @return array List of content (file) names
	 */
	protected function export( MW_Container_Interface $container )
	{
		$default = array( 'attribute', 'media', 'price', 'product', 'text' );

		$domains = $this->getConfig( 'domains', $default );
		$maxItems = $this->getConfig( 'max-items', 10000 );
		$maxQuery = $this->getConfig( 'max-query', 1000 );

		$start = 0; $filenum = 1;
		$names = array();

		$productManager = MShop_Factory::createManager( $this->getContext(), 'product' );

		$search = $productManager->createSearch( true );
		$search->setSortations( array( $search->sort( '+', 'product.id' ) ) );
		$search->setSlice( 0, $maxQuery );

		$content = $this->createContent( $container, $filenum );
		$names[] = $content->getResource();

		do
		{
			$items = $productManager->searchItems( $search, $domains );
			$this->addItems( $content, $items );

			$count = count( $items );
			$start += $count;
			$search->setSlice( $start, $maxQuery );

			if( $start + $maxQuery > $maxItems * $filenum )
			{
				$this->closeContent( $content );
				$content = $this->createContent( $container, ++$filenum );
				$names[] = $content->getResource();
			}
		}
		while( $count >= $search->getSliceSize() );

		$this->closeContent( $content );

		return $names;
	}


	/**
	 * Returns the configuration value for the given name
	 *
	 * @param string $name One of "domain", "max-items" or "max-query"
	 * @param mixed $default Default value if name is unknown
	 * @return mixed Configuration value
	 */
	protected function getConfig( $name, $default = null )
	{
		$config = $this->getContext()->getConfig();

		switch( $name )
		{
			case 'domain':
				/** controller/jobs/product/export/domains
				 * List of associated items from other domains that should be exported too
				 *
				 * Products consist not only of the base data but also of texts, media
				 * files, prices, attrbutes and other details. Those information is
				 * associated to the products via their lists. Using the "domains" option
				 * you can make more or less associated items available in the template.
				 *
				 * @param array List of domain names
				 * @since 2015.01
				 * @category Developer
				 * @category User
				 * @see controller/jobs/product/export/default/container/type
				 * @see controller/jobs/product/export/default/container/content
				 * @see controller/jobs/product/export/default/container/options
				 * @see controller/jobs/product/export/location
				 * @see controller/jobs/product/export/max-items
				 * @see controller/jobs/product/export/max-query
				 */
				return $config->get( 'controller/jobs/product/export/domains', $default );

			case 'max-items':
				/** controller/jobs/product/export/max-items
				 * Maximum number of exported products per file
				 *
				 * Limits the number of exported products per file as the memory
				 * consumption of processing big files is rather high. Splitting
				 * the data into several files that can also be processed in
				 * parallel is able to speed up importing the files again.
				 *
				 * @param integer Number of products entries per file
				 * @since 2015.01
				 * @category Developer
				 * @category User
				 * @see controller/jobs/product/export/default/container/type
				 * @see controller/jobs/product/export/default/container/content
				 * @see controller/jobs/product/export/default/container/options
				 * @see controller/jobs/product/export/location
				 * @see controller/jobs/product/export/max-query
				 * @see controller/jobs/product/export/domains
				 */
				return $config->get( 'controller/jobs/product/export/max-items', $default );

			case 'max-query':
				/** controller/jobs/product/export/max-query
				 * Maximum number of products per query
				 *
				 * The products are fetched from the database in bunches for efficient
				 * retrieval. The higher the value, the lower the total time the database
				 * is busy finding the records. Higher values also means that record
				 * updates in the tables need to wait longer and the memory consumption
				 * of the PHP process is higher.
				 *
				 * @param integer Number of products per query
				 * @since 2015.01
				 * @category Developer
				 * @see controller/jobs/product/export/default/container/type
				 * @see controller/jobs/product/export/default/container/content
				 * @see controller/jobs/product/export/default/container/options
				 * @see controller/jobs/product/export/location
				 * @see controller/jobs/product/export/max-items
				 * @see controller/jobs/product/export/domains
				 */
				return $config->get( 'controller/jobs/product/export/max-query', $default );
		}

		return $default;
	}


	/**
	 * Returns the file name for the new content file
	 *
	 * @param integer $number Current file number
	 * @return string New file name
	 */
	protected function getFilename( $number )
	{
		return sprintf( 'aimeos-products-%d.xml', $number );
	}
}
