<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

$enc = $this->encoder();
$errors = $this->get( 'errors', array() );

?>
<?php if( !empty( $errors ) ) : ?>
<ul class="error-list alert alert-danger" role="alert">
<?php	foreach( $errors as $key => $error ) : ?>
	<li class="error-item" data-key="<?php echo $enc->attr( $key ); ?>">
		<span class="fa fa-exclamation-circle" aria-hidden="true"></span>
		<span class="sr-only"><?php echo $enc->html( $this->translate( 'admin', 'Error' ) ); ?></span>
		<?php echo $enc->html( $error ); ?>
	</li>
<?php	endforeach; ?>
</ul>
<?php endif; ?>