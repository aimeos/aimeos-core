<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();

?>
<?php $this->block()->start( 'locale/select' ); ?>
<section class="aimeos locale-select">
<?php if( ( $errors = $this->get( 'selectErrorList', array() ) ) !== array() ) : ?>
	<ul class="error-list">
<?php foreach( $errors as $error ) : ?>
		<li class="error-item"><?php echo $enc->html( $error ); ?></li>
<?php endforeach; ?>
	</ul>
<?php endif; ?>
<?php echo $this->get( 'selectBody' ); ?>
</section>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'locale/select' ); ?>
