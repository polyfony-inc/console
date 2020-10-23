					<div class="form-group row">

						<div class="control-label col-md-4">

							<span class="fa fa-list"></span> 
							<?= Loc::get('__column__'); ?>

						</div>

						<div class="col-md-8">
						
							<?= $__singular__->select(
								'__column__',
								[], 
								// Models\__Table__::__column__,
								// Models\__Relation__::idAsKey(),
								[
									'class'			=>'form-control',
									'placeholder'	=>'',
									//'type'		=>''
								]		
							); ?>

						</div>

					</div>