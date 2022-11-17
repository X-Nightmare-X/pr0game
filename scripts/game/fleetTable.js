$(function () {
  window.setInterval(function () {
    $('.fleets').each(function () {
      var s = $(this).data('fleet-time') - (serverTime.getTime() - startTime) / 1000;
      if (s <= 0) {
        $(this).text('-');
      } else {
        $(this).text(getRestTimeFormat(s));
      }
    })
    const abortfleet = document.getElementsByClassName("aborttime")


    for (let fleet of abortfleet) {
      let cdate = new Date(serverTime)
      let startdate = new Date(new Date(1000 * parseInt(fleet.getAttribute("starttime"))).toLocaleString('en-US', {timeZone: localtimezonestring}))
      let difftonow = cdate - startdate
      cdate.setMilliseconds(cdate.getMilliseconds() + difftonow)
      let daydiff = Math.trunc((cdate - new Date(serverTime)) / (1000 * 60 * 60 * 24));

      if (daydiff > 0) {
        fleet.innerText = daydiff + "T " + pad(cdate.getHours(), 2) + ":" + pad(cdate.getMinutes(), 2) + ":" + pad(cdate.getSeconds(), 2)
      } else {
        fleet.innerText = pad(cdate.getHours(), 2) + ":" + pad(cdate.getMinutes(), 2) + ":" + pad(cdate.getSeconds(), 2)
      }
    }
  }, 500);
});


const exp_values = {
  "202": 20,
  "203": 60,
  "204": 20,
  "205": 50,
  "206": 135,
  "207": 300,
  "208": 150,
  "209": 80,
  "210": 5,
  "211": 375,
  "212": 0,
  "213": 550,
  "214": 45000,
  "215": 350
}
const ship_storage = {
  "202": 5000,
  "203": 25000,
  "204": 50,
  "205": 100,
  "206": 800,
  "207": 1500,
  "208": 7500,
  "209": 20000,
  "210": 5,
  "211": 500,
  "212": 0,
  "213": 2000,
  "214": 1000000,
  "215": 750
}

function add_exp_eventlisteners() {
  for (let sid in exp_values) {
    let shipinpt = document.getElementById("ship" + sid + "_input")
    if (shipinpt) {
      shipinpt.addEventListener("input", () => setTimeout(show_values, 100));
    }
  }
}

function numberWithCommas(x) {
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function show_values() {
  let fpoints = 0
  let tpoints = 0
  for (let s in exp_values) {
    let shipinpt = document.getElementById("ship" + s + "_input")
    if (shipinpt && shipinpt.value !== "") {
      fpoints += exp_values[s] * parseInt(shipinpt.value)
      tpoints += ship_storage[s] * parseInt(shipinpt.value)
    }
  }
  document.getElementById("expocount").innerText = "Exp:" + fpoints + " / " + document.getElementById("expocount").getAttribute("expocap")
  document.getElementById("cargospace").innerText = "cargo:" + numberWithCommas(tpoints)
}

document.addEventListener("DOMContentLoaded", function () {
  show_values()
  add_exp_eventlisteners()
  addstoragelisteners()
  updatebuttons();
});

function addstoragelisteners() {
  document.getElementsByName("met_storage")[0].addEventListener("input", updatebuttons);
  document.getElementsByName("krist_storage")[0].addEventListener("input", updatebuttons);
  document.getElementsByName("deut_storage")[0].addEventListener("input", updatebuttons);
  document.getElementsByName("met_storage")[0].value = parseInt(document.getElementById("current_metal").getAttribute("data-real"))
  document.getElementsByName("krist_storage")[0].value = parseInt(document.getElementById("current_crystal").getAttribute("data-real"))
  document.getElementsByName("deut_storage")[0].value = parseInt(document.getElementById("current_deuterium").getAttribute("data-real"))
  document.getElementById("gt_select").addEventListener("click", setamt);
  document.getElementById("kt_select").addEventListener("click", setamt);
}

function updatebuttons() {
  let ttl = 0
  ttl += isNaN(parseInt(document.getElementsByName("met_storage")[0].value)) ? 0 : parseInt(document.getElementsByName("met_storage")[0].value.replaceAll(".", ""))
  ttl += isNaN(parseInt(document.getElementsByName("krist_storage")[0].value)) ? 0 : parseInt(document.getElementsByName("krist_storage")[0].value.replaceAll(".", ""))
  ttl += isNaN(parseInt(document.getElementsByName("deut_storage")[0].value)) ? 0 : parseInt(document.getElementsByName("deut_storage")[0].value.replaceAll(".", ""))
  let gts = Math.ceil(ttl / 25000)
  let kts = Math.ceil(ttl / 5000)
  if (ttl / 25000 - gts > 0.8) {
    gts += 1
  }
  if (ttl / 5000 - kts > 0.5) {
    kts += 1
  }
  document.getElementById("kt_amt").innerText = " ( " + numberWithCommas(kts) + " ) "
  document.getElementById("gt_amt").innerText = " ( " + numberWithCommas(gts) + " ) "
}

function setamt() {
  noShips()
  if (this.id === "gt_select") {
    let gt = document.getElementById("ship203_input")
    if (gt !== null) {
      gt.value = parseInt(document.getElementById("gt_amt").innerText.replaceAll(".", "").replaceAll("(", "").replaceAll(")", ""))
    }
  } else {
    let gt = document.getElementById("ship202_input")
    if (gt) {
      gt.value = parseInt(document.getElementById("kt_amt").innerText.replaceAll(".", "").replaceAll("(", "").replaceAll(")", ""))
    }
  }
}

