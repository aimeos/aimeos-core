<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Updates the charset and collations
 */
class TablesUpdateCharsetCollation extends \Aimeos\MW\Setup\Task\Base
{
	private $tables = [
		'db-coupon' => [
			'mshop_attribute_type' => 'code', 'mshop_attribute' => 'code',
			'mshop_attribute_list_type' => 'code', 'mshop_attribute_list' => 'refid',
			'mshop_attribute_property_type' => 'code', 'mshop_attribute_property' => 'value'
		],
		'db-cache' => [
			'cache' => 'value', 'mshop_cache_tag' => 'tname',
		],
		'db-catalog' => [
			'mshop_catalog' => 'code', 'mshop_catalog_list_type' => 'code', 'mshop_catalog_list' => 'refid',
		],
		'db-coupon' => [
			'mshop_coupon' => 'provider', 'mshop_coupon_code' => 'code',
		],
		'db-customer' => [
			'mshop_customer' => 'code', 'mshop_customer_address' => 'email', 'mshop_customer_group' => 'code',
			'mshop_customer_list_type' => 'code', 'mshop_customer_list' => 'refid',
			'mshop_customer_property_type' => 'code', 'mshop_customer_property' => 'value',
		],
		'db-job' => [
			'madmin_job' => 'label',
		],
		'db-locale' => [
			'mshop_locale_site' => 'code', 'mshop_locale' => 'langid',
			'mshop_locale_language' => 'label', 'mshop_locale_currency' => 'label',
		],
		'db-log' => [
			'madmin_log' => 'message',
		],
		'db-media' => [
			'mshop_media_type' => 'code', 'mshop_media' => 'label', 'mshop_media_list_type' => 'code',
			'mshop_media_list' => 'refid', 'mshop_media_property_type' => 'code', 'mshop_media_property' => 'value',
		],
		'db-order' => [
			'mshop_order_base' => 'customerid', 'mshop_order_base_address' => 'email', 'mshop_order_base_coupon' => 'code',
			'mshop_order_base_product' => 'prodcode', 'mshop_order_base_product_attr' => 'code',
			'mshop_order_base_service' => 'code', 'mshop_order_base_service_attr' => 'code',
			'mshop_order' => 'type', 'mshop_order_status' => 'type',
		],
		'db-plugin' => [
			'mshop_plugin_type' => 'code', 'mshop_plugin' => 'type',
		],
		'db-price' => [
			'mshop_price_type' => 'code', 'mshop_price' => 'label',
			'mshop_price_list_type' => 'code', 'mshop_price_list' => 'refid',
		],
		'db-product' => [
			'mshop_index_attribute' => 'code', 'mshop_index_catalog' => 'listtype', 'mshop_index_price' => 'currencyid',
			'mshop_index_supplier' => 'listtype', 'mshop_index_text' => 'name',
			'mshop_product_type' => 'code', 'mshop_product' => 'code', 'mshop_product_list_type' => 'code',
			'mshop_product_list' => 'refid', 'mshop_product_property_type' => 'code', 'mshop_product_property' => 'value',
		],
		'db-queue' => [
			'madmin_queue' => 'queue',
		],
		'db-service' => [
			'mshop_service_type' => 'code', 'mshop_service' => 'code',
			'mshop_service_list_type' => 'code', 'mshop_service_list' => 'refid',
		],
		'db-stock' => [
			'mshop_stock_type' => 'code', 'mshop_stock' => 'type',
		],
		'db-subscription' => [
			'mshop_subscription' => 'interval',
		],
		'db-supplier' => [
			'mshop_supplier' => 'code', 'mshop_supplier_address' => 'email',
			'mshop_supplier_list_type' => 'code', 'mshop_supplier_list' => 'refid',
		],
		'db-tag' => [
			'mshop_tag_type' => 'code', 'mshop_tag' => 'type',
		],
		'db-text' => [
			'mshop_text' => 'label', 'mshop_text_type' => 'code',
			'mshop_text_list_type' => 'code', 'mshop_text_list' => 'refid',
		],
	];


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return [];
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return ['TablesCreateMShop', 'TablesCreateMAdmin'];
	}


	/**
	 * Executes the task
	 */
	public function migrate()
	{
		$this->msg( 'Update charset and collation', 0 );
		$this->status( '' );

		foreach( $this->tables as $rname => $list ) {
			$this->checkTables( $list, $rname );
		}
	}


	protected function checkTables( $tables, $rname )
	{
		$schema = $this->getSchema( $rname );

		if( $this->checkMySqlCompatibility( $schema ) )
		{
			foreach( $tables as $table => $column )
			{
				$this->msg( sprintf( 'Checking table %1$s', $table ), 1 );

				if( $this->checkColumns( $schema, $table, $column ) === true )
				{
					if( $table === 'mshop_locale' && $schema->constraintExists( 'mshop_locale', 'fk_msloc_currid' ) ) {
						$this->execute( 'ALTER TABLE "mshop_locale" DROP FOREIGN KEY "fk_msloc_currid"' );
					}

					if( $table === 'mshop_locale' && $schema->constraintExists( 'mshop_locale', 'fk_msloc_langid' ) ) {
						$this->execute( 'ALTER TABLE "mshop_locale" DROP FOREIGN KEY "fk_msloc_langid"' );
					}

					$this->execute( sprintf( 'ALTER TABLE "%1$s" CONVERT TO CHARACTER SET \'utf8mb4\' COLLATE \'utf8mb4_bin\'', $table ) );
					$this->status( 'done' );
				}
				else
				{
					$this->status( 'OK' );
				}
			}
		}
	}


	protected function checkColumns( \Aimeos\MW\Setup\DBSchema\Iface $schema, $table, $column )
	{
		if( $schema->tableExists( $table ) && $schema->columnExists( $table, $column )
			&& ( $item = $schema->getColumnDetails( $table, $column ) )
			&& ( $item->getCharset() !== 'utf8mb4' || $item->getCollationType() !== 'utf8mb4_bin' )
		) {
			return true;
		}

		return false;
	}

	/**
	 * Checking the MySql compatibility.
	 *
	 * @param \Aimeos\MW\Setup\DBSchema\Iface $schema
	 * @return bool
	 */
	protected function checkMySqlCompatibility( \Aimeos\MW\Setup\DBSchema\Iface $schema )
	{
		if ( !$schema instanceof \Aimeos\MW\Setup\DBSchema\Mysql )
		{
			return true;
		}

		// Ask the database for it's type and version.
		// A MariaDB gets identified as a MySql 5.5.5 by doctrine.
		// We can not rely on it to tell us the actual version and ask
		// the server directly.
		$version = $this->getValue( 'SELECT version() AS "version"', 'version', 'db-customer' );

		if( ( strpos( $version, 'MariaDB' ) !== false && version_compare( $version, '10.2', '>=' ) ) ||
			version_compare( $version, '5.7', '>=' )
		) {
			return true;
		}

		return false;
	}
}