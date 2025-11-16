    <h1 class="text-center">Table of Contents</h1>
    <?= Modules::run('documentation/_draw_search_btn') ?>
    <div class="mt-1 container">
        <?php if (!empty($chapters)): ?>
            <?php foreach ($chapters as $chapter): ?>
                <div class="card">
                    <div class="card-body">
                        <h3 class="mt-0"><?= out($chapter->chapter_title) ?></h3>
                        <?php if (!empty($chapter->pages)): ?>
                            <ul class="toc-list-items">
                                <?php foreach ($chapter->pages as $index => $page): ?>
                                    <li>
                                        <div>
                                            <a href="<?= $page->page_url ?>">
                                                <?= out($page->headline) ?>
                                            </a>
                                        </div>
                                        <div><?= $page->page_number ?></div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-muted small">No pages in this chapter.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center text-muted">No chapters available.</p>
        <?php endif; ?>
    </div>

<style>
    body {
        background-color: #f7f5f3;
        color: #222;
        font-family: "Raleway", "HelveticaNeue", "Helvetica Neue", Helvetica, Arial, sans-serif;
    }
    .page-container {
        background-color: #fbfbfb;
    }
    .card {
        background-color: #fff;
        margin-bottom: 1.5rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .card-body {
        padding: 1.5rem;
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
        font-size: 1em;
        line-height: 1em;
    }
    .toc-list-items li:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    .toc-list-items a {
        text-decoration: none;
        font-weight: 500;
    }
    .toc-list-items a:hover {
        text-decoration: underline;
    }
    .text-muted {
        color: #666 !important;
    }
    .small {
        font-size: 0.875rem;
    }
</style>