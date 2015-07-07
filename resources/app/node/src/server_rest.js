var request = require('sync-request');

var rest = {};

rest.url = 'https://api.parse.com/1/classes/';
	
rest.send = function(method, object, object_id, params)
{
	console.log("trying to send request");
	if(!object_id)
		object_id = '';
	
	var url = this.url+object+'/'+object_id;
	
	try {
		var options = {
			'headers' : {
				'X-Parse-Application-Id': config.parse.app_id,
				'X-Parse-REST-API-Key': config.parse.rest_key
			}
		};
		
		var res = request(method, url, options);
		console.log(JSON.parse(res.body));
	} catch(e) {
		console.log(e);
	}
	return false;
};

rest.get = function(object, object_id)
{
	var user = new Array();
	user[1] = {'name': 'Kostadin Buglow', 'id': '1'};
	user[2] = {'name': 'Stefan', 'id': '2'};
	user[3] = {'name': 'Iwan', 'id': '3'};
	return user[object_id];
	return this.send('GET', object, object_id);
};

rest.del = function(object, object_id)
{
	return this.send('DELETE', object, object_id);
};

rest.post = function(object, params)
{
	return this.send('POST', object, '', params);
};

rest.put = function(object, object_id, params)
{
	return this.send('PUT', object, object_id, params);
};

module.exports = rest;