							<th>
								<?= Form::input(
									'__Table__[__column__][__ConditionType__]',
									null,
									[
										'class'			=>'form-control',
										'placeholder'	=>
											Loc::get('Search for') . ' ' . 
											Loc::get('__column__')
									]
								); ?>
							</th>
