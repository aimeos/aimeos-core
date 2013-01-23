<?php   

/**
* @copyright Copyright (c) Metaways Infosystems GmbH, 2013
* @license LGPLv3, http://www.arcavias.com/en/license
* @version $Id:$
*/


/**
* Adds column type to table mshop_order_base_product_attr and mshop_order_base_service_attr.
*/ 
class MW_Setup_Task_OrderBaseAttrAddType extends MW_Setup_Task_Abstract
{  
	private $_mysql = array(
			'ALTER TABLE "mshop_order_base_product_attr" ADD "type" VARCHAR(32) AFTER "name"',
			'ALTER TABLE "mshop_order_base_service_attr" ADD "type" VARCHAR(32) AFTER "name"',
	);
	
	
	/**
	* Returns the list of task names which this task depends on.
	*
	* @return array List of task names
	*/
	public function getPreDependencies()
	{
		return array('TablesCreateMShop');
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
		$this->_process($this->_mysql);	
	}	
	
	
	/**
	* Add column to table if the column doesn't exist.
	*
	* @param array $sql List of SQL statements to execute for adding columns
	*/
	protected function _process(array $sql)
	{
		$this->_msg ('Add column type to attribute table');
		if ($this->_schema->tableExists('mshop_order_base_product_attr') === true 
		    && $this->_schema->columnExists('mshop_order_base_product_attr', 'type') === false) 
		{
			$this->_executeList($sql);
			$this->_status('added');
		}
		else if ($this->_schema->tableExists('mshop_order_base_service_attr') === true 
		    && $this->_schema->columnExists('mshop_order_base_service_attr', 'type') === false) 
		{
			$this->_executeList($sql);
			$this->_status('added');
		}
		else 
		{
			$this->_status('OK');
		}
	}
}
?>