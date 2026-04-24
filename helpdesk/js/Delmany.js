var rowID=0;

function addMoreRows() {

    var user = document.getElementById('user_id').value;
    var date = document.getElementById('date').value;
    var color = document.getElementById('color').value;
    var table = document.getElementById('tbl_id');

    var row = table.insertRow();

    var rowBox = row.insertCell(0);
    var userName = row.insertCell(1);
    var Date = row.insertCell(2);
    var Color = row.insertCell(3);
    var checkbox = row.insertCell(4);

    rowBox.innerHTML = '<input type="checkbox" id="delete' + getRowId() + '">';
    userName.innerHTML = user;
    Date.innerHTML = date;
    Color.innerHTML = color;

}

function deleteMoreRows(tableID) {

    var table = document.getElementById(tableID);
    var rowCount = table.rows.length;
    var selectedRows = getCheckedBoxes();

    selectedRows.forEach(function(currentValue) {
      deleteRowByCheckboxId(currentValue.id);
    });
}

function getRowId() {
  rowId += 1;
  return rowId;
}

function getRowIdsFromElements($array) {
  var arrIds = [];

  $array.forEach(function(currentValue, index, array){
    arrIds.push(getRowIdFromElement(currentValue));
  });

  return arrIds;
}

function getRowIdFromElement($el) {
    return $el.id.split('delete')[1];
}

function getCheckedBoxes() {
  var inputs = document.getElementsByTagName("input");
  var checkboxesChecked = [];

  // loop over them all
  for (var i=0; i < inputs.length; i++) {
     // And stick the checked ones onto an array...
     if (inputs[i].checked) {
        checkboxesChecked.push(inputs[i]);
     }
  }

  // Return the array if it is non-empty, or null
  return checkboxesChecked.length > 0 ? checkboxesChecked : null;
}

function deleteRowByCheckboxId(CheckboxId) {
    var checkbox = document.getElementById(CheckboxId);
    var row = checkbox.parentNode.parentNode;                //box, cell, row, table
    var table = row.parentNode;

    while ( table && table.tagName != 'TABLE' )
        table = table.parentNode;
    if (!table) return;
    table.deleteRow(row.rowIndex);
}