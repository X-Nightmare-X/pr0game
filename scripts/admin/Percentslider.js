function addpercentsliderstuff(list) {
  const totalamt = parseInt(document.getElementById(list[0]).max)
  const objectlist = list.map(function(e) {
    return document.getElementById(e);
  });


  for (let i = 0; i < list.length; i++) {
    document.getElementById(list[i] + "Value").innerText = 100 * (parseInt(objectlist[i].value) / totalamt) + " %"
    objectlist[i].oninput= function () {
      document.getElementById(list[i] + "Value").innerText = 100 * (parseInt(objectlist[i].value) / totalamt) + " %"
      let total = 0
      for (let slider of objectlist) {
        total = total + parseInt(slider.value)
      }
      let tochange = Math.trunc(totalamt - total)
      let cpos = i + 1
      if (cpos === objectlist.length) {
        cpos = 0
      }
      while (tochange !== 0) {
        let nval = Math.trunc(parseInt(objectlist[cpos].value) + tochange)
        if (nval < 0) {
          tochange = nval
          objectlist[cpos].value = 0
          document.getElementById(list[cpos] + "Value").innerText = 100 * (0 / totalamt) + " %"
          cpos = cpos + 1
          if (cpos === objectlist.length) {
            cpos = 0
          }
        } else {
          tochange = 0
          objectlist[cpos].value = nval
          document.getElementById(list[cpos] + "Value").innerText = 100 * (nval / totalamt) + " %"
        }
      }
    }
  }
}
