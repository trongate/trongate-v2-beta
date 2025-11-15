function initAskQuestion() {
    if (activeRequestCode !== '') {
        const targetUrl = baseUrl + 'module_request_responses/ask_question/' + activeRequestCode;
        window.location.href = targetUrl;
    }
}

function initMakeOffer() {
    if (activeRequestCode !== '') {
        const targetUrl = baseUrl + 'module_request_responses/make_offer/' + activeRequestCode;
        window.location.href = targetUrl;
    }
}

function loadRecord(counter) {
  currentIndex = counter-1;
  const targetRow = rows[currentIndex];
  activeRequestCode = targetRow.code;
  createdBy = parseInt(targetRow.created_by);
  isNew = (targetRow.new == true) ? 1 : 0;

  const recordHeadline = document.querySelector('#job-details > h2:nth-child(2)');
  const subHeadline = document.querySelector('#job-details > p.bigger');
  const askAmount = document.querySelector('#ask-amount');
  const fullDescription = document.querySelector('#job-details > p:nth-child(9)');

  recordHeadline.innerHTML = targetRow.request_title;
  subHeadline.innerHTML = targetRow.created_by_username;
  askAmount.innerHTML = targetRow.budget;
  fullDescription.innerHTML = targetRow.request_details;
  initDrawActionBtns();
  attemptDrawIsNew();
  fetchResponseFeed();
}

function initDrawActionBtns() {
  if(myTrongateUserId>0) {
    const actionBtns = document.getElementById('action-btns');
    if ((myTrongateUserId>0) && (myTrongateUserId !== createdBy)) {
      actionBtns.style.display = 'block';
    } else {
      actionBtns.style.display = 'none';
    }
  }
}

function attemptDrawIsNew() {
  const isNewDiv = document.querySelector('#job-details > div.newjob');

  if(isNew == 1) {
    isNewDiv.style.display = 'block';
  } else {
    isNewDiv.style.display = 'none';
  }
}

function fetchResponseFeed() {
  var responsesGrids = document.getElementsByClassName('responses-grid');
  for (var i = 0; i < responsesGrids.length; i++) {
    responsesGrids[i].remove();
  }

  const specialInfoDiv = document.querySelector('#special-info');
  specialInfoDiv.style.display = 'none';
  specialInfoDiv.innerHTML = '';

  const spinnerDiv = document.querySelector('#record-responses > div');
  spinnerDiv.style.display = 'flex';

  const targetUrl = baseUrl + 'module_request_responses/fetch_feed_data/' + activeRequestCode
  const http = new XMLHttpRequest();
  http.open('get', targetUrl);
  http.setRequestHeader('Content-type', 'application/json');
  http.send();
  http.onload = () => {
    drawResponseFeed(http.responseText);
  }
}

function fetchResponseFeedAlt() {
  var responsesGrids = document.getElementsByClassName('responses-grid');
  for (var i = 0; i < responsesGrids.length; i++) {
    responsesGrids[i].remove();
  }

  const spinnerDiv = document.querySelector('#record-responses > div');
  spinnerDiv.style.display = 'flex';

  const targetUrl = baseUrl + 'module_request_responses/fetch_feed_data/' + activeRequestCode
  const http = new XMLHttpRequest();
  http.open('get', targetUrl);
  http.setRequestHeader('Content-type', 'application/json');
  http.send();
  http.onload = () => {
    drawResponseFeedAlt(http.responseText);
  }
}

