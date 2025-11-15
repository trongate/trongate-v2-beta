<div class="docs-page">
	<div>Chapter vibe here</div>

	<div>

		<div class="page-nav-btns mt-3">
			<div><?= anchor($prev_url, '<i class="fa fa-arrow-left"></i> Prev', array('class' => 'button button-primary')) ?></div>
			<?php
			if ($next_url !== '') {
				?>
				<div><?= anchor($next_url, 'Next <i class="fa fa-arrow-right"></i>',  array('class' => 'button button-primary')) ?></div>
				<?php
			}
			?>
			
		</div>


<h3 class="text-center mt-7">Chapter <?= $chapter_number-1 ?></h3>
<h1><?= $chapter_title ?></h1>



	</div>
</div>



