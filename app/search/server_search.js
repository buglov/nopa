'use strict';
global.http = require('http'); 
var server = http.createServer();
global.config = require('./config/node_conf.js')();
global.io = require('socket.io')(server);
global.main = require('./src/server_main')();

var port = 3001;

/* Start server */
server.listen(port, function() {
	main.server_listen(port);
});

/* Handle server errors */
server.on('error', function(err) {
	main.server_error(err);
});

/* Socket connection */
io.on('connection', function(socket) 
{	
	try 
	{		
		/* SOCKET FUNCTIONS */

		/* Handle connected sockets */
		main.socket_connect(socket);

		/* Handle disconnected sockets */		
		socket.on('disconnect', function() {
			main.socket_disconnect(socket);
		});

		/* Handle socket errors */
		socket.on('error', function(err) {
			main.socket_error(socket, err);
		});
	} 
	catch (e) 
	{
		dumpError(e);
	}
});


function log(err) 
{
	if (typeof err === 'object') 
	{
		if (err.message) 
		{
			console.log('Message: ' + err.message)
		}
		
		if (err.stack) 
		{
			console.log('Stacktrace:')
			console.log('====================')
			console.log(err.stack);
		}
	} 
	else 
	{
		console.log(err);
	}
}