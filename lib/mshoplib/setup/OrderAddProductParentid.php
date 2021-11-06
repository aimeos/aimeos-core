<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */


namespace Aimeos\Upscheme\Task;


class OrderAddProductParentid extends Base
{
	public function before() : array
	{
		return ['Order'];
	}


	public function up()
	{
		$db = $this->db( 'db-order' );

		if( !$db->hasColumn( 'mshop_order_base_product', 'parentprodid' ) ) {
			return;
		}

		$this->info( 'Separate product ID and parent ID in order base product table', 'v' );

		$table = $db->table( 'mshop_order_base_product' );
		$table->refid( 'parentprodid' )->up();

		$db->stmt()->update( 'mshop_order_base_product' )
			->set( 'parentprodid', 'prodid' )
			->where( 'type = \'select\'' )
			->execute();

		$result = $db->stmt()->select( 'siteid', 'prodcode' )
			->from( 'mshop_order_base_product' )
			->where( 'type = \'select\'' )
			->execute();

		$map = [];
		while( $row = $result->fetch() ) {
			$map[$row['siteid']][] = $row['prodcode'];
		}

		$context = clone $this->context();
		$manager = \Aimeos\MShop::create( $context, 'locale' );

		foreach( $map as $siteId => $codes )
		{
			$context->setLocale( $manager->create()->setSiteId( $siteId ) );
			$this->update( $context, $codes );
		}
	}


	protected function update( \Aimeos\MShop\Context\Item\Iface $context, array $codes )
	{
		$start = 0; $size = 1000;
		$pmanager = \Aimeos\MShop::create( $context, 'product' );
		$db = $this->db( 'db-order' );

		while( !empty( $list = array_slice( $codes, $start, $size ) ) )
		{
			$filter = $pmanager->filter()->add( ['product.code' => $list] )->slice( 0, $size );

			foreach( $pmanager->search( $filter ) as $product )
			{
				$db->stmt()->update( 'mshop_order_base_product' )
					->set( 'prodid', '?' )
					->where( 'siteid = ?' )->andWhere( 'prodcode = ?' )
					->setParameters( [$product->getId(), $product->getSiteId(), $product->getCode()] )
					->execute();
			}

			$start += $size;
		}
	}
}
