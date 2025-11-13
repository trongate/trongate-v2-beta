<div class="container">
    <div class="documentation-page">
        <div class="page-nav-btns">
            <div>
                <?php if ($prev_page): ?>
                    <?php 
                        $prev_url = BASE_URL . "documentation/information/{$book_url}/{$prev_chapter->chapter_url_string}/{$prev_page->page_url_string}";
                    ?>
                    <a href="<?= $prev_url ?>" class="button">
                        <i class="fa fa-arrow-circle-left"></i><span> Prev</span>
                    </a>
                <?php endif; ?>
            </div>
            <div class="toc-btn-container">
                <button id="home-menu-btn" onclick="goToUrl('<?= $table_of_contents_url ?>')">
                    <i class="fa fa-home"></i> Table of Contents
                </button>
            </div>
            <div>
                <?php if ($next_page): ?>
                    <?php 
                        $next_url = BASE_URL . "documentation/information/{$book_url}/{$next_chapter->chapter_url_string}/{$next_page->page_url_string}";
                    ?>
                    <a href="<?= $next_url ?>" class="button">
                        <span>Next </span><i class="fa fa-arrow-circle-right"></i>
                    </a>
                <?php elseif (!empty($chapter->pages)): ?>
                    <?php 
                        $first_page = $chapter->pages[0];
                        $next_url = BASE_URL . "documentation/information/{$book_url}/{$chapter->chapter_url_string}/{$first_page->page_url_string}";
                    ?>
                    <a href="<?= $next_url ?>" class="button">
                        <span>Next </span><i class="fa fa-arrow-circle-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="chapter-num">Chapter <?= $chapter->chapter_number ?></div>
        <h1 id="ridiculously-huge"><?= out($chapter->chapter_title) ?></h1>

        <?php if (!empty($chapter->pages)): ?>
            <div class="chapter-pages-list">
                <h3>In This Chapter:</h3>
                <ul>
                    <?php foreach ($chapter->pages as $page): ?>
                        <?php 
                            $page_url = BASE_URL . "documentation/information/{$book_url}/{$chapter->chapter_url_string}/{$page->page_url_string}";
                        ?>
                        <li>
                            <a href="<?= $page_url ?>"><?= out($page->headline) ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    body {
        background-color: #dce4ec;
        color: #222;
        font-family: "Raleway", "HelveticaNeue", "Helvetica Neue", Helvetica, Arial, sans-serif;
    }
    
    .documentation-page {
        background-color: #fff;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        margin-bottom: 2rem;
    }
    
    .page-nav-btns {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e0e0e0;
    }
    
    .button {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background-color: #0066cc;
        color: #fff;
        text-decoration: none;
        border-radius: 4px;
        transition: background-color 0.2s;
    }
    
    .button:hover {
        background-color: #0052a3;
    }
    
    #home-menu-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background-color: #28a745;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    
    #home-menu-btn:hover {
        background-color: #218838;
    }
    
    .chapter-num {
        font-size: 1.2rem;
        color: #666;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }
    
    #ridiculously-huge {
        font-size: 3rem;
        margin-top: 0;
        margin-bottom: 2rem;
        color: #333;
    }
    
    .chapter-pages-list {
        margin-top: 2rem;
    }
    
    .chapter-pages-list h3 {
        margin-bottom: 1rem;
        color: #444;
    }
    
    .chapter-pages-list ul {
        list-style: none;
        padding: 0;
    }
    
    .chapter-pages-list li {
        margin-bottom: 0.75rem;
        padding-left: 1.5rem;
        position: relative;
    }
    
    .chapter-pages-list li:before {
        content: "→";
        position: absolute;
        left: 0;
        color: #0066cc;
        font-weight: bold;
    }
    
    .chapter-pages-list a {
        color: #0066cc;
        text-decoration: none;
        font-size: 1.1rem;
    }
    
    .chapter-pages-list a:hover {
        text-decoration: underline;
    }
</style>

<script>
function goToUrl(url) {
    window.location.href = '<?= BASE_URL ?>' + url;
}
</script>