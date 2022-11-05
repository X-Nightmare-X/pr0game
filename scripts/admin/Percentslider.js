/*
					<input type="range" min="0" max="100" value="50" class="slider" id="expoMetal"> {$LNG.tech.901}: <span id="expoMetalValue"></span><br>
					<input type="range" min="0" max="100" value="33" class="slider" id="expoCrystal"> {$LNG.tech.902}: <span id="expoCrystalValue"></span><br>
					<input type="range" min="0" max="100" value="17" class="slider" id="expoDeut"> {$LNG.tech.903}: <span id="expoDeutValue"></span>



*/

function addpercentsliderstuff(list, totalamt) {
  const objectlist = list.map(function (e) {
    return document.getElementById(e);
  });


  for (let i = 0; i < list.length; i++) {
    document.getElementById(list[i] + "Value").innerText=100*(parseInt(objectlist[i].value)/totalamt) + " %"


    objectlist.addEventListener("change", () => {
      document.getElementById(list[i] + "Value").innerText=100*(parseInt(objectlist[i].value)/totalamt) + " %"
      let total = 0
      for (let slider of objectlist) {
        total = total + parseInt(slider.value)
      }
      let tochange = totalamt - total
      let cpos = i + 1
      if (cpos === objectlist.length) {
        cpos = 0
      }
      while (tochange !== 0) {
        let nval = parseInt(objectlist[cpos].value) - tochange
        if (nval < 0) {
          tochange = nval
          cpos = cpos + 1
          if (cpos === objectlist.length) {
            cpos = 0
          }
          objectlist[cpos].value = 0
        } else {
          objectlist[cpos].value = nval
          tochange = 0
        }
      }
    })


  }


}
