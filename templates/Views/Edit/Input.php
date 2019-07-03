					<div class="form-group row">

						<div class="control-label col-md-4">

							<span class="fa fa-"></span> 
							<?= Loc::get('__column__'); ?>

						</div>

						<div class="col-md-8">
						
							<?= $this->__Singular__->input(
								'__column__',
								[
									'class'			=>'form-control __AdditionnalClasses__',
									'placeholder'	=>Loc::get('__column__')
								]		
							); ?>

						</div>

					</div>