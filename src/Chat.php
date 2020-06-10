<?php
namespace MyApp;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $querystring = $conn->httpRequest->getUri()->getQuery();
        parse_str($querystring,$queryarray);

        $conn->resourceId = $queryarray['token'];
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
       
    }   

    public function onMessage(ConnectionInterface $from, $msg) {
        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

        $msg = json_decode($msg);

        //Faz uma busca em todos os clients
        foreach ($this->clients as $client) {
            if ($from !== $client) {
                // The sender is not the receiver, send to each client connected
                

                //Se mensagem.to id == clientID enviar mensagem
                if($msg->toid == $client->resourceId){
                    $client->send($msg->mensagem);
                }
               //   


               //$client->send($client->resourceId);
            }
        }


    }


    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}