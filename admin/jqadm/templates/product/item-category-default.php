<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

$enc = $this->encoder();

?>
<div class="catalog-item-category card panel">
	<div id="catalog-item-category" class="header card-header collapsed" role="tab" data-toggle="collapse" data-parent="#accordion" data-target="#catalog-item-category-data" aria-expanded="false" aria-controls="catalog-item-category-data">
		<?php echo $enc->html( $this->translate( 'admin', 'Categories' ) ); ?>
	</div>
	<div id="catalog-item-category-data" class="item-category card-block panel-collapse collapse" role="tabpanel" aria-labelledby="catalog-item-category">
		<div class="col-lg-6">
			<table class="category-list table table-default">
				<thead>
					<tr>
						<th><?php echo $enc->html( $this->translate( 'admin', 'Categories' ) ); ?></th>
						<th class="actions"><div class="btn btn-primary fa fa-plus"></div></th>
					</tr>
				</thead>
				<tbody>
<?php foreach( $this->get( 'categoryData/catalog.lists.id', array() ) as $idx => $id ) : ?>
					<tr>
						<td>
							<input class="item-listid" type="hidden" name="category[catalog.lists.id][]" value="<?php echo $enc->attr( $id ); ?>" />
							<input class="item-label" type="hidden" name="category[catalog.label][]" value="<?php echo $enc->attr( $this->get( 'categoryData/catalog.label/' . $idx ) ); ?>" />
							<select class="combobox item-refid" name="category[catalog.lists.refid][]">
								<option value="<?php echo $enc->attr( $this->get( 'categoryData/catalog.lists.refid/' . $idx ) ); ?>" ><?php echo $enc->html( $this->get( 'categoryData/catalog.label/' . $idx ) ); ?></option>
							</select>
						</td>
						<td class="actions"><div class="btn btn-danger fa fa-trash"></div></td>
					</tr>
<?php endforeach; ?>
					<tr class="prototype">
						<td>
							<input class="item-listid" type="hidden" name="category[catalog.lists.id][]" value="" disabled="disabled" />
							<input class="item-label" type="hidden" name="category[catalog.label][]" value="" disabled="disabled" />
							<select class="combobox-prototype item-refid" name="category[catalog.id][]" disabled="disabled">
							</select>
						</td>
						<td class="actions"><div class="btn btn-danger fa fa-trash"></div></td>
					</tr>
				</tbody>
			</table>
		</div>
<?php echo $this->get( 'categoryBody' ); ?>
	</div>
</div>
