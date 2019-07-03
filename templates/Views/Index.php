<?php
use Polyfony\Locales as Loc;
use Polyfony\Form as Form;
?>
<div class="container">
	<div class="row">

		<div class="col-12">

			<h1>
				
				<span class="fa fa-__Icon__"></span>
				__Table__ 
				(<?= count($this->__Table__); ?>

			</h1>

			<table class="table table-striped table-hover">

				<thead>
					<!-- Legend -->
					<tr>

__Legend__

						<th>

						</th>

					</tr>
					<!-- Legend -->

					<!-- Filters -->
					<form 
					action="" 
					method="post">
						<tr>

__Filters__

							<th>
								<button 
								type="submit" 
								class="btn btn-primary btn-block">

									<span class="fa fa-search"></span> 
									<?= Loc::get('Search'); ?>

								</button>
							</th>
	
						</tr>
					</form>
					<!-- Filters -->
				</thead>

				<tbody>
					<?php foreach($this->__Table__ as $__Singular__): ?>
						<tr>

							<!-- Columns -->
							
__Columns__

							<!-- Columns -->

							<td class="text-right">

								<a 
								href="<?= $__Singular__->getUrl('delete'); ?>" 
								class="btn btn-xs btn-link" 
								data-toggle="tooltip" 
								data-placement="top" 
								title="<?= Loc::get('Delete'); ?>">

									<span class="fa fa-trash"></span> 
									
								</a>

								<a 
								href="<?= $__Singular__->getUrl(); ?>" 
								class="btn btn-xs btn-link">

									<?= Loc::get('Open'); ?>
									<span class="fa fa-chevron-right"></span> 

								</a>

							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>

			</table>
		</div>
	</div>
</div>
