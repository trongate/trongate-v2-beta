<div class="container">
    <div class="documentation-page">
        
        <div class="page-content">
            <?= $page->page_content ?>
        </div>

        <div class="page-navigation">
            <?php
            // Find previous and next pages
            $prev_page = null;
            $next_page = null;
            $found_current = false;

            foreach ($chapters as $chapter) {
                if (!empty($chapter->pages)) {
                    foreach ($chapter->pages as $p) {
                        if ($found_current && $next_page === null) {
                            $next_page = $p;
                            $next_chapter = $chapter;
                            break 2;
                        }
                        
                        if ($p->page_url_string === $page->page_url_string) {
                            $found_current = true;
                        } else if (!$found_current) {
                            $prev_page = $p;
                            $prev_chapter = $chapter;
                        }
                    }
                }
            }
            ?>

            <div class="nav-buttons">
                <?php if ($prev_page): ?>
                    <?php 
                        $prev_url = BASE_URL . "documentation/information/{$page->book_url_string}/{$prev_chapter->chapter_url_string}/{$prev_page->page_url_string}";
                    ?>
                    <a href="<?= $prev_url ?>" class="btn-prev">
                        <span class="arrow">←</span>
                        <span class="text">
                            <small>Previous</small><br>
                            <?= out($prev_page->headline) ?>
                        </span>
                    </a>
                <?php else: ?>
                    <div></div>
                <?php endif; ?>

                <?php if ($next_page): ?>
                    <?php 
                        $next_url = BASE_URL . "documentation/information/{$page->book_url_string}/{$next_chapter->chapter_url_string}/{$next_page->page_url_string}";
                    ?>
                    <a href="<?= $next_url ?>" class="btn-next">
                        <span class="text">
                            <small>Next</small><br>
                            <?= out($next_page->headline) ?>
                        </span>
                        <span class="arrow">→</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="back-to-toc">
            <a href="<?= $table_of_contents_url ?>">← Back to Table of Contents</a>
        </div>
    </div>
</div>