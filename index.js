var http = require('http');
var express = require('express');
var app = express();
var fs = require('fs');
var connect = require('connect');
var session = require('express-session');
var gm = require('googlemaps');
var cookieParser = require('cookie-parser');
var cookieParser = cookieParser('your secret sauce')
  , sessionStore = new session.MemoryStore();
var session;
function extractFromAdress(components, type) {
  for (var i = 0; i < components.length; i++)
  for (var j = 0; j < components[i].types.length; j++)
  if (components[i].types[j] == type) return components[i].long_name;
  return "";
}
app.use(cookieParser)
.use(session({secret: 'zetezt', store: sessionStore }))
.get('/', function(req, res) {
	session = req.session;
    res.render('index.ejs', {});
})
.get('', function(req, res) {
	session = req.session;
    res.render('index.ejs', {});
})
.get('/itineraire', function(req, res) {
	session = req.session;
	if(session.deVille && session.aVille) {
		res.render('itineraire.ejs', {session: session});
	} else {
		res.status(404);
    	res.render('404.ejs', { url: req.url });
	}
})
.get('/:dir/:file', function(req, res) {
    var dir = req.params.dir;
    if(dir == 'js' || dir == 'css') {
	    fs.readFile('./'+req.params.dir+'/'+req.params.file+'', function read(err, data) {
		    if (err) {
		        res.status(404);
    			res.render('404.ejs', { url: req.url });
		    } else {
		    	res.end(data);
		    }
		});
	} else {
    	res.status(404);
    	res.render('404.ejs', { url: req.url });
	}
})
.use(function(req, res, next){
    res.status(404);
    res.render('404.ejs', { url: req.url });
});
var server = http.createServer(app).listen(8080, function(){
  console.log("Express server listening on port 8080");
});
var io = require('socket.io').listen(server);
io.on('connection', function (socket) {
    socket.on('loadItineraire', function (post) {
    	var i = 0;
    	var de = post['de']+'';
    	var a = post['a']+'';
    	var patt = /\([-0-9\.]+, [-0-9\.]+\)/;
    	if(patt.test(a) && patt.test(de)) {
    		de = de.replace('(', '');
    		de = de.replace(')', '');
    		de = de.replace(' ', '');
    		de = de.split(',');
    		gm.reverseGeocode(de[0]+','+de[1], function(err, data){
    		  if(data['status'] == 'OK') {
    		  	if(data['results'][0]) {
    		  		session.deVille = extractFromAdress(data['results'][0].address_components, "locality");
					session.de = de;
					session.save();
					i++;
					if(i == 2) socket.emit('succes');
    		  	} else {
	    		  	socket.emit('erreur', {titre:'Ooops...', content:'Erreur système, veuillez réessayer'});
	    		}
    		  } else {
    		  	socket.emit('erreur', {titre:'Ooops...', content:'Erreur système, veuillez réessayer'});
    		  }
			});
    		a = a.replace('(', '');
    		a = a.replace(')', '');
    		a = a.replace(' ', '');
    		a = a.split(',');
    		gm.reverseGeocode(a[0]+','+a[1], function(err, data){
			  if(data['status'] == 'OK') {
    		  	if(data['results'][0]) {
    		  		session.aVille = extractFromAdress(data['results'][0].address_components, "locality");
					session.a = a;
					session.save();
					i++;
					if(i == 2) socket.emit('succes');
    		  	} else {
	    		  	socket.emit('erreur', {titre:'Ooops...', content:'Erreur système, veuillez réessayer'});
	    		}
    		  } else {
    		  	socket.emit('erreur', {titre:'Ooops...', content:'Erreur système, veuillez réessayer'});
    		  }
			});
		} else {
        	socket.emit('erreur', {titre:'Ooops...', content:'Erreur système, veuillez réessayer'});
		}
    });
});