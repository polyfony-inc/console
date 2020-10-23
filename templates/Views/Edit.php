<?php 
use Polyfony\Locales as Loc; 
use Polyfony\Form as Form;
use Polyfony\Router as Router;
use Polyfony\Form\Token as Token;
use Bootstrap\Alert as Alert;
?>
<div class="container-fluid">

	<div class="row justify-content-center">

		<div class="col-12 col-md-10 col-lg-5">

			<?= Alert::flash(); ?>

			<form 
			class="card card-default form form-horizontal mt-5" 
			action="" 
			method="post" 
			enctype="multipart/form-data">

				<?= new Token; ?>

				<div class="card-header lead">

					<span class="fa fa-edit"></span> 
					<?= Loc::get('Edit'); ?> __Singular__ 

					<code>
						ID N° <?= $__singular__->get('id'); ?>
					</code>

					<button 
					type="submit" 
					class="btn btn-primary btn-sm float-right ml-3">

						<span class="fa fa-save"></span> 
						<?= Loc::get('Save'); ?>

					</button>

					<a 
					href="<?= $__singular__->getUrl('delete'); ?>" 
					class="btn btn-sm btn-link text-danger float-right ml-1">

						<span class="fa fa-trash"></span> 
						<?= Loc::get('Delete'); ?>

					</a>

					<a 
					href="<?= Router::reverse('__table__'); ?>" 
					class="btn btn-sm btn-link float-right">

						<span class="fa fa-chevron-left"></span> 
						<?= Loc::get('Back'); ?>

					</a>

				</div>
				<div class="card-body">

__Fields__

				</div>

			</form>

		</div>

	</div>

</div>
