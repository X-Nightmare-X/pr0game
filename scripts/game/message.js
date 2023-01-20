Message	= {
	MessID : 0,

	MessageCount: function() {
		if(Message.MessID == 100) {
			$('#unread_0').text('0');
			$('#unread_1').text('0');
			$('#unread_2').text('0');
			$('#unread_3').text('0');
			$('#unread_4').text('0');
			$('#unread_5').text('0');
			$('#unread_15').text('0');
			$('#unread_99').text('0');
			$('#unread_100').text('0');
			$('#newmes').text('');
		} else {
			var count = parseInt($('#unread_'+Message.MessID).text());
			var lmnew = parseInt($('#newmesnum').text());

			$('#unread_'+Message.MessID).text(Math.max(0, $('#unread_100').text() - 10));
			if(Message.MessID != 999) {
				$('#unread_100').text($('#unread_100').text() - count);
			}

			if(lmnew - count <= 0)
				$('#newmes').text('');
			else
				$('#newmesnum').text(lmnew - count);
		}
	},

	getMessages: function (MessID, page) {
		if (typeof page === "undefined") {
			page = 1;
		}
		Message.MessID	= MessID;
		Message.MessageCount(MessID);

		$('#loading').show();

		$.get('game.php?page=messages&mode=view&messcat='+MessID+'&site='+page+'&ajax=1', function(data) {
			$('#loading').hide();
			$('#messagestable').remove();
			$('#content table:eq(0)').after(data);
		});
	},

	stripHTML: function (string) {
		return string.replace(/<(.|\n)*?>/g, '');
	},

	CreateAnswer: function (Answer) {
		var Answer	= Message.stripHTML(Answer);
		if(Answer.substr(0, 3) == "Re:") {
			return 'Re[2]:'+Answer.substr(3);
		} else if(Answer.substr(0, 3) == "Re[") {
			var re = Answer.replace(/Re\[(\d+)\]:.*/, '$1');
			return 'Re['+(parseInt(re)+1)+']:'+Answer.substr(5+parseInt(re.length))
		} else {
			return 'Re:'+Answer
		}
	},

	getMessagesIDs: function(Infos) {
		var IDs = [];
		$.each(Infos, function(index, mess) {
			if(mess.value == 'on')
				IDs.push(mess.name.replace(/delmes\[(\d+)\]/, '$1'));
		});
		return IDs;
	},

	delMessage: function(ID) {

		$('#loading').show();

		$.getJSON('game.php?page=messages&mode=deleteMessage&delMessID='+ID+'&ajax=1', function(data) {
			$('#loading').hide();

			$('.message_'+ID).remove();

			if(data.code > 0)
				NotifyBox(data.mess);
			else
				NotifyBox(data.mess);
		});

	}
}

