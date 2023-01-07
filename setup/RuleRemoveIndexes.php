<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2023
 */


namespace Aimeos\Upscheme\Task;


class RuleRemoveIndexes extends Base
{
	public function after() : array
	{
		return ['Rule'];
	}


	public function up()
	{
		$this->info( 'Remove rule indexes with siteid column first', 'vv' );

		$this->db( 'db-rule' )
			->dropIndex( 'mshop_rule', 'idx_msrul_sid_prov' )
			->dropIndex( 'mshop_rule', 'idx_msrul_sid_status' )
			->dropIndex( 'mshop_rule', 'idx_msrul_sid_label' )
			->dropIndex( 'mshop_rule', 'idx_msrul_sid_pos' )
			->dropIndex( 'mshop_rule', 'idx_msrul_sid_start' )
			->dropIndex( 'mshop_rule', 'idx_msrul_sid_end' )
			->dropIndex( 'mshop_rule_type', 'unq_msrulty_sid_dom_code' )
			->dropIndex( 'mshop_rule_type', 'idx_msrulty_sid_status_pos' )
			->dropIndex( 'mshop_rule_type', 'idx_msrulty_sid_label' )
			->dropIndex( 'mshop_rule_type', 'idx_msrulty_sid_code' );
	}
}
