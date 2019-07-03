<?php 
use Polyfony\Locales as Loc; 
?>
<div class="container">

	<div class="row">

		<div class="col-md-6 col-md-offset-6">

			<form 
			class="panel panel-default form form-horizontal" 
			action="" 
			method="post" 
			enctype="multipart/form-data">

				<div class="panel-heading lead" style="margin:0">

					<span class="fa fa-trash"></span> 
					<?= Loc::get('Delete '); ?> __Singular__ ID N° 
					<?= $this->__Singular__->get('id'); ?>

				</div>
				<div class="panel-body">

					<p class="lead text-center">
						<?= Loc::get('You_are_about_to_delete'); ?> 
						<strong>
							__Singular__ ID <code>N° <?= $this->__Singular__->get('id'); ?></code>
						</strong> 
						<br />
						<strong>
							<?= Loc::get('Are_you_sure'); ?> ?
						</strong>
					</p>


				</div>
				<div class="panel-footer text-right">

					<a 
					href="<?= $__Singular__->getUrl('edit'); ?>" 
					class="btn btn-sm btn-default">
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
