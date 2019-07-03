							<th>
								<?= Form::input(
									'__Table__[__column__]',
									null,
									[
										'class'			=>'form-control',
										'placeholder'	=>
											Loc::get('Search for') . ' ' . 
											Loc::get('__column__')
									]
								); ?>
							</th>
