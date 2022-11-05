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


function getcurrentsys() {
  const cdate = serverTime.getTime()
  const galaxy = document.getElementsByName("galaxy")[0].value
  const system = document.getElementsByName("system")[0].value
  let systable = document.getElementsByClassName("table569")[0]
  for (let row of systable.getElementsByTagName("tr")) {
    let tds = row.getElementsByTagName("td")
    if (tds.length !== 8 || tds[1].getElementsByTagName("img").length === 0) {
      continue
    }
    let name = tds[5]
    let pid = name.getElementsByTagName("span")[0].getAttribute("playerid")
    let pname = name.getElementsByTagName("span")[0].innerText.trim()
    let moon = tds[2].getElementsByTagName("img").length === 1
    let ally = tds[6].getElementsByTagName("a")
    let allyid = "-1"
    let allyname = "-"
    if (ally.length === 1) {
      allyname = ally[0].innerText.trim()
      allyid = ally[0].getAttribute("allyid")
    }
    let pos = parseInt(tds[0].innerText)
    let status = ""
    for (let st of name.getElementsByClassName("galaxy-short")) {
      status + st.innerText + " "
    }
    status = status.trim()
    console.log(pid, pname, moon, pos)
    insertintouniverse({
      planet: galaxy + ":" + system + ":" + pos,
      moon: moon,
      player: pname,
      alliance: allyname,
      status: status,
      time: cdate,
      pid: pid,
      aid: allyid
    })
  }
}

function insertintouniverse(obj) {
  if (universe[obj.planet]) {
    if (obj.time > universe[obj.planet].time) {
      universe[obj.planet] = obj
    }
  } else {
    universe[obj.planet] = obj
  }
}

getcurrentsys();

function planet_sort(b, a) {
  var coords_a = a.planet.split(":");
  var coords_b = b.planet.split(":");

  if (coords_a[0] > coords_b[0]) {
    return false;
  }
  if (coords_a[0] === coords_b[0] && coords_a[1] > coords_b[1]) {
    return false;
  }
  return !(coords_a[0] === coords_b[0] && coords_a[1] === coords_b[1] && coords_a[2] > coords_b[2]);


}

function download() {


  var data = "Planet, Mond, Spieler, Allianz, Status, Timestamp, Spieler Id, Allianz Id\n";
  for (var entry of Object.keys(universe).sort(planet_sort)) {
    data += universe[entry].planet + ", " + (universe[entry].moon ? "X" : "") + ", "
      + universe[entry].player + ", " + universe[entry].alliance + ", "
      + universe[entry].status + ", " + universe[entry].time + ", " + universe[entry].pid + ", " + universe[entry].aid + "\n";
  }
  return 'data:text/plain;charset=utf-8,' + encodeURIComponent(data)
}

function add_import_button() {

  var universe = GM_getValue("universe");

  $('<span/>', {
    'html': 'Importiere Universum:'
  }).appendTo($('content'));

  $('<br/>').appendTo($('content'));

  $('<input/>', {
    'type': 'file',
    'id': 'file_input'
  }).on('change', function () {
    for (var file of this.files) {

      var reader = new FileReader();
      reader.onload = function (progressEvent) {
        // By lines
        var lines = this.result.split('\n');
        for (var line of lines) {
          // Skip headline
          if (line.startsWith("Planet") || line == "") {
            continue;
          }

          var data = line.split(",").map(function (value) {
            return value.trim();
          });
          ;

          // Create entry in universe list
          var entry = {
            'planet': data[0],
            'moon': data[1] == "X",
            'player': data[2],
            'alliance': data[3],
            'status': data[4],
            time: data[5],
            pid: data[6],
            aid: data[7]
          };


          insertintouniverse(entry);
        }

        GM_setValue("universe", universe);
        alert("Importiert!")
      }
      reader.readAsText(file);
    }
  }).appendTo($('content'));
}




function add_delete_cache_button() {
  $('<br/>').appendTo($('content'));
  $('<a/>', {
    'text': 'Cache löschen'
  }).on('click', function () {
    localStorage.removeItem('universe');
  }).appendTo($('content'));
};
add_export_button();
add_import_button()
add_delete_cache_button();

function add_button() {
  let nad = document.querySelector("input[type=submit]").parentNode;

  const para = document.createElement("button");
  para.innerText = "Sync";
  para.id = "syncbutton"
  para.type = "button"
  nad.appendChild(para);
  document.getElementById("syncbutton").onclick = () => {
    getcurrentsys();
    para.innerText = "Synced";
  }
}


add_button();

function add_export_button() {
  $('<a/>', {
    'id': 'export_universe',
    'text': 'Exportiere Universum',
  }).attr("download", "universe.csv").attr("href", download()).appendTo($('content'));
  $('<br/>').appendTo($('content'));
}




function addplanets() {


  let systable = document.getElementsByClassName("table569")[0]
  for (let row of systable.getElementsByTagName("tr")) {
    let tds = row.getElementsByTagName("td")
    if (tds.length !== 8 || tds[1].getElementsByTagName("img").length === 0) {
      continue
    }
    let name = tds[5]
    let pid = name.getElementsByTagName("span")[0].getAttribute("playerid")
    console.log(name.getElementsByTagName("a")[0])
    let datacontent = name.getElementsByTagName("a")[0].getAttribute("data-tooltip-content");
    let toadd = '<br><u>Planeten</u><br><br>'
    const galaxy = document.getElementsByName("galaxy")[0].value
    const system = document.getElementsByName("system")[0].value
    let toaddary=[]
    for (let ex in universe) {
      let entry=universe[ex]
      console.log(entry,pid)
      if (entry.pid == pid) {
        let spx = entry.planet.split(":")
        const planet_galaxy = spx[0]
        const planet_system = spx[1]

        var font_weight_start = "";
        var font_weight_end = "";

        if (galaxy == planet_galaxy && system == planet_system) {
          font_weight_start = "<b>";
          font_weight_end = "</b>"
        }
        let cdate=new Date(entry.time)
        let timestp=cdate.getDay()-1 + "." + (cdate.getMonth() +1)

        toaddary.push({planet:entry.planet,text:'<a href="game.php?page=galaxy&amp;galaxy=' + planet_galaxy + '&amp;system=' + planet_system + '">' +font_weight_start + '[' + entry.planet + ']' + font_weight_end + '</a> '+ timestp+'<br>'})
      }
    }
    toaddary = toaddary.sort(planet_sort)
    for(let x of toaddary){
      toadd +=x.text
    }
    tds[5].getElementsByTagName("a")[0].setAttribute("data-tooltip-content", datacontent + toadd);
  }
}

addplanets();
