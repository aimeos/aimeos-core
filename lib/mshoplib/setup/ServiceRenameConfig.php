<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Renames configuration values for service providers.
 */
class MW_Setup_Task_ServiceRenameConfig extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'UPDATE "mshop_service" AS ms, "mshop_service_type" AS mstype
			SET "config" = REPLACE("config", \'"project"\', \'"default.project"\')
			WHERE ms."provider" = \'Default\' AND "config" LIKE \'%"project"%\' AND ms."typeid" = mstype."id" AND mstype."code" = \'delivery\'',
		'UPDATE "mshop_service" AS ms, "mshop_service_type" AS mstype
			SET "config" = REPLACE("config", \'"url"\', \'"default.url"\')
			WHERE ms."provider" = \'Default\' AND "config" LIKE \'%"url"%\' AND ms."typeid" = mstype."id" AND mstype."code" = \'delivery\'',
		'UPDATE "mshop_service" AS ms, "mshop_service_type" AS mstype
			SET "config" = REPLACE("config", \'"username"\', \'"default.username"\')
			WHERE ms."provider" = \'Default\' AND "config" LIKE \'%"username"%\' AND ms."typeid" = mstype."id" AND mstype."code" = \'delivery\'',
		'UPDATE "mshop_service" AS ms, "mshop_service_type" AS mstype
			SET "config" = REPLACE("config", \'"password"\', \'"default.password"\')
			WHERE ms."provider" = \'Default\' AND "config" LIKE \'%"password"%\' AND ms."typeid" = mstype."id" AND mstype."code" = \'delivery\'',
		'UPDATE "mshop_service" AS ms, "mshop_service_type" AS mstype
			SET "config" = REPLACE("config", \'"ssl"\', \'"default.ssl"\')
			WHERE ms."provider" = \'Default\' AND "config" LIKE \'%"ssl"%\' AND ms."typeid" = mstype."id" AND mstype."code" = \'delivery\'',

		'UPDATE "mshop_service" AS ms, "mshop_service_type" AS mstype
			SET "config" = REPLACE("config", \'"ApiUsername"\', \'"paypalexpress.ApiUsername"\')
			WHERE ms."provider" = \'PayPalExpress\' AND "config" LIKE \'%"ApiUsername"%\' AND ms."typeid" = mstype."id" AND mstype."code" = \'payment\'',
		'UPDATE "mshop_service" AS ms, "mshop_service_type" AS mstype
			SET "config" = REPLACE("config", \'"ApiPassword"\', \'"paypalexpress.ApiPassword"\')
			WHERE ms."provider" = \'PayPalExpress\' AND "config" LIKE \'%"ApiPassword"%\' AND ms."typeid" = mstype."id" AND mstype."code" = \'payment\'',
		'UPDATE "mshop_service" AS ms, "mshop_service_type" AS mstype
			SET "config" = REPLACE("config", \'"ApiSignature"\', \'"paypalexpress.ApiSignature"\')
			WHERE ms."provider" = \'PayPalExpress\' AND "config" LIKE \'%"ApiSignature"%\' AND ms."typeid" = mstype."id" AND mstype."code" = \'payment\'',
		'UPDATE "mshop_service" AS ms, "mshop_service_type" AS mstype
			SET "config" = REPLACE("config", \'"ApiEndpoint"\', \'"paypalexpress.ApiEndpoint"\')
			WHERE ms."provider" = \'PayPalExpress\' AND "config" LIKE \'%"ApiEndpoint"%\' AND ms."typeid" = mstype."id" AND mstype."code" = \'payment\'',
		'UPDATE "mshop_service" AS ms, "mshop_service_type" AS mstype
			SET "config" = REPLACE("config", \'"ReturnUrl"\', \'"payment.url-success"\')
			WHERE ms."provider" = \'PayPalExpress\' AND "config" LIKE \'%"ReturnUrl"%\' AND ms."typeid" = mstype."id" AND mstype."code" = \'payment\'',
		'UPDATE "mshop_service" AS ms, "mshop_service_type" AS mstype
			SET "config" = REPLACE("config", \'"CancelUrl"\', \'"payment.url-cancel"\')
			WHERE ms."provider" = \'PayPalExpress\' AND "config" LIKE \'%"CancelUrl"%\' AND ms."typeid" = mstype."id" AND mstype."code" = \'payment\'',
		'UPDATE "mshop_service" AS ms, "mshop_service_type" AS mstype
			SET "config" = REPLACE("config", \'"PaymentAction"\', \'"paypalexpress.PaymentAction"\')
			WHERE ms."provider" = \'PayPalExpress\' AND "config" LIKE \'%"PaymentAction"%\' AND ms."typeid" = mstype."id" AND mstype."code" = \'payment\'',
		'UPDATE "mshop_service" AS ms, "mshop_service_type" AS mstype
			SET "config" = REPLACE("config", \'"PaypalUrl"\', \'"paypalexpress.PaypalUrl"\')
			WHERE ms."provider" = \'PayPalExpress\' AND "config" LIKE \'%"PaypalUrl"%\' AND ms."typeid" = mstype."id" AND mstype."code" = \'payment\'',
		'UPDATE "mshop_service" AS ms, "mshop_service_type" AS mstype
			SET "config" = REPLACE("config", \'_express-checkout&TOKEN="\', \'_express-checkout&useraction=commit&token=%1$s"\')
			WHERE ms."provider" = \'PayPalExpress\' AND "config" LIKE \'%"paypalexpress.PaypalUrl"%\' AND ms."typeid" = mstype."id" AND mstype."code" = \'payment\'',
		'UPDATE "mshop_service" AS ms, "mshop_service_type" AS mstype
			SET "config" = REPLACE("config", \'_express-checkout&token="\', \'_express-checkout&useraction=commit&token=%1$s"\')
			WHERE ms."provider" = \'PayPalExpress\' AND "config" LIKE \'%"paypalexpress.PaypalUrl"%\' AND ms."typeid" = mstype."id" AND mstype."code" = \'payment\'',

		'UPDATE "mshop_service" AS ms, "mshop_service_type" AS mstype
			SET "config" = REPLACE("config", \'"payment.directdebit."\', \'"directdebit."\')
			WHERE ms."provider" = \'DirectDebit\' AND "config" LIKE \'%"payment.directdebit."%\' AND ms."typeid" = mstype."id" AND mstype."code" = \'payment\'',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
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
		return array();
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process( $this->_mysql );
	}


	/**
	 * Renames the service configuration if necessary.
	 *
	 * @param array $stmts List of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Renaming service configuration', 0 );

		if( $this->_schema->tableExists( 'mshop_service' )
			&& $this->_schema->columnExists( 'mshop_service', 'config' ) === true
		) {
			$cntRows = 0;

			foreach( $stmts as $stmt )
			{
				$result = $this->_conn->create( $stmt )->execute();
				$cntRows += $result->affectedRows();
				$result->finish();
			}

			if( $cntRows > 0 ) {
				$this->_status( sprintf( 'done (%1$d)', $cntRows ) );
			} else {
				$this->_status( 'OK' );
			}
		}
		else
		{
			$this->_status( 'OK' );
		}
	}

}
