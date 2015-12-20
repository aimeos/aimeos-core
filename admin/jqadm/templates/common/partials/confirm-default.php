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
				<button type="button" class="close" data-dismiss="modal" aria-label="<?php echo $enc->attr( $this->translate( 'admin/jqadm', 'Close' ) ); ?>"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><?php echo $enc->html( $this->translate( 'admin/jqadm', 'Delete item' ) ); ?></h4>
			</div>
			<div class="modal-body">
				<p><?php echo $enc->html( $this->translate( 'admin/jqadm', 'You are going to delete this item. Continue?' ) ); ?></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo $enc->html( $this->translate( 'admin/jqadm', 'Delete' ) ); ?></a>
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $enc->html( $this->translate( 'admin/jqadm', 'Close' ) ); ?></button>
			</div>
		</div>
	</div>
</div>
