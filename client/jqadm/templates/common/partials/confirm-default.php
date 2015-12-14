<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

$enc = $this->encoder();

?>
<div id="confirm-delete" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="<?php echo $enc->attr( $this->translate( 'client/jqadm', 'Close' ) ); ?>"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><?php echo $enc->html( $this->translate( 'client/jqadm', 'Delete item' ) ); ?></h4>
			</div>
			<div class="modal-body">
				<p><?php echo $enc->html( $this->translate( 'client/jqadm', 'You are going to delete this item. Continue?' ) ); ?></p>
			</div>
			<div class="modal-footer">
				<a class="btn btn-danger" href="#"><?php echo $enc->html( $this->translate( 'client/jqadm', 'Delete' ) ); ?></a>
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $enc->html( $this->translate( 'client/jqadm', 'Close' ) ); ?></button>
			</div>
		</div>
	</div>
</div>
