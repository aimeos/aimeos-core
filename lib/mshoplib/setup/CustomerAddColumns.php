<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds address columns to customer table.
 */
class MW_Setup_Task_CustomerAddColumns extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'code' => 'ALTER TABLE "mshop_customer" ADD "code" VARCHAR(32) NOT NULL AFTER "label"',
		'salutation' => 'ALTER TABLE "mshop_customer" ADD "salutation" varchar(8) NOT NULL AFTER "code"',
		'company' => 'ALTER TABLE "mshop_customer" ADD "company" varchar(100) NOT NULL AFTER "salutation"',
		'title' => 'ALTER TABLE "mshop_customer" ADD "title" varchar(64) NOT NULL AFTER "company"',
		'firstname' => 'ALTER TABLE "mshop_customer" ADD "firstname" varchar(64) NOT NULL AFTER "title"',
		'lastname' => 'ALTER TABLE "mshop_customer" ADD "lastname" varchar(64) NOT NULL AFTER "firstname"',
		'address1' => 'ALTER TABLE "mshop_customer" ADD "address1" varchar(255) NOT NULL AFTER "lastname"',
		'address2' => 'ALTER TABLE "mshop_customer" ADD "address2" varchar(255) NOT NULL AFTER "address1"',
		'address3' => 'ALTER TABLE "mshop_customer" ADD "address3" varchar(255) NOT NULL AFTER "address2"',
		'postal' => 'ALTER TABLE "mshop_customer" ADD "postal" varchar(16) NOT NULL AFTER "address3"',
		'city' => 'ALTER TABLE "mshop_customer" ADD "city" varchar(255) NOT NULL AFTER "postal"',
		'state' => 'ALTER TABLE "mshop_customer" ADD "state" varchar(255) NOT NULL AFTER "city"',
		'langid' => 'ALTER TABLE "mshop_customer" ADD "langid" CHAR(2) NULL AFTER "state", ADD CONSTRAINT "fk_mscust_langid" FOREIGN KEY ("langid") REFERENCES "mshop_locale_language" ("id") ON UPDATE CASCADE ON DELETE NO ACTION',
		'countryid' => 'ALTER TABLE "mshop_customer" ADD "countryid" char(2) NOT NULL AFTER "langid"',
		'telephone' => 'ALTER TABLE "mshop_customer" ADD "telephone" varchar(32) NOT NULL AFTER "countryid"',
		'email' => 'ALTER TABLE "mshop_customer" ADD "email" varchar(255) NOT NULL AFTER "telephone"',
		'telefax' => 'ALTER TABLE "mshop_customer" ADD "telefax" varchar(255) NOT NULL AFTER "email"',
		'website' => 'ALTER TABLE "mshop_customer" ADD "website" varchar(255) NOT NULL AFTER "telefax"',
		'birthday' => 'ALTER TABLE "mshop_customer" ADD "birthday" date NULL AFTER "website"',
		'password' => 'ALTER TABLE "mshop_customer" ADD "password" VARCHAR(255) NOT NULL AFTER "birthday"',
		'ctime' => 'ALTER TABLE "mshop_customer" ADD "ctime" DATETIME NOT NULL AFTER "password"',
		'mtime' => 'ALTER TABLE "mshop_customer" ADD "mtime" DATETIME NOT NULL AFTER "ctime"',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'GlobalMoveTablesToLocale' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return array('TablesCreateMShop');
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process( $this->_mysql );
	}


	/**
	 * Add column to table if the column doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( array $stmts )
	{

		$table = 'mshop_customer';
		$this->_msg( sprintf( 'Adding columns to table "%1$s"', $table ), 0 ); $this->_status( '' );

		foreach( $stmts AS $column=>$stmt )
		{
			$this->_msg( sprintf( 'Checking table "%1$s" for column "%2$s": ', $table, $column ), 1 );

			if( $this->_schema->tableExists( $table )
				&& $this->_schema->columnExists( $table, $column ) === false )
			{
				$this->_execute( $stmt );
				$this->_status( 'added' );
			} else {
				$this->_status( 'OK' );
			}
		}
	}
}