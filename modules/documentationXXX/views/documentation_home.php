<div class="container-xxxl">
    <h1 class="text-center mt-1">Official Documentation</h1>
    <div class="docs-books">
        <?php
        foreach ($docs_books as $book) {
            $book_url = BASE_URL.'documentation/'.$book->url_string;
            $book_url = str_replace('-', '_', $book_url);
        ?>
        <div class="card">
            <div class="card-body">
                <div class="cover" onclick="jumpToUrl('<?= $book_url ?>')"><?= $book->cover ?></div>
                <h3><?= anchor($book_url, out($book->book_title)) ?></h3>
                <p><?= $book->description ?></p>
            </div>
        </div>
        <?php
        }
        ?>
    </div>
</div>

<style>
    body {
        background-color: #f7f5f3;
        color: #222;
        font-family: "Raleway", "HelveticaNeue", "Helvetica Neue", Helvetica, Arial, sans-serif;
    }

    .card {
        background-color: #fff;
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

<script>
function jumpToUrl(url) {
    window.location.href = url;
}
</script>