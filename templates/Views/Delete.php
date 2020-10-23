<?php 
use Polyfony\Locales as Loc; 
use Polyfony\Form\Token as Token;
?>
<div class="container-fluid">

	<div class="row justify-content-center">

		<div class="col-12 col-md-10 col-lg-5">

			<form 
			class="card card-default form form-horizontal mt-5" 
			action="" 
			method="post" 
			enctype="multipart/form-data">

				<?= new Token; ?>

				<div class="card-header lead">

					<span class="fa fa-trash"></span> 
					<?= Loc::get('Delete '); ?> __Singular__ 

					<code>
						ID N° <?= $__singular__->get('id'); ?>
					</code>

				</div>
				<div class="card-body">

					<p class="lead text-center">
						<?= Loc::get('You_are_about_to_delete'); ?> 
						<strong>
							__Singular__ ID <code>N° <?= $__singular__->get('id'); ?></code>
						</strong> 
						<br />
						<strong>
							<?= Loc::get('Are_you_sure'); ?> ?
						</strong>
					</p>


				</div>
				<div class="card-footer text-right">

					<a 
					href="<?= $__singular__->getUrl('edit'); ?>" 
					class="btn btn-sm btn-default float-left">
						<span class="fa fa-chevron-left"></span> 
						<?= Loc::get('Cancel'); ?>
					</a>

					<button 
					type="submit" 
					class="btn btn-sm btn-danger">
						<span class="fa fa-trash"></span> 
						<?= Loc::get('Delete'); ?>
					</button>

				</div>

			</form>

		</div>

	</div>

</div>
