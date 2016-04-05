var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);

var bodyParser = require('body-parser');
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: false }));


app.get('/', function(req, res){
    res.send('hai..');
});

app.post('/notify', function(req, res) {   
    var message = req.body.msg;
    var ch = req.body.ch;
    console.log("Broadcasting to channel "+ ch +": " + message);
    io.sockets.emit(ch,   message);

    res.end();
});

io.on('connection', function(socket){
  //console.log('a user connected');
});

http.listen(8081, function(){
  //console.log('listening on *:8081');
});
