<section>
	<h1>Manage Documentation</h1>

	<p style="clear: both">
		<span class="spinner float-left cloak mt-1" style="padding-left: 1em"></span>
		<button id="reindex-btn" onclick="initReindex()">Re-Index</button>
        <button onclick="openModal('convert-modal')">Convert Code To HTML Entities</button>
        <?= anchor('documentation-documentation_search_boss/reindex_refs', 'Reindex Refs', array('class' => 'button')) ?>
	</p>

    <?php
    if (isset($docs_strings)) {
        echo '<h4 class="mt-3">Docs Strings:</h4>';
        echo '<ul class="mt-0">';
        foreach($docs_strings as $docs_string) {
            echo '<li>';
            echo anchor('documentation/manage/'.$docs_string, $docs_string);
            echo '</li>';
        }
        echo '</ul>';
    }
    ?>


</section>

<div class="modal" id="convert-modal" style="display: none">
    <div class="modal-body">
        <p>Convert Code Blocks To HTML Entities</p>
        <form mx-post="documentation/submit_duff_code" 
              mx-target="#code_input" 
              mx-indicator=".modal-body .spinner"
              mx-swap="value" method="post">
            <textarea name="code_input" id="code_input"></textarea>
            <button>Submit</button>
            <button class="alt" onclick="clearAndClose()">Cancel</button>
        </form>
    </div>
</div>


<style>
#convert-modal #code_input {
    min-height: 500px;
}
</style>


<script>
let chapterNumber = 1;
let docsStr = '<?= segment(3) ?>';

let submitBtn = document.querySelector('#reindex-btn');
let spinnerEl = document.querySelector('.spinner');

