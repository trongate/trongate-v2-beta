<div class="docs-page-content" style="width: 840px; margin: 0 auto; padding-bottom: 200px;">
	<div class="page-nav-btns">
		<div><?= anchor($prev_url, '<i class="fa fa-arrow-left"></i> Prev', array('class' => 'button button-primary')) ?></div>
		<div><?= anchor($next_url, 'Next <i class="fa fa-arrow-right"></i>',  array('class' => 'button button-primary')) ?></div>
	</div>

	<h3 class="text-center mt-5">Chapter <?= $current_chapter_number ?></h3>
	<h1 class="text-center"><?= $current_chapter_title ?></h1>
</div>

<style>
.page-container {
    background-color: #fbfbfb;
}

.card {
    background-color: #fff;
    margin-bottom: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.card-body {
    padding: 1.5rem;
}

.mt-0 {
    margin-top: 0 !important;
}

.toc-list-items {
    list-style: none;
    padding: 0;
    margin: 0;
}

.toc-list-items li {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px dotted #ccc;
}

.toc-list-items a {
    text-decoration: none;
    font-weight: 500;
}

    .container-xxxl {
        width: 94%;
        margin: 0 auto;
        max-width: 1200px;
    }

    .docs-books {
        margin-top: 3em;
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1em;
    }

    .docs-books > div .cover {
        max-width: 80%;
        margin: 0 auto;
    }

    .docs-books > div h3 {
        text-align: center;
    }

    .cover {
        cursor: pointer;
    }
</style>