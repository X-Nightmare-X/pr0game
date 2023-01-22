var start = new Date().getTime() / 1000;

function Refresh() {
	var now = new Date().getTime() / 1000;
	$("[data-time]").each( function () {
		var d = $(this).attr('data-time');
		$(this).text(getRestTimeFormat(d-(now-start)));
	});
}

function convertToMetal(crystal, deuterium) {
	var crystalValue = crystal * $('input[name=ratio-mc').val();
	var deutValue = deuterium * $('input[name=ratio-md').val();
	return crystalValue + deutValue;
}

function convertToCrystal(metal, deuterium) {
	var metalValue = metal / $('input[name=ratio-mc').val();
	var deutValue = deuterium * $('input[name=ratio-cd').val();
	return metalValue + deutValue;
}

function convertToDeut(metal, crystal) {
	var metalValue = metal / $('input[name=ratio-md]').val();
	var crystalValue = crystal / $('input[name=ratio-cd]').val();
	console.log(metalValue);
	return metalValue + crystalValue;
}

function getCostType(tradeOffer) {
	if (tradeOffer.find('.wanted-resource-1').length > 0) return 'metal';
	if (tradeOffer.find('.wanted-resource-2').length > 0) return 'crystal';
	if (tradeOffer.find('.wanted-resource-3').length > 0) return 'deut';
}

function getOfferValue(tradeOffer) {
	var metal = parseInt(tradeOffer.find('.resource_metal').html().replace(/\./g,''));
	var crystal = parseInt(tradeOffer.find('.resource_crystal').html().replace(/\./g,''));
	var deut = parseInt(tradeOffer.find('.resource_deuterium').html().replace(/\./g,''));

	var offerValue = 0;
	var costType = getCostType(tradeOffer);
	var costValue = parseInt(tradeOffer.find('.wanted-resource-amount').html().replace(/\./g,''));
	switch (costType) {
		case 'metal':
			offerValue = metal + convertToMetal(crystal,deut);
			break;
		case 'crystal':
			offerValue = crystal + convertToCrystal(metal,deut);
			break;
		case 'deut':
			offerValue = deut + convertToDeut(metal,crystal);
	}
	
	return offerValue;
}

function calculateRatios(){
	/*
	 * Thanks to zb0oj for idea and a part of source code!
	 */
	var referenceRatios = {
			'metal': $('input[name=ratio-metal]').val(),
			'cristal': $('input[name=ratio-cristal]').val(),
			'deuterium': $('input[name=ratio-deuterium]').val()
	};
	$('table#tradeList tbody tr').not('.no-background.no-border.center').each(function() {
			var tradeOffer = $(this);
			var offer = {
				'metal': parseInt(tradeOffer.find('.resource_metal').html().replace(/\./g,'')),
				'cristal': parseInt(tradeOffer.find('.resource_crystal').html().replace(/\./g,'')),
				'deuterium': parseInt(tradeOffer.find('.resource_deuterium').html().replace(/\./g,'')),
			};

			var cost = {
				'isMetal': (tradeOffer.find('.wanted-resource-1').length > 0),
				'isCristal': (tradeOffer.find('.wanted-resource-2').length > 0),
				'isDeuterium': (tradeOffer.find('.wanted-resource-3').length > 0),
				'wantedAmount': parseInt(tradeOffer.find('.wanted-resource-amount').html().replace(/\./g,'')),
			};

			var offerValue = getOfferValue(tradeOffer);
			var ratio = offerValue / cost.wantedAmount;
			
			tradeOffer.find('.total_value').text(offerValue.toFixed(0));
			var n = tradeOffer.find('.ratio').text(ratio.toFixed(2));
			if(ratio < 1) {
				n.css({'color': '#F00'});
			} else {
				n.css({'color': '#0F0'});
			}
		});
}

$(document).ready(function() {
	interval	= window.setInterval(Refresh, 1000);
	Refresh();

	$('input[name=ratio-metal], input[name=ratio-cristal], input[name=ratio-deuterium]').change(function(e){
		calculateRatios();
	});
	calculateRatios();
});
