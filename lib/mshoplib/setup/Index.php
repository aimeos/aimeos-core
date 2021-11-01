<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */


namespace Aimeos\Upscheme\Task;


class Index extends Base
{
	public function after() : array
	{
		return ['Product'];
	}


	public function up()
	{
		$this->info( 'Creating index schema', 'v' );
		$db = $this->db( 'db-product' );

		foreach( $this->paths( 'default/schema/index.php' ) as $filepath )
		{
			if( ( $list = include( $filepath ) ) === false ) {
				throw new \RuntimeException( sprintf( 'Unable to get schema from file "%1$s"', $filepath ) );
			}

			foreach( $list['table'] ?? [] as $name => $fcn ) {
				$db->table( $name, $fcn );
			}
		}

		$db->up();

		if( !$db->hasIndex( 'mshop_index_text', 'idx_msindte_content' ) )
		{
			$db->for( 'mysql', 'CREATE FULLTEXT INDEX `idx_msindte_content` ON `mshop_index_text` (`content`)' );
			$db->for( 'postgresql', 'CREATE INDEX "idx_msindte_content" ON "mshop_index_text" USING GIN (to_tsvector(\'english\', "content"))' );
		}
	}
}
