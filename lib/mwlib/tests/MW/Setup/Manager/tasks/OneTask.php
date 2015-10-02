<?php



class MW_Setup_Task_OneTask extends MW_Setup_Task_Abstract
{
	public function getPreDependencies()
	{
		return array();
	}


	public function getPostDependencies()
	{
		return array( 'TwoTask');
	}


	protected function mysql()
	{
		$this->status( '' );
		$this->msg( 'Executing OneTask' );
		$this->status( 'OK' );
	}
}
