<!DOCTYPE html>
<html lang="en">
<head>
    <base href="<?= BASE_URL ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/themes/prism-atom-dark.css">
    <link rel="stylesheet" href="css/prism.css">
    <link href='//fonts.googleapis.com/css?family=Raleway:400,300,600' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="css/trongate.css">
    <link rel="stylesheet" href="documentation_module/css/docs_ahoy.css">
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

<style>
body {
	background-color: #fff;
	overflow: hidden;
}

.feature-ref-top {
	display: flex;
	flex-direction: row;
	background-color: var(--primary);
	padding: .6em 1em;
	color: #eee;
	display: flex;
	flex-direction: row;
	align-items: center;
	justify-content: space-between;
	position: fixed;
	width: 100%;
}

#feature-ref-information {
	top: 2.77em;
	position: relative;
	background-color: #fff;
	overflow: auto;
	max-height: 900px;
}

.feature-ref-top > div:nth-child(1) {
	font-family: "Inter", sans-serif;
	font-weight: bold;
	font-size: 1.2em;
}

.feature-ref-top .close-btn {
	font-size: 1.33em;
	font-weight: bold;
	cursor: pointer;
	margin-right: 1.4em;
	top: -4px;
	position: relative;
}

tr, td {
	background-color: #fcfbff;
	line-height: 2em;
}

main {
	background-color: #f4f4fc;
	color: var(--dark-blue);
}

h1 {
	text-transform: none;
	font-family: 'Raleway', sans-serif;
	font-size: 44px;
	font-weight: bold;
	text-align: left;
	margin-top: 0;
	margin-bottom: 0;
}

h2 {
	margin-top: 2em;
	text-transform: none;
	font-family: 'Raleway', sans-serif;
	font-size: 27px;
	font-weight: normal;
	margin-bottom: 0;
}

.docs-container {
	width: 90vw;
	margin-left: auto;
	margin-right: auto;
}

.docs-content {
	margin-top: 1em;
}

.docs-lhs > div:nth-child(1) > div:nth-child(1) {
	padding-top: 1em;
	font-size: 27px;
	margin-bottom: 3px;
	font-weight: normal;
	font-family: 'Raleway', sans-serif;
}

.docs-lhs > div > div:nth-child(2) {
	text-transform: uppercase;
	margin-top: 12px;
	font-weight: bold;
}

#docs-lhs-nav {
	margin-top: 33px;
}

/* Create the div wrapper around tables */
.table-wrapper {
  display: block;
  max-width: 100%;
  overflow-x: auto;
  margin-bottom: 1em; /* Optional, adds spacing below each table */
}

.docs-rhs form.search-form {
	max-width: 400px;
	display: flex;
	flex-direction: row;
	font-size: .9em;
	margin: 0 auto;
}

.docs-rhs form.search-form button {
	margin-top: 0;
	border-top-left-radius: 0;
	border-bottom-left-radius: 0;
	max-width: 5em;
}

.docs-rhs #searchphrase {
	border-top-right-radius: 0;
	border-bottom-right-radius: 0;	
}

.page-nav-btns,
#docs-page-preview-modal .modal-heading {
	display: flex;
	flex-direction: row;
	align-items: center;
	justify-content: space-between;
}

.modal-body .search-form-container {
	display: none;
}

.readable-docs {
	max-width: 840px;
	margin: 0 auto;
}

details {
    line-height: 1.8em;
    cursor: pointer;
}

details ul {
	list-style-type: none;
	margin: .6em 0 1em 0;
}

details ul > li {
	font-size: .9em;
}

details ul > li a {
	text-decoration: none;
	color: var(--dark-blue);
}

details ul > li a:hover {
	color: var(--dark-blue);
	text-decoration: underline;
}

.card {
	background-color: #fff;
}

.code-sample {
	width: 90%;
	margin: 0 auto 1em auto;
	overflow: auto; 
}

.code-block-header {
	margin-top: 1em;
	display: flex;
	flex-direction: row;
	align-items: center;
	justify-content: space-between;
	padding: .6em 1em 0 1em;
}

