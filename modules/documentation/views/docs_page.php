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


		<div><?= $page_content ?></div>

		<?php
		if (segment(4) !== 'dedication') { ?>
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
		<?php
	}
	?>

	</div>
</div>
