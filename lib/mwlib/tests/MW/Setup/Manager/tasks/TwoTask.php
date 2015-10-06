<?php



class MW_Setup_Task_TwoTask extends MW_Setup_Task_Abstract
{
	public function getPreDependencies()
	{
		return array();
	}


	public function getPostDependencies()
	{
		return array();
	}


	protected function mysql()
	{
		$this->msg( 'Executing TwoTask' );
		$this->status( 'OK' );
	}
}
