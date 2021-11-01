<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */


namespace Aimeos\Upscheme\Task;


class Text extends Base
{
	public function up()
	{
		$this->info( 'Creating text schema', 'v' );
		$db = $this->db( 'db-text' );

		foreach( $this->paths( 'default/schema/text.php' ) as $filepath )
		{
			if( ( $list = include( $filepath ) ) === false ) {
				throw new \RuntimeException( sprintf( 'Unable to get schema from file "%1$s"', $filepath ) );
			}

			foreach( $list['table'] ?? [] as $name => $fcn ) {
				$db->table( $name, $fcn );
			}
		}

		$db->up();

		if( !$db->hasIndex( 'mshop_text', 'idx_mstex_sid_dom_cont' ) )
		{
			$db->for( 'mysql', 'CREATE INDEX `idx_mstex_sid_dom_cont` ON `mshop_text` (`siteid`, `domain`, `content`(255))' );
			$db->for( 'postgresql', 'CREATE INDEX "idx_mstex_sid_dom_cont" ON "mshop_text" ("siteid", "domain", left("content", 255) )' );
			$db->for( 'mssql', 'CREATE INDEX "idx_mstex_sid_dom_cont" ON "mshop_text" ("siteid", "domain")' );
		}
	}
}
