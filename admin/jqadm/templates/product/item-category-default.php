<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

$enc = $this->encoder();

?>
<div class="product-item-category card panel">
	<div id="product-item-category" class="header card-header collapsed" role="tab" data-toggle="collapse" data-parent="#accordion" data-target="#product-item-category-data" aria-expanded="false" aria-controls="product-item-category-data">
		<?php echo $enc->html( $this->translate( 'admin', 'Categories' ) ); ?>
	</div>
	<div id="product-item-category-data" class="item-category card-block panel-collapse collapse" role="tabpanel" aria-labelledby="product-item-category">
		<div class="col-lg-6">
			<table class="category-list table table-default">
				<thead>
					<tr>
						<th><?php echo $enc->html( $this->translate( 'admin', 'Default' ) ); ?></th>
						<th class="actions"><div class="btn btn-primary fa fa-plus"></div></th>
					</tr>
				</thead>
				<tbody>
<?php $listTypeId = $this->get( 'categoryListTypes/default' ); ?>
<?php foreach( $this->get( 'categoryData/catalog.lists.id', array() ) as $idx => $id ) : ?>
<?php 	if( $this->get( 'categoryData/catalog.lists.typeid/' . $idx ) == $listTypeId ) : ?>
					<tr>
						<td>
							<input class="item-listtypeid" type="hidden" name="<?php echo $enc->attr( $this->formparam( array( 'category', 'catalog.lists.typeid', '' ) ) ); ?>"
								value="<?php echo $enc->attr( $listTypeId ); ?>" />
							<input class="item-listid" type="hidden" name="<?php echo $enc->attr( $this->formparam( array( 'category', 'catalog.lists.id', '' ) ) ); ?>"
								value="<?php echo $enc->attr( $id ); ?>" />
							<input class="item-label" type="hidden" name="<?php echo $enc->attr( $this->formparam( array( 'category', 'catalog.label', '' ) ) ); ?>"
								value="<?php echo $enc->attr( $this->get( 'categoryData/catalog.label/' . $idx ) ); ?>" />
							<select class="combobox item-id" name="<?php echo $enc->attr( $this->formparam( array( 'category', 'catalog.id', '' ) ) ); ?>">
								<option value="<?php echo $enc->attr( $this->get( 'categoryData/catalog.id/' . $idx ) ); ?>" ><?php echo $enc->html( $this->get( 'categoryData/catalog.label/' . $idx ) ); ?></option>
							</select>
						</td>
						<td class="actions"><div class="btn btn-danger fa fa-trash"></div></td>
					</tr>
<?php	endif; ?>
<?php endforeach; ?>
					<tr class="prototype">
						<td>
							<input class="item-listtypeid" type="hidden" name="<?php echo $enc->attr( $this->formparam( array( 'category', 'catalog.lists.typeid', '' ) ) ); ?>" value="<?php echo $enc->attr( $listTypeId ); ?>" disabled="disabled" />
							<input class="item-listid" type="hidden" name="<?php echo $enc->attr( $this->formparam( array( 'category', 'catalog.lists.id', '' ) ) ); ?>" value="" disabled="disabled" />
							<input class="item-label" type="hidden" name="<?php echo $enc->attr( $this->formparam( array( 'category', 'catalog.label', '' ) ) ); ?>" value="" disabled="disabled" />
							<select class="combobox-prototype item-id" name="<?php echo $enc->attr( $this->formparam( array( 'category', 'catalog.id', '' ) ) ); ?>" disabled="disabled">
							</select>
						</td>
						<td class="actions"><div class="btn btn-danger fa fa-trash"></div></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="col-lg-6">
			<table class="category-list table table-default">
				<thead>
					<tr>
						<th><?php echo $enc->html( $this->translate( 'admin', 'Promotion' ) ); ?></th>
						<th class="actions"><div class="btn btn-primary fa fa-plus"></div></th>
					</tr>
				</thead>
				<tbody>
<?php $listTypeId = $this->get( 'categoryListTypes/promotion' ); ?>
<?php foreach( $this->get( 'categoryData/catalog.lists.id', array() ) as $idx => $id ) : ?>
<?php 	if( $this->get( 'categoryData/catalog.lists.typeid/' . $idx ) == $listTypeId ) : ?>
					<tr>
						<td>
							<input class="item-listtypeid" type="hidden" name="<?php echo $enc->attr( $this->formparam( array( 'category', 'catalog.lists.typeid', '' ) ) ); ?>"
								value="<?php echo $enc->attr( $listTypeId ); ?>" />
							<input class="item-listid" type="hidden" name="<?php echo $enc->attr( $this->formparam( array( 'category', 'catalog.lists.id', '' ) ) ); ?>"
								value="<?php echo $enc->attr( $id ); ?>" />
							<input class="item-label" type="hidden" name="<?php echo $enc->attr( $this->formparam( array( 'category', 'catalog.label', '' ) ) ); ?>"
								value="<?php echo $enc->attr( $this->get( 'categoryData/catalog.label/' . $idx ) ); ?>" />
							<select class="combobox item-id" name="<?php echo $enc->attr( $this->formparam( array( 'category', 'catalog.id', '' ) ) ); ?>">
								<option value="<?php echo $enc->attr( $this->get( 'categoryData/catalog.id/' . $idx ) ); ?>" ><?php echo $enc->html( $this->get( 'categoryData/catalog.label/' . $idx ) ); ?></option>
							</select>
						</td>
						<td class="actions"><div class="btn btn-danger fa fa-trash"></div></td>
					</tr>
<?php	endif; ?>
<?php endforeach; ?>
					<tr class="prototype">
						<td>
							<input class="item-listtypeid" type="hidden" name="<?php echo $enc->attr( $this->formparam( array( 'category', 'catalog.lists.typeid', '' ) ) ); ?>" value="<?php echo $enc->attr( $listTypeId ); ?>" disabled="disabled" />
							<input class="item-listid" type="hidden" name="<?php echo $enc->attr( $this->formparam( array( 'category', 'catalog.lists.id', '' ) ) ); ?>" value="" disabled="disabled" />
							<input class="item-label" type="hidden" name="<?php echo $enc->attr( $this->formparam( array( 'category', 'catalog.label', '' ) ) ); ?>" value="" disabled="disabled" />
							<select class="combobox-prototype item-id" name="<?php echo $enc->attr( $this->formparam( array( 'category', 'catalog.id', '' ) ) ); ?>" disabled="disabled">
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