.code-block-header > div:nth-child(1) {
	 font-family: 'Courier New', Courier, monospace;
}

.code-block-header > div:nth-child(2) {
	cursor: pointer;
	font-size: 1.1em;
}

code {
  padding: .2rem .5rem;
  margin: 0 .2rem;
  font-size: 90%;
  white-space: nowrap;
  background: #F1F1F1;
  border: 1px solid #E1E1E1;
  border-radius: 4px; 
}

pre code {
  border: none;
  padding: 0;
  margin: 0;
}

.page-nav-btns button, .page-nav-btns .button {
	transition: .3s;
  	text-transform: uppercase;
}

.alert-body ul {
	margin-top: .6em;
}

.alert-body {
	line-height: 1.5em;
}

.alert-body ul > li {
	margin-bottom: 1em;
}

.alert-body > p:nth-child(1) {
	margin-top: 0;
}

.modal-body ul > li,
.docs-rhs ul > li {
    margin-bottom: .6em;
}

#docs-page-preview-modal .modal-body {
	max-height: 85vh;
	overflow-y: auto;
}

#docs-page-preview-modal .modal-body > div {
	left: 12px;
	position: relative;
}

#docs-page-preview-modal p {
	text-align: left;
}

#docs-page-preview-modal .modal-heading .fa-times,
.documentation-identifier {
	cursor: pointer;
}

.code-demo {
	background-color: #eee;
	border-radius: 6px;
	border: 3px var(--primary) dashed;
	width: max-content;
	margin: 0 auto;
	min-width: 220px;
	padding-top: 1em;
	padding-bottom: 1em;
}

.breadcrumbs {
    padding: 10px 15px;
    background: #f8f9fa;
    border-radius: 5px;
    margin-top: 20px;
    font-family: Arial, sans-serif;
    display: flex;
    align-items: center;
}

.breadcrumbs a {
    color: #0275d8;
    text-decoration: none;
    padding-bottom: 0.05em;
}

.breadcrumbs a:hover {
    border-bottom: 1px solid #0275d8;
}

.breadcrumbs .separator {
    margin: 0 5px;
    color: #6c757d;
}

.breadcrumbs a:focus {
    outline: 2px solid #0275d8;
    outline-offset: 2px;
}

.breadcrumbs > span {
    margin: 0 7px;
}

.chapter-num {
	margin-top: 120px;
	font-weight: bold;
	text-align: center;
	font-size: 1.33em;
}

#ridiculously-huge {
	margin-top: 12px;
	text-align: center;
	font-size: 4em;
	margin-bottom: 45vh;
}

.docs-rhs .cover-container,
.toc-btn-para {
	width: 90%;
	max-width: 450px;
	margin: 0 auto;
}

.cover-container svg {
	margin-top: 1em;
	margin-bottom: 0;
	width: 50vh;
	max-width: 94%;
	cursor: pointer;
}

.docs-rhs .cover-container {
	margin: 3em auto 1em auto;
}

.toc-grid {
	margin-top: 33px;
	display: grid;
	grid-template-columns: repeat(1fr);
	grid-gap: 1em;
}
.toc-grid h4 {
	margin-bottom: 0;
	padding-bottom: 0;
}
.toc-grid h2 {
	margin-top: 0;
	padding-top: 0;
}
.toc-grid h3 {
 	color: #111;
}
.toc-grid ul {
	list-style-type: none;
	font-size: 0.9em;
	font-weight: bold;
}
.toc-grid li {
	display: flex;
	flex-direction: row;
	align-items: center;
	justify-content: space-between;
	border-bottom: 1px #111 dotted;
	margin-bottom: 12px;
}
.toc-grid li a {
	color: var(--primary);
	border: none;
	text-decoration: none;
}
.toc-grid li a:hover {
	color: var(--primary-darker);
}
.toc-grid .card-heading {
	background-color: var(--primary-darker);
}
.page-num {
	font-weight: bold;
	font-size: 0.8em;
}

