function submitinstall()
{
	$.getJSON('?mode=ajax&action=install&lang='+location.search.split('&')[2].substr('-2')+'&'+$('#install').serialize(), function(data) {
		alert(data.msg);
		if(!data.error)
			document.location.href = '?mode=ins&page=2&lang='+location.search.split('&')[2].substr('-2');
	});
	return false;
}