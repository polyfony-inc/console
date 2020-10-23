<?php
use Polyfony\Locales as Loc;
use Polyfony\Form as Form;
use Polyfony\Router as Router;
use Bootstrap\Alert as Alert;
?>
<div class="container-fluid">
	<div class="row justify-content-center">

		<div class="col-12">

			<?= Alert::flash(); ?>

			<h1>
				
				<span class="fa fa-__Icon__"></span>
				__Table__ 
				(<?= count($__table__); ?>)

				<a 
				href="<?= Router::reverse('__table__', ['action'=>'create']); ?>" 
				class="btn btn-sm btn-primary float-right m-3">

					<span class="fa fa-plus-circle"></span> 
					<?= Loc::get('Create_new'); ?> 
					<strong>
						__Singular__
					</strong>

				</a>

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
					<?php foreach($__table__ as $__singular__): ?>
						<tr>

							<!-- Columns -->
							
__Columns__

							<!-- Columns -->

							<td class="text-right">

								<a 
								href="<?= $__singular__->getUrl('delete'); ?>" 
								class="btn btn-sm btn-light text-danger" 
								data-toggle="tooltip" 
								data-placement="top" 
								title="<?= Loc::get('Delete'); ?>">

									<span class="fa fa-trash"></span> 
									
								</a>

								<a 
								href="<?= $__singular__->getUrl(); ?>" 
								class="btn btn-sm btn-light">

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
