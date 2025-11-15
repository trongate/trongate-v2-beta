<!DOCTYPE html>
<html lang="en">
<head>
	<base href="<?= BASE_URL ?>">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="css/trongate.css">
	<link rel="stylesheet" href="css/tronpro-docs.css">
	<title>Document</title>
</head>
<body id="feature-ref-modal">
	<div class="feature-ref-top">
		<div>Feature Reference</div>
		<div class="close-btn" onclick="window.parent.close();">&times;</div>
	</div>
	<div id="feature-ref-information"><?= $feature_ref_info ?>
		<div class="text-center" style="margin: 0 auto; max-width: 90%; display: flex; align-items: center; justify-content: center; margin-bottom: 12em;">
			<button class="alt" onclick="window.parent.close();">Close Window</button>
		</div>
	</div>

<script>
window.addEventListener('load', (ev) => {
    // Your code here
const featureRefInformation = document.querySelector('#feature-ref-information');
const height = featureRefInformation.getBoundingClientRect().height;
console.log('height is ' + height);
});

</script>
</body>
</html>