#scrollToTopBtn {
    position: fixed;
    bottom: 30vh;
    right: 3px;
    padding: 10px 20px;
    font-size: 24px;
    border: none;
    cursor: pointer;
    border-radius: 5px;
    display: none; /* Start with button hidden */
    z-index: 1000; /* Make sure the button is above other content */
    max-width: 3em;
    background-color: transparent;
    border: 1px var(--primary) solid;
    color: var(--primary);
}

#scrollToTopBtn:hover {
    background-color: #ffffffaa;
}

.side-by-side {
   display: grid;
   gap: 1em;
}

.trongate-css-demo {
	max-width: 760px;
	margin: 33px auto;
	background-color: #fff;
	padding: .6em 0 1.2em 0;
	border-radius: 6px;
	-webkit-box-shadow: -2px 6px 16px -2px rgba(0,0,0,0.75);
	-moz-box-shadow: -2px 6px 16px -2px rgba(0,0,0,0.75);
	box-shadow: -2px 6px 16px -2px rgba(0,0,0,0.75);

    /* Override variables back to trongate.css values */
    --primary: #4682b4;
    --primary-dark: #38678f;
    --primary-darker: #294d6b;
    --primary-color: #fff;
    --secondary: #af46b4;
    --border: #c5c5c5;
    --alt: #fff;
    --success: #4bb446;
    --success-dark: #368532;
    --info: #b2cce1;
    --warning: #b47846;
    --danger: #b4464b;
    --danger-dark: #8f383b;
    --inverse: #333;
	--secondary-dark: #943f99;
	--secondary-darker: #77337a;
	--neutral: #f5f5f5;
}

.gold {
    background: linear-gradient(to right, #BF953F, #FCF6BA, #B38728, #FBF5B7, #AA771C);
    color: #fff;
    border: 1px #ded4c2 solid;
}

.silver {
    background: linear-gradient(to right, #8faab7, #f1f8ff, #9eb2c1, #f0f6f9, #96a2a9);
    color: #fff;
    border: 1px #c0cbd1 solid;
}

.trongate-css-demo > div {
	width: 88%;
	margin: 1em auto;
}

.trongate-css-demo h1,
.h1-default {
    color: rgb(85, 85, 85);
    display: block;
    font-family: Tahoma, Geneva, sans-serif;
    font-size: 64px;
    font-weight: 700;
    height: 77px;
    margin-block-end: 12.8px;
    margin-block-start: 0px;
    margin-bottom: 12.8px;
    margin-inline-end: 0px;
    margin-inline-start: 0px;
    margin-top: 0px;
    text-align: left;
    unicode-bidi: isolate;
}

.trongate-css-demo h2,
.h2-default {
    color: rgb(85, 85, 85);
    display: block;
    font-family: Tahoma, Geneva, sans-serif;
    font-size: 32px;
    font-weight: 700;
    height: 39px;
    margin-block-end: 26.56px;
    margin-block-start: 26.56px;
    margin-inline-end: 0px;
    margin-inline-start: 0px;
    text-align: left;
    unicode-bidi: isolate;
}

.trongate-css-demo h3,
.h3-default {
    color: rgb(85, 85, 85);
    display: block;
    font-family: Tahoma, Geneva, sans-serif;
    font-size: 28.8px;
    font-weight: 700;
    height: 35px;
    margin-block-end: 28.8px;
    margin-block-start: 28.8px;
    margin-inline-end: 0px;
    margin-inline-start: 0px;
    text-align: left;
    unicode-bidi: isolate;
}

.trongate-css-demo h4,
.h4-default {
    color: rgb(85, 85, 85);
    display: block;
    font-family: Tahoma, Geneva, sans-serif;
    font-size: 16px;
    font-weight: 700;
    height: 19px;
    margin-block-end: 21.28px;
    margin-block-start: 21.28px;
    margin-inline-end: 0px;
    margin-inline-start: 0px;
    unicode-bidi: isolate;
}

.trongate-css-demo .default-para {
  color: rgb(85, 85, 85);
  display: block;
  font-family: Tahoma, Geneva, sans-serif;
  font-size: 16px;
  height: 48px;
  line-height: 24px;
  margin-block-end: 16px;
  margin-block-start: 16px;
  margin-inline-end: 0px;
  margin-inline-start: 0px;
  unicode-bidi: isolate;
}

.color-sample {
  border-radius: 6px;
  margin-bottom: .5em;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 1em;
  line-height: 1.33em;
}

.color-sample > div:nth-child(2) {
	display: none;
}

.color-samples {
	display: grid;
	grid-template-columns: repeat(3, 1fr);
	gap: .33em 1em;
}

.color-samples-alt {
	display: grid;
	grid-template-columns: 1fr;
	gap: .33em 1em;	
}

.text-light {
  color: #fff;
}

.text-dark {
  color: #000;
}

#feature-ref-list {
	margin: 0 auto;
}

.fxeature-ref button .fa {
    font-size: 1.4em;
}

.fxeature-ref button.alt {
    color: var(--primary);
    background-color: transparent;
    border: none;
}

#feature-ref-modal {
  font-family: Tahoma, Geneva, sans-serif;
  margin: 0;
  padding: 0;
  color: #555;
  font-size: 1rem;
}

