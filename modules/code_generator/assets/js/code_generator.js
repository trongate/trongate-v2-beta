function renderLoader() {
    const main = document.querySelector('main');
    Array.from(main.children).forEach(childEl => {
        if (childEl.classList.contains('loader')) {
            childEl.classList.remove('cloak');
        } else {
            childEl.classList.add('cloak');
        }
    });

	setTimeout(() => {
		hideLoader();
	}, 1000);

}

function hideLoader() {
    const main = document.querySelector('main');
    Array.from(main.children).forEach(childEl => {
        if (childEl.classList.contains('loader')) {
            childEl.classList.add('cloak');
        } else {
            childEl.classList.remove('cloak');
        }
    });
}

function revealOptions() {
    const codegenSelectContainer = document.querySelector('#codegen-select-container');
    codegenSelectContainer.remove();
    const optionsList = document.querySelector('.options-list');
    optionsList.removeAttribute('style');
}

function formOnInput() {
    const valueInputEl = document.querySelector('.value-input');
    valueInputEl.focus();
}

function openPropertiesBuilder() {
    parent.openPropertiesBuilder(1200, 300);
}

window.addEventListener('load', (ev) => {
	const loaderEl = document.querySelector('.loader');
	loaderEl.removeAttribute('style');
});