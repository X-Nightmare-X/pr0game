console.log('Start running pr0game tests');

var page = require('webpage').create();
var prefix = 'http://localhost:8000';


page.open(prefix, function(status) {
    console.log(status);
});