function initReindex() {

    spinnerEl.classList.remove('cloak');
    submitBtn.classList.add('cloak');

    const targetUrl = '<?= BASE_URL ?>documentation-manage/submit_reindex';

    const params = {
        docsStr,
        chapterNumber
    }

    const http = new XMLHttpRequest();
    http.open('post', targetUrl);
    http.setRequestHeader('Content-type', 'application/json');

    console.log('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
    console.log('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
    console.log('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
    console.log('posting to:  ' + targetUrl);
    console.log(JSON.stringify(params));
    console.log('***************');

    http.send(JSON.stringify(params));
    http.onload = function() {
        console.log(http.status);
        console.log(http.responseText);

        spinnerEl.classList.add('cloak');
        submitBtn.classList.remove('cloak');

       if (http.status === 200) {
           const pageHeadlineEl = document.querySelector('h1');
           pageHeadlineEl.innerText = 'Updated Tables!';
       }

    }

}

function fetchTableSummary() {
    const targetUrl = '<?= BASE_URL ?>documentation-manage/table_summary';
    const http = new XMLHttpRequest();
    http.open('get', targetUrl);
    http.setRequestHeader('Content-type', 'application/json');
    http.send();
    http.onload = function() {
        if (http.status !== 200) {
            console.error('Failed to fetch data');
            return;
        }

        const data = JSON.parse(http.responseText);
        const mainSection = document.querySelector('.center-stage section');
        mainSection.appendChild(document.createElement('hr'));

        const table = document.createElement('table');
        table.classList.add('table-summary');
        
        const thead = document.createElement('thead');
        thead.innerHTML = `
            <tr>
                <th>Page Number</th>
                <th>Docs String</th>
                <th>Chapter Number</th>
                <th>Headline</th>
                <th>Character Count</th>
            </tr>
        `;
        table.appendChild(thead);

        const tbody = document.createElement('tbody');
        data.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${item.page_number}</td>
                <td>${item.docs_string}</td>
                <td>${item.chapter_number}</td>
                <td>${item.headline}</td>
                <td>${item.page_content}</td>
            `;
            tbody.appendChild(tr);
        });

        const tableSummaryPara = document.createElement('p');
        tableSummaryPara.innerText = 'The table now has ' + data.length + ' pages.';
        mainSection.appendChild(tableSummaryPara);
        
        table.appendChild(tbody);
        mainSection.appendChild(table);
    }
}


function attemptResubmit() {
    const formEl = document.querySelector('#target-chapter-number');
    const currentChapterNumber = parseInt(formEl.value, 10); // Convert to integer
    const nextChapterNumber = currentChapterNumber + 1;

    if (nextChapterNumber === 0) {
    	alert("finished");
    } else {
    	 console.log(nextChapterNumber);
    	 formEl.value = nextChapterNumber;
    	 const submitBtn = document.querySelector('button');
    	 submitBtn.click();
    }

}

function tryAgain() {

    const centerStage = document.querySelector('.center-stage section');
    while(centerStage.firstChild) {
        centerStage.removeChild(centerStage.firstChild);
    }

    const headlineEl = document.createElement('h1');
    headlineEl.innerText = 'Manage Documentation';
    centerStage.appendChild(headlineEl);

    const newPara = document.createElement('p');
    newPara.style = 'clear:both';
    centerStage.appendChild(newPara);

    const newSpan = document.createElement('span');
    newSpan.setAttribute('class', 'spinner float-left cloak mt-1');
    newSpan.setAttribute('style', 'padding-left: 1em');
    centerStage.appendChild(newSpan);

    const btn = document.createElement('button');
    btn.setAttribute('type', 'button');
    btn.setAttribute('onclick', 'initReindex()');
    btn.innerText = 'Re-Index';
    btn.setAttribute('id', 'reindex-btn');
    // btn.addEventListener('click', (ev) => {
    //     console.log('click');
    //     initReindex();
    // });

    newPara.appendChild(btn);

    chapterNumber = 1;
    docsStr = '';
    submitBtn = document.querySelector('#reindex-btn');
    spinnerEl = document.querySelector('.spinner');

setTimeout(() => {
    submitBtn.click();
}, 100);

}


function openModalNew(modalId) {
    var pageOverlay = document.getElementById("overlay");
    if(typeof(pageOverlay) == 'undefined' || pageOverlay == null) {

        var modalContainer = document.createElement("div");
        modalContainer.setAttribute("id", "modal-container");
        modalContainer.setAttribute("style", "z-index: 3;");
        body.prepend(modalContainer);
        var overlay = document.createElement("div");
        overlay.setAttribute("id", "overlay");
        overlay.setAttribute("style", "z-index: 2");
        body.prepend(overlay);
        var targetModal = document.getElementById(modalId);
        targetModalContent = targetModal.innerHTML;
        targetModal.remove();
        //create a new model
        var newModal = document.createElement("div");
        newModal.setAttribute("class", "modal");
        newModal.setAttribute("id", modalId);
        newModal.style.zIndex = 4;
        newModal.innerHTML = targetModalContent;
        modalContainer.appendChild(newModal);
        setTimeout(() => {
            newModal.style.opacity = 1;
            newModal.style.marginTop = '12vh';
        }, 0);

    }   
}

function openModal(modalId) {
    console.log('running open modal');
    console.log(modalId);
    var modalContainer = document.createElement("div");
    modalContainer.setAttribute("id", "modal-container");
    modalContainer.setAttribute("style", "z-index: 3;");
    body.prepend(modalContainer);

    var overlay = document.createElement("div");
    overlay.setAttribute("id", "overlay");
    overlay.setAttribute("style", "z-index: 2");
    
    body.prepend(overlay);

    var targetModal = _(modalId);
    targetModal.removeAttribute('style');

setTimeout(() => {
    console.log('Boom!');

    targetModalContent = targetModal.innerHTML;
    targetModal.remove();

    //create a new model
    var newModal = document.createElement("div");
    newModal.setAttribute("class", "modal");
    newModal.setAttribute("id", modalId);

    newModal.style.zIndex = 4;
    newModal.innerHTML = targetModalContent;
    modalContainer.appendChild(newModal);

    setTimeout(() => {
        newModal.style.opacity = 1;
        newModal.style.marginTop = '12vh';
    }, 1);   

}, 1);

 
}

function clearAndClose() {
    const codeInput = document.querySelector('#code_input');
    codeInput.value = '';
    closeModal();
}

</script>