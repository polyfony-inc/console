<?php 
use Polyfony\Locales as Loc; 
use Polyfony\Form as Form;
use Polyfony\Router as Router;
use Polyfony\Form\Token as Token;
use Bootstrap\Alert as Alert;
?>
<div class="container">

	<div class="row justify-content-center">

		<div class="col-12 col-md-10 col-lg-6">

			<?= Alert::flash(); ?>

			<form 
			class="card card-default form form-horizontal" 
			action="" 
			method="post" 
			enctype="multipart/form-data">

				<?= new Token; ?>

				<div class="card-heading lead" style="margin:0">

					<span class="fa fa-pencil"></span> 
					__Singular__ 
					<?= Loc::get('Edition'); ?>

					<button 
					type="submit" 
					class="btn btn-link btn-sm pull-right">

						<span class="fa fa-save"></span> 
						<?= Loc::get('Save'); ?>

					</button>

					<a 
					href="<?= $this->__Singular__->getUrl('delete'); ?>" 
					class="btn btn-sm btn-link text-danger pull-right">

						<span class="fa fa-trash"></span> 
						<?= Loc::get('Delete'); ?>

					</a>

					<a 
					href="<?= Router::reverse('__table__'); ?>" 
					class="btn btn-sm btn-link pull-right">

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
