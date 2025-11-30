<div class="w3-row">
    <div class="w3-container">
        <h1><?= $headline ?></h1>
        <?= flashdata() ?>
        <p>
            <a href="<?= BASE_URL ?>properties/create"><button class="w3-button w3-medium primary">
                <i class="fa fa-pencil"></i> CREATE NEW RECORD</button>
            </a>
        </p>
        <div id="loader" class="loadersmall"></div>
        <p id="showing-statement"></p>
        <div class="pagination" id="pagination"></div>

        <table class="w3-table results-tbl" id="results-tbl" style="margin-left: -2000em;">
            <thead>
                <tr class="primary">                    <th colspan="2">
                        <div class="table-top">
                            <div>   
                                <input type="text" id="searchPhrase" placeholder="Search records..." >
                                <button onclick="submitSearch()"><i class="fa fa-search"> Search</i></button>
                            </div>
                            <div>Records Per Page:
                                <div class="w3-dropdown-click">
                                    <button id="perPage" onclick="togglePerPage()"></button>
                                    <div id="per-page-options" class="w3-dropdown-content w3-bar-block w3-border" style="right:0">
                                        <a href="#" class="w3-bar-item w3-button" onClick="setPerPage(10)">10</a>
                                        <a href="#" class="w3-bar-item w3-button" onClick="setPerPage(20)">20</a>
                                        <a href="#" class="w3-bar-item w3-button" onClick="setPerPage(50)">50</a>
                                        <a href="#" class="w3-bar-item w3-button" onClick="setPerPage(100)">100</a>
                                    </div>
                                </div>
                            </div>    
                        </div>
                    </th>
                </tr>

                <tr class="secondary">
                    <th>Property Title</th>
                    <th style="width: 20px;">Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <div class="pagination" id="pagination-btm" style="margin-top: 2em;"></div>
    </div>
</div>

<script>
var token = '<?= $token ?>';
var limit = 20;
var offset = 0;
var pageNum = 1;
var totalRows = <?= $total_rows ?>;
var recordNamePlural = '<?= $record_name_plural ?>';

function fetchRecords(pageNum) {
  buildPagination(pageNum);

  if (totalRows < 1) {
    noResults();
  } else {
    getRecords();
  }


}

function refreshResults(searchPhrase) {
  //update totalRows
    var params = {        
      'property_title LIKE': '%' + searchPhrase + '%'
    }

  var target_url = '<?= BASE_URL ?>api/count/properties';

  const http = new XMLHttpRequest()
  http.open('POST', target_url)
  http.setRequestHeader('Content-type', 'application/json')
  http.setRequestHeader("trongateToken", token)
  http.send(JSON.stringify(params))
  http.onload = function () {

    var numRows = http.responseText;

    if (isNaN(numRows)) {
      noResults();
    } else {
      //update totalRows
      totalRows = numRows;
      pageNum = 1;
      fetchRecords(pageNum);
    }

  }

}

function tryAgain() {
  document.getElementById('searchPhrase').value = '';
  totalRows = '<?= $total_rows ?>';
  pageNum = 1;
  fetchRecords(pageNum);
}

function noResults() {

  var searchPhrase = document.getElementById('searchPhrase').value;

  if (searchPhrase !== '') {
    var msg = '<p>Your search produced no results.</p>';

    var tryAgainBtn = `<button onclick="tryAgain()" class="w3-button w3-medium w3-white w3-border">
                            <i class="fa fa-refresh"></i> TRY AGAIN</button>`;

    msg = msg.concat(tryAgainBtn);


  } else {
    var msg = 'There are currently no property records';
  }

  document.getElementById("showing-statement").innerHTML = msg;
  document.getElementById("loader").style.display = 'none';
  document.getElementById("pagination").innerHTML = '';
  document.getElementById("pagination-btm").innerHTML = '';
  document.getElementById("results-tbl").style.marginLeft = '-2000em';
}

function submitSearch() {

  var searchPhrase = document.getElementById('searchPhrase').value;

  if (searchPhrase !== '') {

    pageNum = 1;
    document.getElementById("loader").style.display = 'block';
    document.getElementById("results-tbl").style.marginLeft = '-2000em';
    document.getElementById("showing-statement").innerHTML = '';
    document.getElementById("pagination").innerHTML = '';
    document.getElementById("pagination-btm").innerHTML = '';

    //update the totalRows
    refreshResults(searchPhrase);

  } else {
    pageNum = 1;
    totalRows = '<?= $total_rows ?>';
    fetchRecords(pageNum);
  }
  
}

function getRecords() {

  var searchPhrase = document.getElementById('searchPhrase').value;
  document.getElementById("results-tbl").tBodies[0].innerHTML = '';

  if (searchPhrase !== '') {

    var params = {        
      'property_title LIKE': '%' + searchPhrase + '%',
      orderBy: '<?= $order_by ?>',
      limit,
      offset
    }

  } else {

    var params = {
      orderBy: '<?= $order_by ?>',
      limit,
      offset
    }

  }

  var target_url = '<?= BASE_URL ?>api/get/properties';

  const http = new XMLHttpRequest()
  http.open('POST', target_url)
  http.setRequestHeader('Content-type', 'application/json')
  http.setRequestHeader("trongateToken", token)
  http.send(JSON.stringify(params))
  http.onload = function () {

    var records = JSON.parse(http.responseText);
    var newData = '<tbody>';

    for (var i = 0; i < records.length; i++) {
      records[i];
      var recordUrl = '<?= BASE_URL ?>properties/show/' + records[i]['id'];
      var editBtn = '<a href="' + recordUrl + '"><button type="button" class="btn btn-xs">View</button></a>';
      var newRow = `<tr>
                        <td>${records[i]['property_title']}</td>
                        <td>${editBtn}</td>
                    </tr>`;

      newData = newData.concat(newRow);
    }

    newData = newData.concat('</tbody>');

    var searchPhrase = document.getElementById('searchPhrase').value;
    var resultsTable = document.getElementById("results-tbl").innerHTML;
    document.getElementById("results-tbl").innerHTML = resultsTable.replace('<tbody></tbody>', newData);
    document.getElementById("loader").style.display = 'none';
    document.getElementById("results-tbl").style.marginLeft = '0em';
    document.getElementById("perPage").innerHTML = limit;

    if (searchPhrase !== '') {
      document.getElementById('searchPhrase').value = searchPhrase;
    }

  }
}