function drawResponseFeed(responseText) {
  let numOffers = 0;
  const responseRows = JSON.parse(responseText);
  const spinnerDiv = document.querySelector('#record-responses > div');
  spinnerDiv.style.display = 'none';
  if (responseRows.length == 0) {
    drawEmptyResponseFeed();
  } else {
    const parentContainer = document.getElementById('record-responses');
    const responsesGrid = document.createElement('div');
    responsesGrid.setAttribute('class', 'responses-grid');
    parentContainer.appendChild(responsesGrid);

    for (var i = 0; i < responseRows.length; i++) {
      const responseRow = document.createElement('div');
      responseRow.classList.add('response-row');
      responsesGrid.appendChild(responseRow);

      const rowUpperDiv = document.createElement('div');
      rowUpperDiv.setAttribute('class', 'row-upper');
      responseRow.appendChild(rowUpperDiv);

      console.log(responseRows[i]);

      if (responseRows[i].response_type == 'Offer') {
        numOffers++;
      } else if (responseRows[i].response_type == 'Answer') {
        responseRow.classList.add('hidden-answer');
        responseRow.setAttribute('id', 'answer-for-' + responseRows[i].answer_for);
      }

      const div1 = document.createElement('div');
      div1.innerHTML = responseRows[i].response_type;
      div1.setAttribute('class', responseRows[i].class);
      rowUpperDiv.appendChild(div1);


      let gotExtraLinks = false;

      if ((myTrongateUserId>0) && (responseRows[i].trongate_user_id !== myTrongateUserId)) {
        //potentially draw 'compose answer' or 'accept quote'

        if (responseRows[i].response_type == 'Question') {
          //build a 'compose answer' button
          let composeAnswerLink = document.createElement('a');
          let composeAnswerUrl = baseUrl + 'module_request_responses/compose_answer/';
          composeAnswerUrl+= activeRequestCode + '/' + responseRows[i].response_code;
          composeAnswerLink.setAttribute('href', composeAnswerUrl);
          composeAnswerLink.innerHTML = 'Compose Answer <i class=\'fa fa-pencil\'></i>';
          composeAnswerLink.setAttribute('class', 'button alt');

          if (gotExtraLinks == false) {
            const linkBtnsDiv = document.createElement('div');
            linkBtnsDiv.setAttribute('class', 'text-right');
            rowUpperDiv.appendChild(linkBtnsDiv);
            linkBtnsDiv.appendChild(composeAnswerLink);
            gotExtraLinks = true; 
          }

          responseRow.setAttribute('id', 'question-' + responseRows[i].response_code);

        }

        if (responseRows[i].response_type == 'Offer') {
          //build a 'compose answer' button
          const acceptBtn = document.createElement('button');
          let acceptOfferUrl = baseUrl + 'module_request_responses/accept_offer/';
          acceptOfferUrl+= activeRequestCode + '/' + responseRows[i].response_code;
          acceptBtn.innerHTML = 'Accept Offer <i class=\'fa fa-star\'></i>';
          acceptBtn.setAttribute('class', 'button green-btn');
          acceptBtn.setAttribute('onclick', 'initAcceptOffer(\'' + responseRows[i].response_code + '\')');
          
          if (gotExtraLinks == false) {
            const linkBtnsDiv = document.createElement('div');
            linkBtnsDiv.setAttribute('class', 'text-right');
            rowUpperDiv.appendChild(linkBtnsDiv);
            linkBtnsDiv.appendChild(acceptBtn);
            gotExtraLinks = true; 
          }

        }
      }

      if ((responseRows[i].response_type == 'Offer') || (responseRows[i].response_type == 'Winning Offer')) {
        const offerValueDiv = document.createElement('div');
        offerValueDiv.setAttribute('class', 'offer-value');
        offerValueDiv.innerHTML = responseRows[i].offer_value;
        offerValueDiv.innerHTML+= '<span class="offer-currency"> ' + responseRows[i].offer_currency + '</span>';
        responseRow.appendChild(offerValueDiv);

        if (responseRows[i].date_accepted>0) {
          const acceptedDiv = document.createElement('div');
          acceptedDiv.setAttribute('class', 'accepted-info');
          acceptedDiv.innerHTML = 'Offer accepted on ' + responseRows[i].date_accepted_desc;
          responseRow.appendChild(acceptedDiv);
        }
      }

      const div2 = document.createElement('div');
      div2.setAttribute('class', 'posted-by');
      div2.innerHTML = 'Posted by ' + responseRows[i].username + ' on ' + responseRows[i].date_created;
      responseRow.appendChild(div2);

      const div3 = document.createElement('div');
      div3.innerHTML = responseRows[i].comment;
      responseRow.appendChild(div3);

      if (numOffers == 0) {
        const specialInfoDiv = document.querySelector('#special-info');
        specialInfoDiv.style.display = 'block';
        specialInfoDiv.innerHTML = '<i class="fa fa-star"></i> Be the first to make an offer!';        
      }
    }
  }

  repositionAnswers();
  addEditBtns();
}

function addEditBtns() {

  if (createdBy == myTrongateUserId) {
    const targetEl = document.querySelector('#job-details > h2:nth-child(2)');
    const targetElInner = targetEl.innerHTML;
    console.log(targetElInner);
    const editUrl = baseUrl + 'module_requests/create/' + activeRequestCode;
    let editBtnCode = ' <span class="sm" style="font-weight:normal"><a href="';
    editBtnCode+= editUrl + '" class="button alt sm">edit <i class="fa fa-pencil"></i></a></span>';
    const targetElInnerNew = targetElInner + editBtnCode;
    targetEl.innerHTML = targetElInnerNew;
  }

}

