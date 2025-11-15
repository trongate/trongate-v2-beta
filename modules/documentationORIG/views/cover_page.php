<div class="container mt-1">
	<h1 class="text-center"><?= $chapters[0]->book_title ?></h1>
	<div class="text-center">Official Documentation</div>
	<div class="cover mt-1" onclick="jumpToUrl('<?= $table_of_contents_url ?>')"><?= $cover ?></div>	
	<div class="text-center mt-2"><?= anchor($table_of_contents_url, 'View Table of Contents', array('class' => 'button')) ?></div>
</div>

<style>
body {
    background-color: #f7f5f3;
    color: #222;
    font-family: "Raleway", "HelveticaNeue", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
    
.cover {
	width: 90%;
	max-width: 420px;
	margin: 0 auto;
	cursor: pointer;
}
</style>


<script>
function jumpToUrl(url) {
    window.location.href = url;
}
</script>
