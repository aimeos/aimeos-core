<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2023
 */


namespace Aimeos\Upscheme\Task;


class CustomerRenameGroup extends Base
{
	public function before() : array
	{
		return ['Customer', 'Group'];
	}


	public function up()
	{
		$this->info( 'Rename mshop_customer_group to mshop_group', 'vv' );

		$db = $this->db( 'db-customer' );

		if( $db->hasTable( 'mshop_customer_group' ) )
		{
			$db->dropIndex( 'mshop_customer_group', 'pk_mscusgr_id' )
				->dropIndex( 'mshop_customer_group', 'unq_mscusgr_code_sid' )
				->dropIndex( 'mshop_customer_group', 'idx_mscusgr_label_sid' )
				->renameTable( 'mshop_customer_group', 'mshop_group' );
		}

		$this->update( 'mshop_customer_list' );
	}


	protected function update( string $table )
	{
		$db = $this->db( 'db-customer' );

		if( !$db->hasTable( $table ) ) {
			return;
		}

		$result = $db->query( 'SELECT
			' . $db->qi( 'id' ) . ',
			' . $db->qi( 'type' ) . ',
			' . $db->qi( 'refid' ) . '
			FROM ' . $db->qi( $table ) . '
			WHERE domain = ?',
			['customer/group']
		);

		foreach( $result->iterateAssociative() as $row )
		{
			$db->update(
				'mshop_customer_list',
				['key' => 'group|' . $row['type'] . '|' . $row['refid'], 'domain' => 'group'],
				['id' => $row['id']]
			);
		}
	}
}