function scavengers() {


	// TODO debris values übergeben
	const recycleList = {
		"202": 4000, //KT
		"203": 12000, //GT
		"204": 4000, //LJ
		"205": 10000, //SJ
		"206": 27000, //Xer
		"207": 60000, //SS
		"208": 30000, //Colo
		"209": 16000, //Rec
		"210": 1000, //Spio
		"211": 75000, //B
		"212": 2000, //Sat
		"213": 110000, //Zer
		"214": 9000000, //RIP
		"215": 70000, //SXer
	};

	// TODO market values & techs übergeben
  const metalValueFromMarketplace = 4;
  const krisValueFromMarketplace = 0.8;
  const deutValueFromMarketplace = 1;
  const impulseEngineTech = 10;
	const combustionEngineTech = 8;
  const tfperc=40;
	const marketRatios = {
		"901": metalValueFromMarketplace,
		"902": krisValueFromMarketplace,
		"903": deutValueFromMarketplace,
	};
  const fleetspeed = 1;
  const stbSettings= {'stb_big_time':,
    'stb_med_time':,
    'stb_small_time':,
    'stb_big_ress':,
    'stb_med_ress':,
    'stb_small_ress':
  }




	function spyReportParser(spyReportElement) {
		let spyReport = {};
		var spyReportHead = spyReportElement.querySelector(".spyRaportHead");
		let cords = spyReportHead.getAttribute('coords').split(':');
		spyReport.head = {
							galaxy : cords[0],
							system : cords[1],
							planet : cords[2],
						};
		spyReport.content ={}
    for(let id of [901, 902, 903]){
        spyReport.content[id] = 0;
    }
    for(let field of spyReportElement.parentElement.querySelectorAll('div[data-info]')){
      spyReport.content[field.getAttribute("data-info").split('_')[1]]=parseInt(field.innerText.replaceAll('.', ''))
    }
		return spyReport;
	}

	function sumUpResources(spyReport) {
		return spyReport.content[901] + spyReport.content[902] + spyReport.content[903];
	}

	function calculateNeededCapacity(metal, crystal, deuterium) {
		return 0.5 * Math.max(metal + crystal + deuterium, Math.min(0.75 * (2 * metal + crystal + deuterium), 2 * metal + deuterium));
	}

	function calculateResourceMarketValue(metal, crystal, deuterium) {
		return Math.ceil(metal / marketRatios[901] + crystal / marketRatios[902] + deuterium / marketRatios[903])
	}

	function estimateTransporters(capacity,maxcap) {
		return Math.ceil(capacity / maxcap) + 1;
	}

	function estimateRecyclers(recycleValue) {
		return Math.ceil(recycleValue / 20000) + 1;
	}


	function raidTimeKT(met, kris, deut, raidLocation) {
		var bestPlanet,
			flytime = '',
			flytimeBestPlanet = '',
			flightSpeedKT = flightSpeed("202"),
			calculateResourceMarketValuePerSecond,
			calculateResourceMarketValuePerSecondBestPlanet,
			galaJump,
			galaJumpPlanet;

		var bestLocationForRaid = bestLocation(raidLocation);
		bestPlanet = bestLocationForRaid.bestPlanet;
		galaJump = bestLocationForRaid.galaJump;
		galaJumpPlanet = bestLocationForRaid.galaJumpPlanet;


		//TODO: Prüfen, original aus flotten.js evntl sind hier anpassungen nötig ~ 10er Potenz stimmt noch nicht ganz genau ~~ 5 mins diff
		flytime = Math.max(Math.round((3500 / (100 * 0.1) * Math.pow(bestLocationForRaid.activPlanetdistance * 10 / flightSpeedKT, 0.5) + 10) / 1 * Math.max(0, 1 + 100) * 1, 5));
		flytime = flytime / 10;

		flytimeBestPlanet = Math.max(Math.round((3500 / (100 * 0.1) * Math.pow(bestLocationForRaid.bestPlanetdistance * 10 / flightSpeedKT, 0.5) + 10) / 1 * Math.max(0, 1 + 100) * 1, 5));
		flytimeBestPlanet = flytimeBestPlanet / 10;

		calculateResourceMarketValuePerSecond = (calculateResourceMarketValue(met, kris, deut) / (flytime * 2));
		calculateResourceMarketValuePerSecond = calculateResourceMarketValuePerSecond.toFixed(2);

		calculateResourceMarketValuePerSecondBestPlanet = (calculateResourceMarketValue(met, kris, deut) / (flytimeBestPlanet * 2));
		calculateResourceMarketValuePerSecondBestPlanet = calculateResourceMarketValuePerSecondBestPlanet.toFixed(2);

		return {
			'calculateResourceMarketValuePerSecond': calculateResourceMarketValuePerSecond,
			'calculateResourceMarketValuePerSecondBestPlanet': calculateResourceMarketValuePerSecondBestPlanet,
			'bestPlanet': bestPlanet,
			'galaJumpPlanet': galaJumpPlanet,
			'galaJump': galaJump
		}

	}

	function bestLocation(raidLocation) {

		var activPlanet,
			activPlanetdistance,
			bestPlanet,
			bestPlanetdistance,
			galaJump,
			galaJumpPlanet,
			planetList,
			planetListLenght,
			bestPlanetGalaJump;

		planetList = document.getElementById("planetSelector");
		activPlanet = planetList.options[planetList.selectedIndex].text;
		planetListLenght = planetList.length;

		for (let index = 0; index < planetListLenght; index++) {

			let regexCoordinates = /\[(?<galaxy>[1-9]):(?<system>[0-9]{1,3}):(?<planet>[0-9]{1,2})\]/,
				planetCoordinates = planetList.options[index].text.match(regexCoordinates),
				tempPlananet = [planetCoordinates.groups.galaxy, planetCoordinates.groups.system, planetCoordinates.groups.planet],
				tempDiff,
				tempDistance,
				isActive = 0;

			if (activPlanet == planetList.options[index].text) {
				isActive = 1
			}

			if (raidLocation[0] - tempPlananet[0] != 0) {
				tempDiff = Math.abs(raidLocation[0] - tempPlananet[0]);
				tempDistance = tempDiff * 20000;
				galaJump = 1;
			} else {
				if (raidLocation[1] - tempPlananet[1] != 0) {
					tempDiff = Math.abs(raidLocation[1] - tempPlananet[1]);
					tempDistance = tempDiff * 95 + 2700;
					galaJump = 0;
				} else {
					tempDiff = Math.abs(raidLocation[2] - tempPlananet[2]);
					tempDistance = tempDiff * 5 + 1000;
					galaJump = 0;
				}
			}

			if (index == 0) {
				bestPlanetdistance = tempDistance;
				bestPlanet = tempPlananet;
				if (galaJump == 1) {
					bestPlanetGalaJump = 1
				}
			} else if (bestPlanetdistance > tempDistance) {
				bestPlanetdistance = tempDistance;
				bestPlanet = tempPlananet;
				if (galaJump == 0) {
					bestPlanetGalaJump = 0
				}
			}

			if (isActive == 1) {
				activPlanetdistance = tempDistance;
			}

			// TODO LNG
			if (bestPlanetGalaJump == 1) {
				galaJumpPlanet = "Beliebiger Planet in Gala " + bestPlanet[0]
			} else {
				galaJump = 0;
			}

		}

		return {
			'galaJump': galaJump,
			'galaJumpPlanet': galaJumpPlanet,
			'activPlanetdistance': activPlanetdistance,
			'bestPlanet': bestPlanet,
			'bestPlanetdistance': bestPlanetdistance
		}

	}

	function flightSpeed(shiptype) {
		var speed;


		if (shiptype === "202") {
			if (impulseEngineTech < 5) {
				speed = 5000 * (1 + (0.1 * combustionEngineTech));
			}
			else {
				speed = 10000 * (1 + (0.2 * impulseEngineTech));
			}
		}

		return fleetspeed * speed;

	}

	function determineRecycleValue(spyReport) {
		let summedUpRecycleValue = 0;
		Object.keys(recycleList).forEach(recycleKey => {
			let singleRecycleValue = recycleList[recycleKey];
			if (spyReport.content[recycleKey] !== undefined) {
				summedUpRecycleValue += spyReport.content[recycleKey] * singleRecycleValue ;
			}
		});
		return summedUpRecycleValue* tfperc/100;
	}


	document.querySelectorAll(".spyRaport").forEach(spyReportElement => {
		let spyReport = spyReportParser(spyReportElement);

		var raidLocation = [spyReport.head.galaxy, spyReport.head.system, spyReport.head.planet];
		spyReport.conclusions = {};

		spyReport.conclusions.summedUpResources = sumUpResources(spyReport);
		spyReport.conclusions.neededCapacity = calculateNeededCapacity(
			spyReport.content[901],
			spyReport.content[902],
			spyReport.content[903]
		);
		spyReport.conclusions.ktNeeded = estimateTransporters(spyReport.conclusions.neededCapacity,5000);
    spyReport.conclusions.gtNeeded = estimateTransporters(spyReport.conclusions.neededCapacity,25000);
		spyReport.conclusions.recycleValue = determineRecycleValue(spyReport);
		spyReport.conclusions.recyclersNeeded = estimateRecyclers(spyReport.conclusions.recycleValue);
		spyReport.conclusions.marketValue = calculateResourceMarketValue(
			spyReport.content[901],
			spyReport.content[902],
			spyReport.content[903]
		);


		spyReport.conclusions.MarketValuePerSecond = raidTimeKT(spyReport.content[901], spyReport.content[902], spyReport.content[903], raidLocation);
    let bestrespertime=spyReport.conclusions.MarketValuePerSecond.calculateResourceMarketValuePerSecondBestPlanet
    let bestRessPerTimeClass = "lowRess";
    if (bestrespertime > stbSettings['stb_big_time']) {
      bestRessPerTimeClass = "realHighRess";
    } else if (bestrespertime > stbSettings['stb_med_time']) {
      bestRessPerTimeClass = "highRess";
    } else if (bestrespertime > stbSettings['stb_small_time']) {
      bestRessPerTimeClass = "midRess";
    }


    let ressourcesByMarketValue =spyReport.conclusions.marketValue
    let mkvaluetime="lowRess";
    if (ressourcesByMarketValue > stbSettings['stb_big_ress']) {
      mkvaluetime = "realHighRess";
    } else if (ressourcesByMarketValue > stbSettings['stb_med_ress']) {
      mkvaluetime = "highRess";
    } else if (ressourcesByMarketValue > stbSettings['stb_small_ress']) {
      mkvaluetime = "midRess";
    }
    for(let d of spyReportElement.parentElement.querySelectorAll('*[classadd="marketClass"]')){
      d.classList.add(mkvaluetime);
    }

    for(let d of spyReportElement.parentElement.querySelectorAll('*[classadd="timePerResClass"]')){
      d.classList.add(bestRessPerTimeClass);
    }

     document.getElementsByName("totalRes")[0].innerText=spyReport.conclusions.summedUpResources.toLocaleString("de");
    document.getElementsByName("resToRaid")[0].innerText=spyReport.conclusions.neededCapacity.toLocaleString("de");
    document.getElementsByName("resToRec")[0].innerText=spyReport.conclusions.recycleValue.toLocaleString("de");
    document.getElementsByName("ktNeeded")[0].innerText=spyReport.conclusions.ktNeeded.toLocaleString("de");
    document.getElementsByName("gtNeeded")[0].innerText=spyReport.conclusions.gtNeeded.toLocaleString("de");
    document.getElementsByName("ktNeeded")[1].innerText=spyReport.conclusions.ktNeeded.toLocaleString("de");
    document.getElementsByName("gtNeeded")[1].innerText=spyReport.conclusions.gtNeeded.toLocaleString("de");
    document.getElementsByName("recNeeded")[0].innerText=spyReport.conclusions.recyclersNeeded.toLocaleString("de");
    document.getElementsByName("marketValue")[0].innerText=spyReport.conclusions.marketValue.toLocaleString("de");
    document.getElementsByName("resPerSec")[0].innerText=spyReport.conclusions.MarketValuePerSecond.calculateResourceMarketValuePerSecondBestPlanet.toLocaleString("de");
    document.getElementsByName("bestPlanet")[0].innerText= "[" + spyReport.conclusions.MarketValuePerSecond.bestPlanet.join(":") + "]";
    document.getElementsByName("totalRes")[0].innerText=spyReport.conclusions.summedUpResources.toLocaleString("de");
    document.getElementsByName("ktNeeded")[1].parentElement.parentElement.href +=  spyReport.conclusions.ktNeeded
    document.getElementsByName("gtNeeded")[1].parentElement.parentElement.href +=  spyReport.conclusions.gtNeeded
	});

	}


document.addEventListener("DOMContentLoaded", function() {
  let inpts=document.querySelectorAll('input[name*="messageID"]');
  for(let checkbox of inpts){
    checkbox.addEventListener('change', function() {
      for(let inp of document.querySelectorAll('input[name="'+this.name+'"]')){
        inp.checked=this.checked
      }
    });
  }
  scavengers();
});
