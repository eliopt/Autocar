var Graph = (function (undefined) {

	var extractKeys = function (obj) {
		var keys = [], key;
		for (key in obj) {
		    Object.prototype.hasOwnProperty.call(obj,key) && keys.push(key);
		}
		return keys;
	}

	var sorter = function (a, b) {
		return parseFloat (a) - parseFloat (b);
	}

	var findPaths = function (map, start, end, infinity) {
		infinity = infinity || Infinity;

		var costs = {},
		    open = {'0': [start]},
		    predecessors = {},
		    keys;

		var addToOpen = function (cost, vertex) {
			var key = "" + cost;
			if (!open[key]) open[key] = [];
			open[key].push(vertex);
		}

		costs[start] = 0;

		while (open) {
			if(!(keys = extractKeys(open)).length) break;

			keys.sort(sorter);

			var key = keys[0],
			    bucket = open[key],
			    node = bucket.shift(),
			    currentCost = parseFloat(key),
			    adjacentNodes = map[node] || {};

			if (!bucket.length) delete open[key];

			for (var vertex in adjacentNodes) {
			    if (Object.prototype.hasOwnProperty.call(adjacentNodes, vertex)) {
					var cost = adjacentNodes[vertex],
					    totalCost = cost + currentCost,
					    vertexCost = costs[vertex];

					if ((vertexCost === undefined) || (vertexCost > totalCost)) {
						costs[vertex] = totalCost;
						addToOpen(totalCost, vertex);
						predecessors[vertex] = node;
					}
				}
			}
		}

		if (costs[end] === undefined) {
			return null;
		} else {
			return predecessors;
		}

	}

	var extractShortest = function (predecessors, end) {
		var nodes = [],
		    u = end;

		while (u) {
			nodes.push(u);
			predecessor = predecessors[u];
			u = predecessors[u];
		}

		nodes.reverse();
		return nodes;
	}

	var findShortestPath = function (map, nodes) {
		var start = nodes.shift(),
		    end,
		    predecessors,
		    path = [],
		    shortest;

		while (nodes.length) {
			end = nodes.shift();
			predecessors = findPaths(map, start, end);

			if (predecessors) {
				shortest = extractShortest(predecessors, end);
				if (nodes.length) {
					path.push.apply(path, shortest.slice(0, -1));
				} else {
					return path.concat(shortest);
				}
			} else {
				return null;
			}

			start = end;
		}
	}

	var toArray = function (list, offset) {
		try {
			return Array.prototype.slice.call(list, offset);
		} catch (e) {
			var a = [];
			for (var i = offset || 0, l = list.length; i < l; ++i) {
				a.push(list[i]);
			}
			return a;
		}
	}

	var Graph = function (map) {
		this.map = map;
	}

	Graph.prototype.findShortestPath = function (start, end) {
		if (Object.prototype.toString.call(start) === '[object Array]') {
			return findShortestPath(this.map, start);
		} else if (arguments.length === 2) {
			return findShortestPath(this.map, [start, end]);
		} else {
			return findShortestPath(this.map, toArray(arguments));
		}
	}

	Graph.findShortestPath = function (map, start, end) {
		if (Object.prototype.toString.call(start) === '[object Array]') {
			return findShortestPath(map, start);
		} else if (arguments.length === 3) {
			return findShortestPath(map, [start, end]);
		} else {
			return findShortestPath(map, toArray(arguments, 1));
		}
	}

	return Graph;

})();
function distance(lat_a, lon_a, lat_b, lon_b)  { a = Math.PI / 180; lat1 = lat_a * a; lat2 = lat_b * a; lon1 = lon_a * a; lon2 = lon_b * a;  t1 = Math.sin(lat1) * Math.sin(lat2); t2 = Math.cos(lat1) * Math.cos(lat2); t3 = Math.cos(lon1 - lon2); t4 = t2 * t3; t5 = t1 + t4; rad_dist = Math.atan(-t5/Math.sqrt(-t5 * t5 +1)) + 2 * Math.atan(1);  return (rad_dist * 3437.74677 * 1.1508) * 1.6093470878864446; }
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
var mysql = require('mysql');
function extractFromAdress(components, type) {
  for (var i = 0; i < components.length; i++)
  for (var j = 0; j < components[i].types.length; j++)
  if (components[i].types[j] == type) return components[i].long_name;
  return "";
}
function getBestItineraire(itineraire, socket) {
	socket.emit('succes', {itineraire:itineraire});
}
function check(post) {
	var returnVar = new Array();
	if(session.a && session.de) {
		if(typeof post['departDate'] != 'undefined' && typeof post['retourDate'] != 'undefined' && typeof post['adultes'] != 'undefined' && typeof post['enfants'] != 'undefined') {
			if(!isNaN(post['adultes']) && !isNaN(post['enfants'])) {
				if(post['adultes']+post['enfants'] > 0) {
					//returnVar = array();
					returnVar['adultes'] = post['adultes'];
					returnVar['enfants'] = post['enfants'];
					returnVar['de'] = session.de;
					returnVar['a'] = session.a;
					if(departDate = Date.parse(post['departDate'])) {
						returnVar['departDate'] = departDate;
						if(post['retourDate']) {
							if(retourDate = Date.parse(post['retourDate'])) {
								returnVar['retourDate'] = retourDate;
								return returnVar;
							} else {
								return 'Erreur, la date de retour est invalide';
							}
						} else {
							returnVar['retourDate'] = '';
							return returnVar;
						}
					} else {
						return 'Erreur, la date de départ est invalide';
					}
				} else {
					return 'Erreur, vous devez prendre au moins une place !';
				}
			} else {
				return 'Erreur, entrez un nombre pour choisir le nombre de places !';
			}
		} else {
			return 'Erreur système !';
		}
	} else {
		return 'Erreur système !';
	}
}
app.use(cookieParser)
.use(session({secret: 'zetezt', store: sessionStore, resave:true, saveUninitialized:true}))
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
	socket.on('computeItineraire', function (post) {
		var checkVar = check(post);
		if(typeof checkVar === 'string') {
			socket.emit('erreur', {titre:'Ooops...', content:checkVar});
		} else {
			var connection = mysql.createConnection({
			  host     : 'localhost',
			  user     : 'emmanuelcoppey',
			  password : 'azerty',
			  port     : 8889,
			  database : 'centrocar'
			});
			connection.query('SELECT * FROM arrets WHERE get_distance_metres("'+checkVar['de'][0]+'", "'+checkVar['de'][1]+'", lat, lng) < 20000 ORDER BY get_distance_metres("'+checkVar['de'][0]+'", "'+checkVar['de'][1]+'", lat, lng) ASC LIMIT 20', function(errDe, rowsDe, fieldsDe) {
			  if(errDe || typeof rowsDe[0] == 'undefined') {
			  	socket.emit('erreur', {titre:'Ooops...', content:'Aucun itinéraire n\'a été trouvé.'});
			  } else {
			  	connection.query('SELECT * FROM arrets WHERE get_distance_metres("'+checkVar['a'][0]+'", "'+checkVar['a'][1]+'", lat, lng) < 20000 ORDER BY get_distance_metres("'+checkVar['a'][0]+'", "'+checkVar['a'][1]+'", lat, lng) ASC LIMIT 20', function(errA, rowsA, fieldsA) {
				  if(errA || typeof rowsA[0] == 'undefined') {
				  	socket.emit('erreur', {titre:'Ooops...', content:'Aucun itinéraire n\'a été trouvé.'});
				  } else {
				  	console.log('Il y a des arrets a proximité.');
				  	var i = 0;
				  	var itineraire = [];
				  	for(var keyDe in rowsDe) {
				  		var nbrDe = rowsDe.length;
				  		for(var keyA in rowsA) {
				  			i++;
				  			var nbrA = rowsA.length;
				  			var totalCount = nbrA * nbrDe;
				  			if(i == totalCount) getBestItineraire(itineraire, socket);
						  	connection.query('SELECT * FROM arrets ORDER BY ligneId ASC,idArret ASC', function(errArret, rowsArret, fieldsArret) {
						  		if(errArret || typeof rowsArret[0] == 'undefined') {
						  			socket.emit('erreur', {titre:'Ooops...', content:'Aucun itinéraire n\'a été trouvé.'});
						  		}
						  		else {
							  		var map = [];
							  		var last;
							  		for(var key in rowsArret){
							  			var current = rowsArret[key];
							  		    if(last) {
							  		    	if(last.ligneId == current.ligneId) {
							  		    		map[last.id] = [];
							  		    		map[current.id] = [];
							  		    		map[last.id][current.id] = distance(last.lat, last.lng, current.lat, current.lng)*1000;
							  		    		map[current.id][last.id] = distance(last.lat, last.lng, current.lat, current.lng)*1000;
							  		    	}
							  		    }
							  		    last = rowsArret[key];
									}
									map = JSON.parse(JSON.stringify(map));
								  	//var map = {a:{b:3,c:1},b:{a:2,c:1},c:{a:4,b:1}},
									var graph = new Graph(map);
									itineraire.push(graph.findShortestPath(''+rowsDe[keyDe].id+'', ''+rowsA[keyA].id+''));
									console.log(graph.findShortestPath(''+rowsDe[keyDe].id+'', ''+rowsA[keyA].id+''));      // => ['a', 'c', 'b']
									/*graph.findShortestPath('a', 'c');      // => ['a', 'c']
									graph.findShortestPath('b', 'a');      // => ['b', 'a']
									graph.findShortestPath('b', 'c', 'b'); // => ['b', 'c', 'b']
									graph.findShortestPath('c', 'a', 'b'); // => ['c', 'b', 'a', 'c', 'b']
									graph.findShortestPath('c', 'b', 'a'); // => ['c', 'b', 'a']*/
								}
						  		if(errArret) console.log(errArret);
						  	});
						}
					}
				  }
				  if(errA) console.log(errA);
				});
			  }
			  if(errDe) console.log(errDe);
			});
			//connection.end();
			socket.emit('succes', {itineraire:JSON.stringify(checkVar)});
		}
	});
});