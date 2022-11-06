$(function() {
	window.setInterval(function() {
		$('.fleets').each(function() {
			var s		= $(this).data('fleet-time') - (serverTime.getTime() - startTime) / 1000;
			if(s <= 0) {
				$(this).text('-');
			} else {
				$(this).text(getRestTimeFormat(s));
			}
		})
	}, 1000);
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
const ship_storage={
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
      console.log(shipinpt)
      shipinpt.addEventListener("input", () => setTimeout(show_values, 100));
    }
  }
}
function numberWithCommas(x) {
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}
function show_values() {
  let fpoints = 0
  let tpoints= 0
  for (let s in exp_values) {
    let shipinpt = document.getElementById("ship" + s + "_input")
    if (shipinpt && shipinpt.value!=="") {
      fpoints += exp_values[s] * parseInt(shipinpt.value)
      tpoints += ship_storage[s] * parseInt(shipinpt.value)
    }
  }
  document.getElementById("expocount").innerText ="Exp:"+ fpoints + " / " + document.getElementById("expocount").getAttribute("expocap")
  document.getElementById("cargospace").innerText = "cargo:" + numberWithCommas(tpoints)
}
document.addEventListener("DOMContentLoaded", function() {
  show_values()
  add_exp_eventlisteners()
});