function drawResponseFeedAlt(responseText) {
  //simplified version of above function
  let numOffers = 0;
  const responseRows = JSON.parse(responseText);
  const spinnerDiv = document.querySelector('#record-responses > div');
  spinnerDiv.style.display = 'none';
  if (responseRows.length == 0) {
    drawEmptyResponseFeed();
  } else {
    const parentContainer = document.getElementById('record-responses');
    const responsesGrid = document.createElement('div');
    responsesGrid.setAttribute('class', 'responses-grid');
    parentContainer.appendChild(responsesGrid);

    for (var i = 0; i < responseRows.length; i++) {
      const responseRow = document.createElement('div');
      responseRow.classList.add('response-row');
      responsesGrid.appendChild(responseRow);

      const rowUpperDiv = document.createElement('div');
      rowUpperDiv.setAttribute('class', 'row-upper');
      responseRow.appendChild(rowUpperDiv);

      if (responseRows[i].response_type == 'Offer') {
        numOffers++;
      } else if (responseRows[i].response_type == 'Answer') {
        responseRow.classList.add('hidden-answer');
        responseRow.setAttribute('id', 'answer-for-' + responseRows[i].answer_for);
      }

      const div1 = document.createElement('div');
      div1.innerHTML = responseRows[i].response_type;
      div1.setAttribute('class', responseRows[i].class);
      rowUpperDiv.appendChild(div1);

      if (responseRows[i].response_type == 'Question') {
        responseRow.setAttribute('id', 'question-' + responseRows[i].response_code);
      }

      if ((responseRows[i].response_type == 'Offer') || (responseRows[i].response_type == 'Winning Offer')) {
        const offerValueDiv = document.createElement('div');
        offerValueDiv.setAttribute('class', 'offer-value');
        offerValueDiv.innerHTML = responseRows[i].offer_value;
        offerValueDiv.innerHTML+= '<span class="offer-currency"> ' + responseRows[i].offer_currency + '</span>';
        responseRow.appendChild(offerValueDiv);
      }

      const div2 = document.createElement('div');
      div2.setAttribute('class', 'posted-by');
      div2.innerHTML = 'Posted by ' + responseRows[i].username + ' on ' + responseRows[i].date_created;
      responseRow.appendChild(div2);

      const div3 = document.createElement('div');
      div3.innerHTML = responseRows[i].comment;
      responseRow.appendChild(div3);

      if (numOffers == 0) {
        const specialInfoDiv = document.querySelector('#special-info');
        specialInfoDiv.style.display = 'block';
        specialInfoDiv.innerHTML = '<i class="fa fa-star"></i> Be the first to make an offer!';        
      }
    }
  }

  repositionAnswers();
}

function initAcceptOffer(responseCode) {
  openModal('accept-modal');
  const activeRequestCodeField = document.getElementById('active_request_code');
  activeRequestCodeField.value = activeRequestCode;

  const hiddenFormField = document.getElementById('accepted_response_code');
  hiddenFormField.value = responseCode;
}

function repositionAnswers() {
  const answers = document.getElementsByClassName('hidden-answer');
  console.log(answers.length);
  for (var i = answers.length - 1; i >= 0; i--) {
    const responseRow = answers[i];
    responseRow.classList.remove('hidden-answer');
    const responseRowId = responseRow.id;
    const targetQuestionRowId = responseRowId.replace('answer-for-', 'question-');
    const targetQuestionRow = document.getElementById(targetQuestionRowId);
    targetQuestionRow.parentNode.insertBefore(responseRow, targetQuestionRow.nextSibling);
  }
}

function drawEmptyResponseFeed() {
  const parentContainer = document.getElementById('record-responses');
  const responsesGrid = document.createElement('div');
  responsesGrid.setAttribute('class', 'responses-grid');
  parentContainer.appendChild(responsesGrid);

  const infoPara = document.createElement('p');
  infoPara.innerHTML = 'Nobody has responsed to this module request.';
  responsesGrid.appendChild(infoPara);

  const specialInfoDiv = document.querySelector('#special-info');
  specialInfoDiv.style.display = 'block';
  specialInfoDiv.innerHTML = '<i class="fa fa-star"></i> Be the first to respond!';
}