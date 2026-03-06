<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2026
 */


namespace Aimeos\Upscheme\Task;


class CatalogSetPath extends Base
{
	public function before() : array
	{
		return ['Catalog'];
	}


	public function up()
	{
		$this->info( 'Set catalog materialized path', 'vv' );

		$db = $this->db( 'db-catalog' );

		if( !$db->hasTable( 'mshop_catalog' ) || !$db->hasColumn( 'mshop_catalog', 'pathid' ) ) {
			return;
		}

		$result = $db->stmt()->select( 'id', 'parentid' )->from( 'mshop_catalog' )->executeQuery();
		$nodes = [];

		while( $row = $result->fetchAssociative() ) {
			$nodes[$row['id']] = $row['parentid'];
		}

		foreach( $nodes as $id => $parentid )
		{
			$path = [$id];

			$pid = $parentid;
			while( $pid && isset( $nodes[$pid] ) ) {
				array_unshift( $path, $pid );
				$pid = $nodes[$pid];
			}

			$pathStr = implode( '.', $path ) . '.';

			$db->update( 'mshop_catalog', ['pathid' => $pathStr], ['id' => $id] );
		}
	}
}
