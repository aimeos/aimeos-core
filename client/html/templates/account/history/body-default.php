<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();

?>
<?php $this->block()->start( 'account/history' ); ?>
<section class="aimeos account-history">
<?php if( ( $errors = $this->get( 'historyErrorList', array() ) ) !== array() ) : ?>
	<ul class="error-list">
<?php foreach( $errors as $error ) : ?>
		<li class="error-item"><?php echo $enc->html( $error ); ?></li>
<?php endforeach; ?>
	</ul>
<?php endif; ?>
<?php echo $this->get( 'historyBody' ); ?>
</section>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'account/history' ); ?>
