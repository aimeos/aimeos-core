<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Remove incompatible index tables
 */
class IndexRemoveIncompatibleTables extends \Aimeos\MW\Setup\Task\Base
{
    /**
     * Returns the list of task names which this task depends on.
     *
     * @return string[] List of task names
     */
    public function getPreDependencies()
    {
        return [];
    }


    /**
     * Returns the list of task names which depends on this task.
     *
     * @return string[] List of task names
     */
    public function getPostDependencies()
    {
        return ['TablesCreateMShop', 'IndexRebuild'];
    }


    /**
     * Executes the task
     */
    public function migrate()
    {
        $this->msg('Remove incompatible index tables', 0);
        $this->status('');

        $schema = $this->getSchema('db-product');

        $table = 'mshop_index_text';
        $this->msg(sprintf('Checking table "%1$s"', $table), 1);

        if ($schema->tableExists($table) === true
            && $schema->columnExists($table, 'url') === false) {
            $this->execute('DROP TABLE "mshop_index_text"');
            IndexRebuild::forceExecute();

            $this->status('done');
        } else {
            $this->status('OK');
        }
    }
}
