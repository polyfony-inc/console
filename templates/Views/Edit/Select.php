					<div class="form-group row">

						<div class="control-label col-md-4">

							<span class="fa fa-list"></span> 
							<?= Loc::get('__column__'); ?>

						</div>

						<div class="col-md-8">
						
							<?= $this->__Singular__->select(
								'__column__',
								[], 
								// __Table__::__column__
								// __Relation__::idAsKey()
								[
									'class'			=>'form-control',
									'placeholder'	=>'',
									//'type'		=>''
								]		
							); ?>

						</div>

					</div>