function buildPagination(pageNum) {

  if (pageNum == 1) {
    offset = 0;
  } else {
    offset = limit * (pageNum - 1);
  }

  if (totalRows <= limit) {
    return;
  }

  var maxLinks = 10;
  var addFirst = true;
  var addLast = true;
  var addPrev = true;
  var addNext = true;

  //calculate number of pages
  var totalPages = Math.ceil(totalRows / limit);
  var currentPage = pageNum;

  //figure out startPoint
  var startPoint = currentPage - (maxLinks - 1);

  if (startPoint < 1) {
    startPoint = 1;
  }

  var numBeforeLinks = currentPage - startPoint;

  //figure out endPoint
  var endPoint = currentPage + maxLinks;
  var numAfterLinks = endPoint - currentPage;

  if (endPoint > totalPages) {
    endPoint = totalPages + 1;
  }

  //modify number of before links
  var totalLinksRequired = numBeforeLinks + numAfterLinks;

  if (totalLinksRequired > maxLinks) {
    //too many links!

    //modify the startPoint
    startPoint = currentPage - Math.ceil(maxLinks / 2);

    if (startPoint < 1) {
      startPoint = 1;
    }

    //modify the endPoint
    endPoint = currentPage + Math.ceil(maxLinks / 2);

    if (endPoint > totalPages) {
      endPoint = totalPages + 1;
    }

  }

  if (endPoint - startPoint < maxLinks) {
    var numAfterLinks = endPoint - currentPage;
    endPoint = endPoint + Math.ceil(maxLinks / 2);
    if (endPoint > totalPages) {
      endPoint = totalPages + 1;
    }
  }

  if (currentPage < 2) {
    addPrev = false;
    addFirst = false;
  }

  if (currentPage >= totalPages) {
    addNext = false;
    addLast = false;
  }

  var pagination = [];

  if (addFirst == true) {
    pagination.push("First");
  }

  if (addPrev == true) {
    pagination.push("Prev");
  }

  var halfMax = Math.ceil(maxLinks);
  var endPointLimit = startPoint + halfMax;

  if (endPoint > endPointLimit) {
    endPoint = endPointLimit;
  }

  for (var i = startPoint; i < endPoint; i++) {
    pagination.push(i);
  }

  if (addNext == true) {
    pagination.push("Next");
  }

  if (addLast == true) {
    pagination.push("Last");
  }

  drawPagination(pagination, pageNum, totalPages);

}

function drawPagination(pagination, pageNum, totalPages) {

  var paginationHtml = '';

  for (var i = 0; i < pagination.length; i++) {

    switch (pagination[i]) {
      case "First":
        linkLabel = 'First';
        linkValue = 1;
        break;
      case "Last":
        linkLabel = 'Last';
        linkValue = totalPages;
        break;
      case "Prev":
        linkLabel = '«';
        linkValue = pageNum - 1;
        break;
      case "Next":
        linkLabel = '»';
        linkValue = pageNum + 1;
        break;
      default:
        linkLabel = pagination[i];
        linkValue = pagination[i];
        break;
    }

    if (linkValue == pageNum) {
      paginationHtml = paginationHtml.concat('<a href="#" class="active" onclick="fetchRecords(' + linkValue + ')">' + linkLabel + '</a>');
    } else {
      paginationHtml = paginationHtml.concat('<a href="#" onclick="fetchRecords(' + linkValue + ')">' + linkLabel + '</a>');
    }

  }

  document.getElementById("pagination").innerHTML = paginationHtml;
  document.getElementById("pagination-btm").innerHTML = paginationHtml;
  addShowingStatement(limit, pageNum, totalRows, recordNamePlural);
}

fetchRecords(pageNum);

function addShowingStatement(limit, pageNum, totalRows, recordNamePlural) {

  var value1 = offset + 1;
  var value2 = offset + limit;
  var value3 = totalRows;

  if (value2 > value3) {
    value2 = value3;
  }

  showingStatement = `Showing ${value1} to ${value2} of ${value3} ${recordNamePlural}.`;
  document.getElementById("showing-statement").innerHTML = showingStatement;
}

function togglePerPage() {
  var x = document.getElementById("per-page-options");
  if (x.className.indexOf("w3-show") == -1) {
    x.className += " w3-show";
  } else {
    x.className = x.className.replace(" w3-show", "");
  }
}

function setPerPage(perPage) {
  pageNum = 1;
  limit = perPage;
  fetchRecords(1);
}

//When the user clicks anywhere outside of the dropdown btn, close it
window.onclick = function (event) {
  if (event.target !== perPage) {
    var x = document.getElementById("per-page-options");
    x.className = x.className.replace(" w3-show", "");
  }
}
</script>

<style>
.table-top {
    display: flex;
    flex-direction: row;
    align-items: stretch;
    justify-content: space-between;
}
</style>