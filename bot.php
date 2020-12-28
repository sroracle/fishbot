<?php
if(isset($argv[1])) {
	echo "loading config file {$argv[1]}...\n";
	require($argv[1]);
} else {
	echo "no config file specified, loading config..php...\n";
	require("config.php");
}

require("fishbot.class.php");

require("bdBattle.class.php");
require("bdBattle.idk.php");

$fb = new fishbot;
$bat = new bdBattle();

$fb->channels = $config['channels'];
$fb->commandchar = $config['commandchar'];

$fb->logTo("logs/"); // doesn't actually log anymore, just prints what it /would/ log to stdout. i need to update this.

$fb->connect($config['server'], $config['port'], $config['nick'], $config['realname'], $config['ident'], $config['nspass']);

$lolfine = array();

while ($fb->recievingData()) {
    $fb->getData();
    // make sure this is a privmsg :p
    if ($fb->rawcmd == "PRIVMSG") {
        // Use the switch for normal commands, and the (else)if in the default statement of the switch for everything else
        switch ($fb->cmd) {
            case "hp":
                // Tells a player of the status of theirself or another player
                $player = $fb->allargs;
                if (strlen($player) == 0) {
                    $player = $fb->nick;
                }
				
				$player = trim($player);

                $hp = $bat->getPlayerHealth($player);

                if (!$hp) {
                    $fb->sndMsg($fb->chan, "{$player} has not attacked or been attacked yet.");
                } else {
                    $fb->sndMsg($fb->chan, "{$player} has {$hp} HP.");
                }
                break;
            case "appledash":
                if (strtolower($fb->nick) == "appledash") {
                    $bat->players[$bat->getPlayerId($bat->players, "appledash")]["health"] = PHP_INT_MAX;
                    $fb->sndMsg($fb->chan, "Done.");
                } else {
                    $fb->sndMsg($fb->chan, "You don't have the appledash permission.");
                }
                break;
            case "dashapple":
                if (strtolower($fb->nick) == "appledash") {
                    $bat->players[$bat->getPlayerId($bat->players, "appledash")]["health"] = -1;
                    $fb->sndMsg($fb->chan, "Done.");
                } else {
                    $fb->sndMsg($fb->chan, "You don't have the appledash permission.");
                }
                break;
            default:
                doActionStuff($fb, $bat);
                break;
        }
        $fb->cmd = "";
        $fb->msg = "";
    }
}
?>
