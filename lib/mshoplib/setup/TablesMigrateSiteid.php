<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Updates site ID columns
 */
class TablesMigrateSiteid extends \Aimeos\MW\Setup\Task\Base
{
	private $resources = [
		'db-attribute' => [
			'mshop_attribute_type', 'mshop_attribute_list_type', 'mshop_attribute_property_type',
			'mshop_attribute_list', 'mshop_attribute_property', 'mshop_attribute'
		],
		'db-cache' => [
			'madmin_cache_tag', 'madmin_cache'
		],
		'db-catalog' => [
			'mshop_catalog_list_type', 'mshop_catalog_list', 'mshop_catalog'
		],
		'db-coupon' => [
			'mshop_coupon_code', 'mshop_coupon'
		],
		'db-customer' => [
			'mshop_customer_list_type', 'mshop_customer_property_type', 'mshop_customer_group',
			'mshop_customer_property', 'mshop_customer_list', 'mshop_customer_address', 'mshop_customer',
		],
		'db-job' => [
			'madmin_job',
		],
		'db-locale' => [
			'mshop_locale_site', 'mshop_locale',
		],
		'db-log' => [
			'madmin_log',
		],
		'db-media' => [
			'mshop_media_type', 'mshop_media_list_type', 'mshop_media_property_type',
			'mshop_media_list', 'mshop_media_property', 'mshop_media'
		],
		'db-order' => [
			'mshop_order_base_product_attr', 'mshop_order_base_service_attr', 'mshop_order_base_coupon',
			'mshop_order_base_product', 'mshop_order_base_service', 'mshop_order_base_address',
			'mshop_order_base', 'mshop_order_status', 'mshop_order'
		],
		'db-plugin' => [
			'mshop_plugin_type', 'mshop_plugin'
		],
		'db-price' => [
			'mshop_price_type', 'mshop_price_list_type', 'mshop_price_list', 'mshop_price'
		],
		'db-product' => [
			'mshop_index_attribute', 'mshop_index_catalog', 'mshop_index_price', 'mshop_index_supplier', 'mshop_index_text',
			'mshop_product_list_type', 'mshop_product_property_type', 'mshop_product_type',
			'mshop_product_list', 'mshop_product_property', 'mshop_product'
		],
		'db-service' => [
			'mshop_service_type', 'mshop_service_list_type', 'mshop_service_list', 'mshop_service'
		],
		'db-stock' => [
			'mshop_stock_type', 'mshop_stock'
		],
		'db-subscription' => [
			'mshop_subscription'
		],
		'db-supplier' => [
			'mshop_supplier_list_type', 'mshop_supplier_list', 'mshop_supplier_address', 'mshop_supplier'
		],
		'db-tag' => [
			'mshop_tag_type', 'mshop_tag'
		],
		'db-text' => [
			'mshop_text_type', 'mshop_text_list_type', 'mshop_text_list', 'mshop_text'
		],
	];


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies() : array
	{
		return ['IndexMigrateTextInnodb'];
	}


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies() : array
	{
		return ['TablesCreateMShop'];
	}


	/**
	 * Executes the task
	 */
	public function migrate()
	{
		$this->msg( 'Update "siteid" columns', 0, '' );

		$this->process( $this->resources );
	}


	protected function addLocaleSiteColumn()
	{
		$rname = 'db-locale';
		$table = 'mshop_locale_site';
		$schema = $this->getSchema( $rname );

		if( $schema->columnExists( $table, 'siteid' ) === false )
		{
			$this->msg( 'Adding "siteid" column to "mshop_locale_site" table', 1 );

			$dbm = $this->additional->getDatabaseManager();
			$conn = $dbm->acquire( $rname );

			$dbal = $conn->getRawObject();

			if( !( $dbal instanceof \Doctrine\DBAL\Connection ) ) {
				throw new \Aimeos\MW\Setup\Exception( 'Not a DBAL connection' );
			}

			$dbalManager = $dbal->getSchemaManager();
			$config = $dbalManager->createSchemaConfig();

			$tabledef = $dbalManager->listTableDetails( $table );
			$newdef = clone $tabledef;
			$newdef->addColumn( 'siteid', 'integer', ['default' => 0] );

			$src = new \Doctrine\DBAL\Schema\Schema( [$tabledef], [], $config );
			$dest = new \Doctrine\DBAL\Schema\Schema( [$newdef], [], $config );

			$this->update( $src, $dest, $rname );
			$this->execute( 'UPDATE "mshop_locale_site" SET "siteid"="id"' );

			$dbm->release( $conn, $rname );

			$this->status( 'done' );
		}
	}


