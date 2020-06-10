

// PRE SET USERID
var conn ;


function openSocket(){
    conn = new WebSocket('ws://localhost:8080?token='+userid);

    conn.onopen = function(e) {
        console.log("Connection established!");
    };

    conn.onmessage = function(e) {
        console.log(e.data);
    };

}






function mensagem( to_id ,msg ){
  var mensagem = JSON.stringify({toid:to_id,mensagem:msg});

  console.log(mensagem);

  conn.send(mensagem);

}