function fetchStarterContent() {
	console.log('fetchStarterContent is invoked');
	console.log('and the current URL is ' + window.location.href);

	const targetUrl = localBaseUrl + 'code_generator/fetch_starter_content';

	const http = new XMLHttpRequest();
	http.open('get', targetUrl);
	http.setRequestHeader('Content-type', 'application/json');
	http.send();
	http.onload = function() {
		console.log(http.status);
		console.log(http.responseText);
		const body = document.querySelector('body');
		if (http.status === 200) {
			body.style.opacity = 0;
			const styleEl = document.createElement('link');
			styleEl.rel = 'stylesheet';
			styleEl.type = 'text/css';
			styleEl.href = cssPath;
			document.head.appendChild(styleEl);

			const jsEl = document.createElement('script');
			jsEl.src = jsPath;
			body.appendChild(jsEl);

			body.innerHTML = http.responseText;

			setTimeout(() => {
				body.style.opacity = 1;
			}, 300);

			
		}
	}

}

window.addEventListener('load', (ev) => {
    fetchStarterContent();
});