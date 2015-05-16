<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Job controller for product sitemap.
 *
 * @package Controller
 * @subpackage Jobs
 */
class Controller_Jobs_Product_Export_Sitemap_Default
	extends Controller_Jobs_Product_Export_Default
	implements Controller_Jobs_Interface
{
	/**
	 * Returns the localized name of the job.
	 *
	 * @return string Name of the job
	 */
	public function getName()
	{
		return $this->_getContext()->getI18n()->dt( 'controller/jobs', 'Product site map' );
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription()
	{
		return $this->_getContext()->getI18n()->dt( 'controller/jobs', 'Creates a product site map for search engines' );
	}


	/**
	 * Executes the job.
	 *
	 * @throws Controller_Jobs_Exception If an error occurs
	 */
	public function run()
	{
		$container = $this->_createContainer();

		$files = $this->_export( $container );
		$this->_createSitemapIndex( $container, $files );

		$container->close();
	}


	/**
	 * Adds the given products to the content object for the site map file
	 *
	 * @param MW_Container_Content_Interface $content File content object
	 * @param MShop_Product_Item_Interface[] $items List of product items
	 */
	protected function _addItems( MW_Container_Content_Interface $content, array $items )
	{
		$config = $this->_getContext()->getConfig();

		/** controller/jobs/product/export/sitemap/changefreq
		 * Change frequency of the products
		 *
		 * Depending on how often the product content changes (e.g. price updates)
		 * and the site map files are generated you can give search engines a
		 * hint how often they should reindex your site. The site map schema
		 * allows a few pre-defined strings for the change frequency:
		 * * always
		 * * hourly
		 * * daily
		 * * weekly
		 * * monthly
		 * * yearly
		 * * never
		 *
		 * More information can be found at
		 * {@link http://www.sitemaps.org/protocol.html#xmlTagDefinitions sitemap.org}
		 *
		 * @param string One of the pre-defined strings (see description)
		 * @since 2015.01
		 * @category User
		 * @category Developer
		 * @see controller/jobs/product/export/sitemap/container/options
		 * @see controller/jobs/product/export/sitemap/location
		 * @see controller/jobs/product/export/sitemap/max-items
		 * @see controller/jobs/product/export/sitemap/max-query
		 */
		$changefreq = $config->get( 'controller/jobs/product/export/sitemap/changefreq', 'daily' );

		/** controller/jobs/product/export/sitemap/default/template-items
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
		 * @see controller/jobs/product/export/sitemap/default/template-header
		 * @see controller/jobs/product/export/sitemap/default/template-footer
		 * @see controller/jobs/product/export/sitemap/default/template-index
		 */
		$tplconf = 'controller/jobs/product/export/sitemap/default/template-items';
		$default = 'product/export/sitemap-items-body-default.xml';

		$view = $this->_getContext()->getView();

		$view->siteItems = $items;
		$view->siteFreq = $changefreq;

		$content->add( $view->render( $this->_getTemplate( $tplconf, $default ) ) );
	}


	/**
	 * Creates a new container for the site map file
	 *
	 * @return MW_Container_Interface Container object
	 */
	protected function _createContainer()
	{
		$config = $this->_getContext()->getConfig();

		/** controller/jobs/product/export/sitemap/location
		 * Directory where the generated site maps should be placed into
		 *
		 * The site maps must be publically available for download by the search
		 * engines. Therefore, you have to configure a directory for the site
		 * maps in your web space that is writeable by the process generating
		 * the files, e.g.
		 *
		 * /var/www/yourshop/your/sitemap/path
		 *
		 * The location of the site map index file should then be
		 * added to the robots.txt in the document root of your domain:
		 *
		 * Sitemap: https://www.yourshop.com/your/sitemap/path/aimeos-sitemap-index.xml
		 *
		 * The "sitemapindex-aimeos.xml" file is the site map index file that
		 * references the real site map files which contains the links to the
		 * products. Please make sure that the protocol and domain
		 * (https://www.yourshop.com/) is the same as the ones used in the
		 * product links!
		 *
		 * More details about site maps can be found at
		 * {@link http://www.sitemaps.org/protocol.html sitemaps.org}
		 *
		 * @param string Absolute directory to store the site maps into
		 * @since 2015.01
		 * @category Developer
		 * @category User
		 * @see controller/jobs/product/export/sitemap/container/options
		 * @see controller/jobs/product/export/sitemap/max-items
		 * @see controller/jobs/product/export/sitemap/max-query
		 * @see controller/jobs/product/export/sitemap/changefreq
		 */
		$location = $config->get( 'controller/jobs/product/export/sitemap/location', sys_get_temp_dir() );

		/** controller/jobs/product/export/sitemap/container/options
		 * List of file container options for the site map files
		 *
		 * The directory and the generated site map files are stored using
		 * container/content objects from the core, namely the "Directory"
		 * container and the "Binary" content classes. Both implementations
		 * support some options:
		 * * dir-perm (default: 0755): Permissions if the directory must be created
		 * * gzip-level (default: 5): GZip compression level from 0 to 9 (0 = fast, 9 = best)
		 *
		 * @param array Associative list of option name/value pairs
		 * @since 2015.01
		 * @category Developer
		 * @see controller/jobs/product/export/sitemap/location
		 * @see controller/jobs/product/export/sitemap/max-items
		 * @see controller/jobs/product/export/sitemap/max-query
		 * @see controller/jobs/product/export/sitemap/changefreq
		*/
		$options = $config->get( 'controller/jobs/product/export/sitemap/container/options', array() );

		return MW_Container_Factory::getContainer( $location, 'Directory', 'Gzip', $options );
	}


	/**
	 * Creates a new site map content object
	 *
	 * @param MW_Container_Interface $container Container object
	 * @param integer $filenum New file number
	 * @return MW_Container_Content_Interface New content object
	 */
	protected function _createContent( MW_Container_Interface $container, $filenum )
	{
		$config = $this->_getContext()->getConfig();

		/** controller/jobs/product/export/sitemap/default/template-header
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
		 * @see controller/jobs/product/export/sitemap/default/template-items
		 * @see controller/jobs/product/export/sitemap/default/template-footer
		 * @see controller/jobs/product/export/sitemap/default/template-index
		 */
		$tplconf = 'controller/jobs/product/export/sitemap/default/template-header';
		$default = 'product/export/sitemap-items-header-default.xml';

		$view = $this->_getContext()->getView();

		$content = $container->create( $this->_getFilename( $filenum ) );
		$content->add( $view->render( $this->_getTemplate( $tplconf, $default ) ) );
		$container->add( $content );

		return $content;
	}


	/**
	 * Closes the site map content object
	 *
	 * @param MW_Container_Content_Interface $content
	 */
	protected function _closeContent( MW_Container_Content_Interface $content )
	{
		$config = $this->_getContext()->getConfig();

		/** controller/jobs/product/export/sitemap/default/template-footer
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
		 * @see controller/jobs/product/export/sitemap/default/template-header
		 * @see controller/jobs/product/export/sitemap/default/template-items
		 * @see controller/jobs/product/export/sitemap/default/template-index
		 */
		$tplconf = 'controller/jobs/product/export/sitemap/default/template-footer';
		$default = 'product/export/sitemap-items-footer-default.xml';

		$view = $this->_getContext()->getView();

		$content->add( $view->render( $this->_getTemplate( $tplconf, $default ) ) );
	}


	/**
	 * Adds the content for the site map index file
	 *
	 * @param MW_Container_Interface $container File container object
	 * @param array $files List of generated site map file names
	 */
	protected function _createSitemapIndex( MW_Container_Interface $container, array $files )
	{
		$config = $this->_getContext()->getConfig();

		/** controller/jobs/product/export/sitemap/default/template-index
		 * Relative path to the XML site map index template of the product site map job controller.
		 *
		 * The template file contains the XML code and processing instructions
		 * to generate the site map index files. The configuration string is the path
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
		 * @param string Relative path to the template creating XML code for the site map index
		 * @since 2015.01
		 * @category Developer
		 * @see controller/jobs/product/export/sitemap/default/template-header
		 * @see controller/jobs/product/export/sitemap/default/template-items
		 * @see controller/jobs/product/export/sitemap/default/template-footer
		 */
		$tplconf = 'controller/jobs/product/export/sitemap/default/template-index';
		$default = 'product/export/sitemap-index-default.xml';

		$view = $this->_getContext()->getView();
		$view->siteFiles = $files;

		$content = $container->create( 'aimeos-sitemap-index.xml' );
		$content->add( $view->render( $this->_getTemplate( $tplconf, $default ) ) );
		$container->add( $content );
	}


	/**
	 * Returns the configuration value for the given name
	 *
	 * @param string $name One of "domain", "max-items" or "max-query"
	 * @param mixed $default Default value if name is unknown
	 * @return mixed Configuration value
	 */
	protected function _getConfig( $name, $default = null )
	{
		$config = $this->_getContext()->getConfig();

		switch( $name )
		{
			case 'domain':
				return array();

			case 'max-items':
				/** controller/jobs/product/export/sitemap/max-items
				 * Maximum number of products per site map
				 *
				 * Each site map file must not contain more than 50,000 links and it's
				 * size must be less than 10MB. If your product URLs are rather long
				 * and one of your site map files is bigger than 10MB, you should set
				 * the number of products per file to a smaller value until each file
				 * is less than 10MB.
				 *
				 * More details about site maps can be found at
				 * {@link http://www.sitemaps.org/protocol.html sitemaps.org}
				 *
				 * @param integer Number of products per file
				 * @since 2015.01
				 * @category Developer
				 * @category User
				 * @see controller/jobs/product/export/sitemap/container/options
				 * @see controller/jobs/product/export/sitemap/location
				 * @see controller/jobs/product/export/sitemap/max-query
				 * @see controller/jobs/product/export/sitemap/changefreq
				 */
				return $config->get( 'controller/jobs/product/export/sitemap/max-items', 50000 );

			case 'max-query':
				/** controller/jobs/product/export/sitemap/max-query
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
				 * @see controller/jobs/product/export/sitemap/container/options
				 * @see controller/jobs/product/export/sitemap/location
				 * @see controller/jobs/product/export/sitemap/max-items
				 * @see controller/jobs/product/export/sitemap/changefreq
				 */
				return $config->get( 'controller/jobs/product/export/sitemap/max-query', 1000 );
		}

		return $default;
	}


	/**
	 * Returns the file name for the new content file
	 *
	 * @param integer $number Current file number
	 * @return string New file name
	 */
	protected function _getFilename( $number )
	{
		return sprintf( 'aimeos-sitemap-%d.xml', $number );
	}
}
