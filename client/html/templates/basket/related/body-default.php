<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

$enc = $this->encoder();

?>
<section class="aimeos basket-related">
<?php if( isset( $this->relatedErrorList ) ) : ?>
	<ul class="error-list">
<?php foreach( (array) $this->relatedErrorList as $errmsg ) : ?>
		<li class="error-item"><?php echo $enc->html( $errmsg ); ?></li>
<?php endforeach; ?>
	</ul>
<?php endif; ?>
	<h1><?php echo $enc->html( $this->translate( 'client/html', 'Related' ), $enc::TRUST ); ?></h1>
<?php echo $this->get( 'relatedBody' ); ?>
</section>
