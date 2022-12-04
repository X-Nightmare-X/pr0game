function doit(missionID, planetID) {
	$.getJSON("game.php?page=fleetAjax&ajax=1&mission="+missionID+"&planetID="+planetID, function(data)
	{
		$('#slots').text(data.slots);
		if(typeof data.ships !== "undefined")
		{
			$.each(data.ships, function(elementID, value) {
				$('#elementID'+elementID).text(number_format(value));
			});
		}

		var statustable	= $('#fleetstatusrow');
		var messages	= statustable.find("~tr");
		if(messages.length == MaxFleetSetting) {
			messages.filter(':last').remove();
		}
		var element		= $('<td />').attr('colspan', 8).attr('class', data.code == 600 ? "success" : "error").text(data.mess).wrap('<tr />').parent();
		statustable.removeAttr('style').after(element);
	});
}

function galaxy_submit(value) {
	$('#auto').attr('name', value);
	$('#galaxy_form').submit();
}

const universe = GM_getValue("universe", {})

function GM_getValue(key, defaultv) {
  let x = localStorage.getItem(key);
  if (x === null) {
    return defaultv;
  }
  return JSON.parse(x)
}

function GM_setValue(key, value) {
  localStorage.setItem(key, JSON.stringify(value))
}


function addlisteners (){
  document.getElementById("gala_sync").onclick = ()=>{
    syncsys();
    document.getElementById("gala_sync").value = "Synced";

  }

  document.getElementById('gala_upload').addEventListener('change', onChange_import);



  document.getElementById("gala_exportjson").onclick =export_json
    document.getElementById("gala_exportcsv").onclick =export_csv
      document.getElementById("gala_reset").onclick =reset
}
document.addEventListener("DOMContentLoaded", function() {

  addlisteners();
});


function onChange_import(event) {
  var reader = new FileReader();
  reader.onload = onReaderLoad_import;
  reader.readAsText(event.target.files[0]);
}

function onReaderLoad_import(event){
  console.log(event.target.result);
  var obj = JSON.parse(event.target.result);
console.log(obj)
}
function export_csv(){


}
function export_json(){

}
function reset(){

}

function syncsys(){
  universe[galakey]=systemdata;
  GM_setValue("universe", universe)

}
