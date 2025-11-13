<div class="docs-page-content" style="width: 840px; margin: 0 auto; padding-bottom: 200px;">

<div class="page-nav-btns">
	<div><?= anchor($prev_url, '<i class="fa fa-arrow-left"></i> Prev', array('class' => 'button button-primary')) ?></div>
	<div><?= anchor($next_url, 'Next <i class="fa fa-arrow-right"></i>',  array('class' => 'button button-primary')) ?></div>
</div>

<?= $page_obj->page_content ?>
	
<div class="page-nav-btns">
	<div><?= anchor($prev_url, '<i class="fa fa-arrow-left"></i> Prev', array('class' => 'button button-primary')) ?></div>
	<div><?= anchor($next_url, 'Next <i class="fa fa-arrow-right"></i>',  array('class' => 'button button-primary')) ?></div>
</div>

</div>

<style>
h1 {
	text-align: center;
	margin: 1em 0;
}

</style>