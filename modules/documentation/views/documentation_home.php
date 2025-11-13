<div class="docs-container">
    <h1 class="text-center mt-1">Documentation</h1>
    <div class="docs-books">
        <?php
        foreach ($books as $book) {
            $book_url = BASE_URL.'documentation/'.$book->url_string;
            $book_url = str_replace('-', '_', $book_url);
        ?>
        <div class="card">
            <div class="card-body">
                <div class="cover" onclick="jumpToUrl('<?= $book_url ?>')">
                    <?= $book->cover ?>
                </div>
                <h3 class="text-center"><?= anchor($book_url, out($book->book_title)) ?></h3>
                <p class="text-center"><?= $book->description ?></p>
            </div>
        </div>
        <?php
        }
        ?>
    </div>
</div>
