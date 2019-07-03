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

				</div>
				<div class="panel-body">

__Fields__

				</div>

			</form>

		</div>

	</div>

</div>
