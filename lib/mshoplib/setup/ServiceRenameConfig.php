<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Renames configuration values for service providers.
 */
class ServiceRenameConfig extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'UPDATE "mshop_service"
			SET "config" = REPLACE("config", \'"project"\', \'"default.project"\')
			WHERE "provider" = \'Default\' AND "config" LIKE \'%"project"%\' AND "typeid" IN (
				SELECT "id" FROM "mshop_service_type" WHERE "code" = \'delivery\'
			)
		',
		'UPDATE "mshop_service"
			SET "config" = REPLACE("config", \'"url"\', \'"default.url"\')
			WHERE "provider" = \'Default\' AND "config" LIKE \'%"url"%\' AND "typeid" IN (
				SELECT "id" FROM "mshop_service_type" WHERE "code" = \'delivery\'
			)
		',
		'UPDATE "mshop_service"
			SET "config" = REPLACE("config", \'"username"\', \'"default.username"\')
			WHERE "provider" = \'Default\' AND "config" LIKE \'%"username"%\' AND "typeid" IN (
				SELECT "id" FROM "mshop_service_type" WHERE "code" = \'delivery\'
			)
		',
		'UPDATE "mshop_service"
			SET "config" = REPLACE("config", \'"password"\', \'"default.password"\')
			WHERE "provider" = \'Default\' AND "config" LIKE \'%"password"%\' AND "typeid" IN (
				SELECT "id" FROM "mshop_service_type" WHERE "code" = \'delivery\'
			)
		',
		'UPDATE "mshop_service"
			SET "config" = REPLACE("config", \'"ssl"\', \'"default.ssl"\')
			WHERE "provider" = \'Default\' AND "config" LIKE \'%"ssl"%\' AND "typeid" IN (
				SELECT "id" FROM "mshop_service_type" WHERE "code" = \'delivery\'
			)
		',

		'UPDATE "mshop_service"
			SET "config" = REPLACE("config", \'"ApiUsername"\', \'"paypalexpress.ApiUsername"\')
			WHERE "provider" = \'PayPalExpress\' AND "config" LIKE \'%"ApiUsername"%\' AND "typeid" IN (
				SELECT "id" FROM "mshop_service_type" WHERE "code" = \'payment\'
			)
		',
		'UPDATE "mshop_service"
			SET "config" = REPLACE("config", \'"ApiPassword"\', \'"paypalexpress.ApiPassword"\')
			WHERE "provider" = \'PayPalExpress\' AND "config" LIKE \'%"ApiPassword"%\' AND "typeid" IN (
				SELECT "id" FROM "mshop_service_type" WHERE "code" = \'payment\'
			)
		',
		'UPDATE "mshop_service"
			SET "config" = REPLACE("config", \'"ApiSignature"\', \'"paypalexpress.ApiSignature"\')
			WHERE "provider" = \'PayPalExpress\' AND "config" LIKE \'%"ApiSignature"%\' AND "typeid" IN (
				SELECT "id" FROM "mshop_service_type" WHERE "code" = \'payment\'
			)
		',
		'UPDATE "mshop_service"
			SET "config" = REPLACE("config", \'"ApiEndpoint"\', \'"paypalexpress.ApiEndpoint"\')
			WHERE "provider" = \'PayPalExpress\' AND "config" LIKE \'%"ApiEndpoint"%\' AND "typeid" IN (
				SELECT "id" FROM "mshop_service_type" WHERE "code" = \'payment\'
			)
		',
		'UPDATE "mshop_service"
			SET "config" = REPLACE("config", \'"ReturnUrl"\', \'"payment.url-success"\')
			WHERE "provider" = \'PayPalExpress\' AND "config" LIKE \'%"ReturnUrl"%\' AND "typeid" IN (
				SELECT "id" FROM "mshop_service_type" WHERE "code" = \'payment\'
			)
		',
		'UPDATE "mshop_service"
			SET "config" = REPLACE("config", \'"CancelUrl"\', \'"payment.url-cancel"\')
			WHERE "provider" = \'PayPalExpress\' AND "config" LIKE \'%"CancelUrl"%\' AND "typeid" IN (
				SELECT "id" FROM "mshop_service_type" WHERE "code" = \'payment\'
			)
		',
		'UPDATE "mshop_service"
			SET "config" = REPLACE("config", \'"PaymentAction"\', \'"paypalexpress.PaymentAction"\')
			WHERE "provider" = \'PayPalExpress\' AND "config" LIKE \'%"PaymentAction"%\' AND "typeid" IN (
				SELECT "id" FROM "mshop_service_type" WHERE "code" = \'payment\'
			)
		',
		'UPDATE "mshop_service"
			SET "config" = REPLACE("config", \'"PaypalUrl"\', \'"paypalexpress.PaypalUrl"\')
			WHERE "provider" = \'PayPalExpress\' AND "config" LIKE \'%"PaypalUrl"%\' AND "typeid" IN (
				SELECT "id" FROM "mshop_service_type" WHERE "code" = \'payment\'
			)
		',
		'UPDATE "mshop_service"
			SET "config" = REPLACE("config", \'_express-checkout&TOKEN="\', \'_express-checkout&useraction=commit&token=%1$s"\')
			WHERE "provider" = \'PayPalExpress\' AND "config" LIKE \'%"paypalexpress.PaypalUrl"%\' AND "typeid" IN (
				SELECT "id" FROM "mshop_service_type" WHERE "code" = \'payment\'
			)
		',
		'UPDATE "mshop_service"
			SET "config" = REPLACE("config", \'_express-checkout&token="\', \'_express-checkout&useraction=commit&token=%1$s"\')
			WHERE "provider" = \'PayPalExpress\' AND "config" LIKE \'%"paypalexpress.PaypalUrl"%\' AND "typeid" IN (
				SELECT "id" FROM "mshop_service_type" WHERE "code" = \'payment\'
			)
		',
		'UPDATE "mshop_service"
			SET "config" = REPLACE("config", \'"payment.directdebit."\', \'"directdebit."\')
			WHERE "provider" = \'DirectDebit\' AND "config" LIKE \'%"payment.directdebit."%\' AND "typeid" IN (
				SELECT "id" FROM "mshop_service_type" WHERE "code" = \'payment\'
			)
		',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'ServiceRenameDomainToTypeid' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return [];
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	public function migrate()
	{
		$this->process( $this->mysql );
	}


	/**
	 * Renames the service configuration if necessary.
	 *
	 * @param array $stmts List of SQL statements to execute.
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Renaming service configuration', 0 );

		if( $this->schema->tableExists( 'mshop_service' )
			&& $this->schema->columnExists( 'mshop_service', 'config' ) === true
		) {
			$cntRows = 0;
			$conn = $this->acquire( 'db-service' );

			foreach( $stmts as $stmt )
			{
				$result = $conn->create( $stmt )->execute();
				$cntRows += $result->affectedRows();
				$result->finish();
			}

			$this->release( $conn, 'db-service' );

			if( $cntRows > 0 ) {
				$this->status( sprintf( 'done (%1$d)', $cntRows ) );
			} else {
				$this->status( 'OK' );
			}
		}
		else
		{
			$this->status( 'OK' );
		}
	}

}