#feature-ref-modal > div > h1 {
	font-family: "Inter", sans-serif;
	text-transform: none;
	text-align: left;
	font-size: 33px;
	color: var(--primary);
	margin-top: 0;
    margin-bottom: 0;
}

#feature-ref-modal p {
	text-align: left;
	font-size: 14px;
}

#feature-ref-modal table {
	font-size: 14px;
}

#feature-ref-modal table th {
	width: max-content;
}

#feature-ref-modal h2 {
	font-family: "Inter", sans-serif;
    margin-top: 1.6em;
    font-size: 21px;
    color: var(--primary);
    font-weight: normal;
}

#feature-ref-modal .signature {
    font-family: 'Courier New', Courier, monospace;
    background-color: #f0f0f0;
    padding: 10px;
    border-left: 5px solid var(--primary);
    text-align: left;
    font-weight: bold;
    color: #555;
}

#feature-ref-modal .param,
#feature-ref-modal .return {
  background-color: #e0e0e0;
  padding: 10px;
  margin: 10px 0;
  border-left: 3px solid #0056b3;
}

#feature-ref-modal .example {
  background-color: #fff;
  padding: 10px;
  border: 1px solid #ccc;
}

#feature-ref-modal pre {
  background-color: #333;
  color: #fff;
  padding: 10px 7px 10px 7px;
  max-width: 100%;
  line-height: 1.4em;
  font-size: 15px;
}

#feature-ref-modal .note {
  font-style: italic;
}

#feature-ref-modal .link a {
  color: #007bff;
  text-decoration: none;
}

.modal-body {
	max-height: 85vh;
	overflow-y: auto;
}

.video-container {
	max-width: 760px;
	margin: 1em auto;
}

figure {
	width: 90%;
	max-width: 620px;
    margin: 2rem auto;
    text-align: center;
    background-color: #f4f7ff;
    display: flex;
    align-items: center;
    flex-direction: column;
    padding: 1em;
	-webkit-box-shadow: 0px 10px 14px 0px rgba(0,0,0,0.75);
	-moz-box-shadow: 0px 10px 14px 0px rgba(0,0,0,0.75);
	box-shadow: 0px 10px 14px 0px rgba(0,0,0,0.75);
}

img {
	margin: 3em auto 5em auto;
	display: block;
}

img,
figure img {
  border:5px #8890a6 solid;
  border-style: double;
  margin: 0;
}

figcaption {
	margin-top: 1.33em;
	display: flex;
	flex-direction: row;
	justify-content: flex-start;
	align-items: center;
	width: 100%;
	font-size: .9em;
	color: #545969;
	text-align: left;
	font-family: Garamond, serif;
}
</style>

<script>
window.addEventListener('load', (ev) => {
	const featureRefInformation = document.querySelector('#feature-ref-information');
	const height = featureRefInformation.getBoundingClientRect().height;
});
</script>
<script src="js/prism.js"></script>
<script src="documentation_module/js/docs-ahoy.js"></script>
</body>
</html>