<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015
 */

?>
<?php $this->block()->start( 'catalog/suggest' ); ?>
<?php echo json_encode( $this->get( 'suggestTextItems', '' ) ); ?>
<?php echo $this->get( 'suggestBody' ); ?>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'catalog/suggest' ); ?>
