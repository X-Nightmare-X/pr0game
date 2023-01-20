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


	const resourceIdList = {
		"1": "Metallmine",
		"2": "Kristallmine",
		"3": "Deuteriumsynthetisierer",
		"4": "Solarkraftwerk",
		"6": "TechnoDome",
		"12": "Fusionskraftwerk",
		"14": "Roboterfabrik",
		"15": "Nanitenfabrik",
		"21": "Raumschiffwerft",
		"22": "Metallspeicher",
		"23": "Kristallspeicher",
		"24": "Deuteriumtank",
		"31": "Forschungslabor",
		"33": "Terraformer",
		"34": "Allianzdepot",
		"41": "Basisstützpunkt",
		"42": "Sensorenphalanx",
		"43": "Sprungtor",
		"44": "Raketensilo",
		"106": "Spionagetechnik",
		"108": "Computertechnik",
		"109": "Waffentechnik",
		"110": "Schildtechnik",
		"111": "Raumschiffpanzerung",
		"113": "Energietechnik",
		"114": "Hyperraumtechnik",
		"115": "Verbrennungstriebwerk",
		"117": "Impulstriebwerk",
		"118": "Hyperraumantrieb",
		"120": "Lasertechnik",
		"121": "Ionentechnik",
		"122": "Plasmatechnik",
		"123": "Intergalaktisches Forschungsnetzwerk",
		"124": "Astrophysik",
		"131": "Produktionsmaximierung Metall",
		"132": "Produktionsmaximierung Kristall",
		"133": "Produktionsmaximierung Deuterium",
		"199": "Gravitonforschung",
		"202": "Kleiner Transporter",
		"203": "Großer Transporter",
		"204": "Leichter Jäger",
		"205": "Schwerer Jäger",
		"206": "Kreuzer",
		"207": "Schlachtschiff",
		"208": "Kolonieschiff",
		"209": "Recycler",
		"210": "Spionagesonde",
		"211": "Bomber",
		"212": "Solarsatellit",
		"213": "Zerstörer",
		"214": "Todesstern",
		"215": "Schlachtkreuzer",
		"401": "Raketenwerfer",
		"402": "Leichtes Lasergeschütz",
		"403": "Schweres Lasergeschütz",
		"404": "Gaußkanone",
		"405": "Ionengeschütz",
		"406": "Plasmawerfer",
		"407": "Kleine Schildkuppel",
		"408": "Große Schildkuppel",
		"502": "Abfangrakete",
		"503": "Interplanetarrakete",
		"901": "Metall",
		"902": "Kristall",
		"903": "Deuterium",
		"911": "Energie",
	};

	// TODO attack values übergeben
	const dangersList = {
		"202": 5,
		"203": 5,
		"204": 50,
		"205": 150,
		"206": 400,
		"207": 1000,
		"208": 50,
		"209": 1,
		"211": 1000,
		"213": 2000,
		"214": 200000,
		"215": 700,
		"401": 80,
		"402": 100,
		"403": 250,
		"404": 1100,
		"405": 150,
		"406": 3000,
	};

	// TODO debris values übergeben
	const recycleList = {
		"202": 1200, //KT
		"203": 3600, //GT
		"204": 1200, //LJ
		"205": 3000, //SJ
		"206": 8100, //Xer
		"207": 18000, //SS
		"208": 9000, //Colo
		"209": 4800, //Rec
		"210": 300, //Spio
		"211": 22500, //B
		"212": 600, //Sat
		"213": 33000, //Zer
		"214": 2700000, //RIP
		"215": 21000, //SXer
	};

	// TODO market values & techs übergeben
	var metalValueFromMarketplace = 4;
	var krisValueFromMarketplace = 0.8;
	var deutValueFromMarketplace = 1;
	var impulseEngineTech = 10;
	var combustionEngineTech = 8;

	const marketRatios = {
		"901": metalValueFromMarketplace,
		"902": krisValueFromMarketplace,
		"903": deutValueFromMarketplace,
	};

	function containerParser(containerElements) {
		var spyReportContent = {};
		containerElements.forEach(containerElement => {
			var containerCells = containerElement.querySelectorAll('div[data-info]');
			for (var i = 0; i < containerCells.length; i++ ) {
				// console.log(containerCells[i])
				let id = containerCells[i].getAttribute('data-info').split('_')[1];
				// var identifierMatch = containerCells[i].innerHTML.match(/return Dialog\.info\(([0-9]+)\)/);
				spyReportContent[id] = {
					"title": resourceIdList[id],
					"value": parseInt(containerCells[i].innerText.replaceAll('.', '')),
				}
			}
		});
		// console.log(spyReportContent);
		return spyReportContent;
	}

	function spyReportParser(spyReportElement) {
		let spyReport = {};
		var spyReportHead = spyReportElement.querySelector(".spyRaportHead");
		let cords = spyReportHead.getAttribute('data-info').split(':');
		spyReport.head = {
							galaxy : cords[0],
							system : cords[1],
							planet : cords[2],
						};
		spyReport.content = containerParser(spyReportElement.querySelectorAll(".spyRaportContainer"));
		// Make sure all main resources are initialized
		[901, 902, 903].forEach((id) => {
			if (!(id in spyReport.content)) {
				spyReport.content[id] = { "title": resourceIdList[id], "value": 0 };
			}
		});
		return spyReport;
	}

	function getPlanetType(spyReportElement) {
		return spyReportElement.innerHTML.match(/game\.php.+?planettype=(?<planettype>[0-9]+)/).groups.planettype;
	}

	function sumUpResources(spyReport) {
		return spyReport.content[901].value + spyReport.content[902].value + spyReport.content[903].value;
	}

	function calculateNeededCapacity(metal, crystal, deuterium) {
		let capacity = 0;

		capacity = 0.5 * Math.max(metal + crystal + deuterium, Math.min(0.75 * (2 * metal + crystal + deuterium), 2 * metal + deuterium));

		return capacity;
	}

	function calculateResourceMarketValue(metal, crystal, deuterium) {
		let temp = Math.ceil(metal / marketRatios[901] + crystal / marketRatios[902] + deuterium / marketRatios[903])
		// if (isNaN(temp)) {
		// 	temp = "MARKTPLATZ"
		// }
		return temp;
	}

	function estimateTransporters(capacity) {
		return Math.ceil(capacity / 5000) + 1;
	}

	function estimateRecyclers(recycleValue) {
		return Math.ceil(recycleValue / 20000) + 1;
	}

	function determineDangerValue(spyReport) {
		let dangerValue = 0;
		Object.keys(dangersList).forEach(dangerKey => {
			let attackValue = dangersList[dangerKey];
			if (spyReport.content[dangerKey] !== undefined) {
				dangerValue += spyReport.content[dangerKey].value * attackValue;
			}
		});
		return dangerValue;
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

		// TODO fleetspeed = 1 ersetzen
		const fleetspeed = 1;
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
				summedUpRecycleValue += spyReport.content[recycleKey].value * singleRecycleValue;
			}
		});
		return summedUpRecycleValue;
	}

	function generateContainerCell(cellContent, additionalClasses = "") {
		let containerCell = document.createElement('div');
		containerCell.classList.add('spyRaportContainerCell');
		if (additionalClasses.length > 0) {
			containerCell.classList.add(additionalClasses);
		}
		containerCell.innerText = cellContent;

		return containerCell;
	}

	function generateContainerRow(cells) {
		let containerRow = document.createElement('div');
		containerRow.classList.add('spyRaportContainerRow');
		if (cells.length == 2) {
			containerRow.classList.add('doubleSizeCells');
		}
		containerRow.classList.add('clearfix');

		cells.forEach(cell => {
			containerRow.append(cell);
		});

		return containerRow;
	}

	function attachConclusionsToReport(spyReportElement, spyReport) {
		let conclusionReportHead = document.createElement('div');
		conclusionReportHead.classList.add('spyRaportContainerHead');
		conclusionReportHead.innerHTML = 'Zusammenfassung';


		// spyReportElement.getElementsByName("dangervalue")[0].innerText = spyReport.conclusions.dangerValue;
		// console.log(spyReport.conclusions.dangerValue);
		// console.log("foo");
		let reportContents = [
			// 0
			{
				"title": "Gesamte Ressourcen",
				"content": spyReport.conclusions.summedUpResources.toLocaleString('de-DE')
			},
			// 1
			{
				"title": "Gefahrenpotenzial",
				"content": spyReport.conclusions.dangerValue.toLocaleString('de-DE'),
				"additionalClasses": spyReport.conclusions.dangerValue > 0 ? "dangervalue" : ""
			},
			// 2
			{
				"title": "Potenziell zu erbeutende Ressourcen (ohne Recycling)",
				"content": Math.floor(spyReport.conclusions.summedUpResources / 2).toLocaleString('de-DE')
			},
			// 3
			{
				"title": "Recyclepotenzial",
				"content": spyReport.conclusions.recycleValue.toLocaleString('de-DE')
			},
			// 4
			{
				"title": "Notwendige Kleine Transporter",
				"content": spyReport.conclusions.transportersNeeded.toLocaleString('de-DE')
			},
			// 5
			{
				"title": "Notwendige Recycler",
				"content": spyReport.conclusions.recyclersNeeded.toLocaleString('de-DE')
			},
			// 6
			{
				"title": spyReport.conclusions.marketValue == "MARKTPLATZ" ? "Bitte Marktplatz öffnen" : `Marktwert (${marketRatios[901]}:${marketRatios[902]}:${marketRatios[903]})`,
				"content": spyReport.conclusions.marketValue == "MARKTPLATZ" ? "Bitte Marktplatz öffnen" : spyReport.conclusions.marketValue.toLocaleString('de-DE'),
				"additionalClasses": spyReport.conclusions.marketValue == "MARKTPLATZ" ? "dangervalue" : spyReport.conclusions.marketValue < 30000 ? "lowRess" : spyReport.conclusions.marketValue < 90000 ? "midRess" : spyReport.conclusions.marketValue < 150000 ? "highRess" : "realHighRess"
			},
			// 7
			{
				"title": spyReport.conclusions.MarketValuePerSecond.calculateResourceMarketValuePerSecond == "FORSCHUNG" ? "Bitte FORSCHUNG öffnen" : `Ressourcen pro Sekunde Flugzeit, bigger = better`,
				"content": spyReport.conclusions.MarketValuePerSecond.calculateResourceMarketValuePerSecond == "FORSCHUNG" ? "Bitte FORSCHUNG öffnen" : spyReport.conclusions.MarketValuePerSecond.calculateResourceMarketValuePerSecond.toLocaleString('de-DE').replace(/\./g, ','),
				"additionalClasses": spyReport.conclusions.MarketValuePerSecond.calculateResourceMarketValuePerSecond == "FORSCHUNG" ? "dangervalue" : spyReport.conclusions.MarketValuePerSecond.calculateResourceMarketValuePerSecond < 5 ? "lowRess" : spyReport.conclusions.MarketValuePerSecond.calculateResourceMarketValuePerSecond < 10 ? "midRess" : spyReport.conclusions.MarketValuePerSecond.calculateResourceMarketValuePerSecond < 20 ? "highRess" : "realHighRess"
			},
			// 8
			{
				"title": spyReport.conclusions.MarketValuePerSecond.calculateResourceMarketValuePerSecond == "FORSCHUNG" ? "Bitte FORSCHUNG öffnen" : `Best Ress / Sek`,
				"content": spyReport.conclusions.MarketValuePerSecond.calculateResourceMarketValuePerSecond == "FORSCHUNG" ? "Bitte FORSCHUNG öffnen" : spyReport.conclusions.MarketValuePerSecond.calculateResourceMarketValuePerSecondBestPlanet.toLocaleString('de-DE').replace(/\./g, ','),
				"additionalClasses": spyReport.conclusions.MarketValuePerSecond.calculateResourceMarketValuePerSecond == "FORSCHUNG" ? "dangervalue" : spyReport.conclusions.MarketValuePerSecond.calculateResourceMarketValuePerSecondBestPlanet < 5 ? "lowRess" : spyReport.conclusions.MarketValuePerSecond.calculateResourceMarketValuePerSecondBestPlanet < 10 ? "midRess" : spyReport.conclusions.MarketValuePerSecond.calculateResourceMarketValuePerSecondBestPlanet < 20 ? "highRess" : "realHighRess"
			},
			// 9
			{
				"title": spyReport.conclusions.MarketValuePerSecond.calculateResourceMarketValuePerSecond == "FORSCHUNG" ? "Bitte FORSCHUNG öffnen" : `Best Planet`,
				"content": spyReport.conclusions.MarketValuePerSecond.calculateResourceMarketValuePerSecond == "FORSCHUNG" ? "Bitte FORSCHUNG öffnen" : spyReport.conclusions.MarketValuePerSecond.galaJump == 1 ? spyReport.conclusions.MarketValuePerSecond.galaJumpPlanet : spyReport.conclusions.MarketValuePerSecond.bestPlanet[0] + ":" + spyReport.conclusions.MarketValuePerSecond.bestPlanet[1] + ":" + spyReport.conclusions.MarketValuePerSecond.bestPlanet[2],
				"additionalClasses": spyReport.conclusions.MarketValuePerSecond.calculateResourceMarketValuePerSecond == "FORSCHUNG" ? "dangervalue" : ""
			},
			// 10
			{
				"title": "Energie",
				"content": spyReport.conclusions.energy.toLocaleString('de-DE'),
				"additionalClasses": spyReport.conclusions.energy > 300000 ? "lowRess" : spyReport.conclusions.energy > 100000 ? "midRess" : ""
			},
		];

		let rows = [];

		rows[0] = generateContainerRow([
			//generateContainerCell(reportContents[0].title, reportContents[1].additionalClasses),          // Gesammte Ressourcen
			//generateContainerCell(reportContents[0].content, reportContents[1].additionalClasses),        // Gesammte Ressourcen
			//generateContainerCell(reportContents[2].title, reportContents[2].additionalClasses),          // Gefahrenpotenzial
			//generateContainerCell(reportContents[2].content, reportContents[2].additionalClasses),        // Gefahrenpotenzial
			generateContainerCell(reportContents[0].title, reportContents[0].additionalClasses),            // Gesammte Ressourcen
			generateContainerCell(reportContents[0].content, reportContents[0].additionalClasses),          // Gesammte Ressourcen
			generateContainerCell(reportContents[1].title, reportContents[1].additionalClasses),            // Gefahrenpotenzial
			generateContainerCell(reportContents[1].content, reportContents[1].additionalClasses),          // Gefahrenpotenzial
		]);

		rows[1] = generateContainerRow([
			generateContainerCell(reportContents[2].title, reportContents[2].additionalClasses),            // Potenzielle Ressourcen
			generateContainerCell(reportContents[2].content, reportContents[2].additionalClasses),          // Potenzielle Ressourcen
			generateContainerCell(reportContents[3].title, reportContents[3].additionalClasses),            // Recyclepotenzial
			generateContainerCell(reportContents[3].content, reportContents[3].additionalClasses),          // Recyclepotenzial
		]);

		rows[2] = generateContainerRow([
			generateContainerCell(reportContents[4].title, reportContents[4].additionalClasses),            // Transporter
			generateContainerCell(reportContents[4].content, reportContents[4].additionalClasses),          // Transporter
			generateContainerCell(reportContents[5].title, reportContents[5].additionalClasses),            // Recycler
			generateContainerCell(reportContents[5].content, reportContents[5].additionalClasses),          // Recycler
		]);

		rows[3] = generateContainerRow([
			generateContainerCell(reportContents[6].title, reportContents[6].additionalClasses),            // Marktwert
			generateContainerCell(reportContents[6].content, reportContents[6].additionalClasses),          // Marktwert
			generateContainerCell(reportContents[10].title, reportContents[10].additionalClasses),          // Energie
			generateContainerCell(reportContents[10].content, reportContents[10].additionalClasses),        // Energie
		]);

		rows[4] = generateContainerRow([
			generateContainerCell(reportContents[8].title, reportContents[8].additionalClasses),            // Best Ress / Sek
			generateContainerCell(reportContents[8].content, reportContents[8].additionalClasses),          // Best Ress / Sek
			generateContainerCell(reportContents[9].title, reportContents[9].additionalClasses),            // Best Planet
			generateContainerCell(reportContents[9].content, reportContents[9].additionalClasses),          // Best Planet
		]);

		rows[5] = generateContainerRow([
			generateContainerCell(reportContents[7].title, reportContents[7].additionalClasses),            // Ressourcen pro Sekunde Flugzeit
			generateContainerCell(reportContents[7].content, reportContents[7].additionalClasses),          // Ressourcen pro Sekunde Flugzeit
		]);

		let conclusionReport = document.createElement('div');
		conclusionReport.classList.add('spyRaportContainer');
		conclusionReport.appendChild(conclusionReportHead);
		rows.forEach(row => {
			conclusionReport.appendChild(row);
		});

		spyReportElement.querySelector('div.spyRaportHead').after(conclusionReport);

		let attackBox = document.createElement('div');

		let attackHrefTable = document.createElement("div");
		attackHrefTable.classList.add('spyRaportContainerRow', 'clearfix');

		// attackHrefTable.innerHTML = '<div class="spyRaportContainerCell nonedanger" style="width: 50% !important; text-align:center;"> <a href="game.php?page=fleetTable&amp;galaxy=1&amp;system=247&amp;planet=8&amp;planettype=1&amp;target_mission=1#ship_input[202]=8" target:"_blank"> <button type="button" style="text-align: center;">⚔️ 8 Kleine Transporter ⚔️</button> </a>'
		// attackHrefTable.innerHTML += '<div class="spyRaportContainerCell nonedanger" style="width: 50% !important; text-align:center;"> <a href="game.php?page=fleetTable&amp;galaxy=1&amp;system=247&amp;planet=8&amp;planettype=1&amp;target_mission=1#ship_input[203]=2" target:"_blank"> <button type="button" style="text-align: center;">⚔️ 2 Große Transporter ⚔️</button> </a>'

		attackBox.appendChild(attackHrefTable);
		conclusionReport.after(attackBox);
	}

	document.querySelectorAll(".spyRaport").forEach(spyReportElement => {
		let spyReport = spyReportParser(spyReportElement);

		var raidLocation = [spyReport.head.galaxy, spyReport.head.system, spyReport.head.planet];
		spyReport.conclusions = {};
		spyReport.conclusions.planetType = getPlanetType(spyReportElement);
		spyReport.conclusions.summedUpResources = sumUpResources(spyReport);
		spyReport.conclusions.neededCapacity = calculateNeededCapacity(
			spyReport.content[901].value,
			spyReport.content[902].value,
			spyReport.content[903].value
		);
		spyReport.conclusions.transportersNeeded = estimateTransporters(spyReport.conclusions.neededCapacity);
		spyReport.conclusions.dangerValue = determineDangerValue(spyReport);
		spyReport.conclusions.recycleValue = determineRecycleValue(spyReport);
		spyReport.conclusions.recyclersNeeded = estimateRecyclers(spyReport.conclusions.recycleValue);
		spyReport.conclusions.marketValue = calculateResourceMarketValue(
			spyReport.content[901].value,
			spyReport.content[902].value,
			spyReport.content[903].value
		);

		var energy;
		// try catch fpr energy on moon
		try {
			energy = spyReport.content[911].value;
		} catch (err) {
			energy = "It's a Moon!";
		}
		spyReport.conclusions.energy = energy;

		spyReport.conclusions.MarketValuePerSecond = raidTimeKT(spyReport.content[901].value, spyReport.content[902].value, spyReport.content[903].value, raidLocation);
		// console.log(spyReport);
		attachConclusionsToReport(spyReportElement, spyReport);

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