	protected function getSites()
	{
		$map = [];

		$dbm = $this->additional->getDatabaseManager();
		$conn = $dbm->acquire( 'db-locale' );
		$tconn = $dbm->acquire( 'db-locale' );

		$type = \Aimeos\MW\DB\Statement\Base::PARAM_INT;
		$roots = $conn->create( 'SELECT id, nleft, nright FROM mshop_locale_site WHERE level = 0' )->execute();

		while( $root = $roots->fetch() )
		{
			$sql = 'SELECT id, nleft, nright FROM mshop_locale_site WHERE nleft >= ? and nright <= ? ORDER BY nleft';
			$result = $tconn->create( $sql )->bind( 1, $root['nleft'], $type )->bind( 2, $root['nright'], $type )->execute();

			while( $row = $result->fetch() )
			{
				$map[$row['id']] = $row['id'] . '.';
				$this->map( $result, $row, $map, $row['id'] . '.' );
			}
		}

		$dbm->release( $tconn, 'db-locale' );
		$dbm->release( $conn, 'db-locale' );

		return $map;
	}


	protected function isChild( array $row, array $parent )
	{
		return $row['nleft'] > $parent['nleft'] && $row['nright'] < $parent['nright'];
	}


	protected function map( \Aimeos\MW\DB\Result\Iface $result, array $parent, array &$map, string $site )
	{
		while( $row = $result->fetch() )
		{
			while( $this->isChild( $row, $parent ) )
			{
				$map[$row['id']] = $site . $row['id'] . '.';

				if( ( $row = $this->map( $result, $row, $map, $site . $row['id'] . '.' ) ) === null ) {
					return null;
				}
			}

			return $row;
		}

		return null;
	}


	protected function process( array $resources )
	{
		if( $this->getSchema( 'db-locale' )->tableExists( 'mshop_locale_site' ) === false ) {
			return;
		}

		$this->addLocaleSiteColumn();

		foreach( $resources as $rname => $tables )
		{
			$schema = $this->getSchema( $rname );

			$dbm = $this->additional->getDatabaseManager();
			$conn = $dbm->acquire( $rname );

			$dbal = $conn->getRawObject();

			if( !( $dbal instanceof \Doctrine\DBAL\Connection ) ) {
				throw new \Aimeos\MW\Setup\Exception( 'Not a DBAL connection' );
			}

			$dbalManager = $dbal->getSchemaManager();
			$config = $dbalManager->createSchemaConfig();

			if( $schema->tableExists( 'mshop_locale_site' ) ) { // PostgreSQL workaround
				$dbalManager->tryMethod( 'dropForeignKey', 'mshop_locale_site_siteid_key', 'mshop_locale_site' );
			}

			foreach( $tables as $table )
			{
				$this->msg( sprintf( 'Checking table %1$s', $table ), 1 );
				$colname = null;

				if( $schema->tableExists( $table ) && $schema->columnExists( $table, 'siteid' )
					&& $schema->getColumnDetails( $table, 'siteid' )->getDataType() === 'integer'
				) {
					$colname = 'siteid';
				}

				if( $schema->tableExists( $table ) && $schema->columnExists( $table, 'tsiteid' )
					&& $schema->getColumnDetails( $table, 'tsiteid' )->getDataType() === 'integer'
				) {
					$colname = 'tsiteid';
				}

				if( $colname )
				{
					$tabledef = $dbalManager->listTableDetails( $table );
					$newdef = clone $tabledef;

					foreach( ['fk_macac_tid_tsid', 'fk_mslocla_siteid', 'fk_msloccu_siteid', 'fk_msloc_siteid'] as $foreignkey )
					{
						if( $newdef->hasForeignKey( $foreignkey ) ) {
							$newdef->removeForeignKey( $foreignkey );
						}
					}

					$type = new \Doctrine\DBAL\Types\StringType();
					$newdef->changeColumn( $colname, ['type' => $type, 'length' => 255] );

					$src = new \Doctrine\DBAL\Schema\Schema( [$tabledef], [], $config );
					$dest = new \Doctrine\DBAL\Schema\Schema( [$newdef], [], $config );

					$this->update( $src, $dest, $rname );

					foreach( $this->getSites() as $siteid => $site )
					{
						$stmt = $conn->create( sprintf( 'UPDATE "%1$s" SET "%2$s" = ? WHERE "%2$s" = ? OR "%2$s" = \'\'', $table, $colname ) );
						$result = $stmt->bind( 1, $site )->bind( 2, $siteid )->execute();
					}

					$this->status( 'done' );
				}
				else
				{
					$this->status( 'OK' );
				}
			}

			$dbm->release( $conn, $rname );
		}
	}
}
