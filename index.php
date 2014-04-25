<?php
require("players.php");
// This is a pseudo-draw function that allows both players attack themselves
// If we had a conflict in the array
function Draw($P1,$P2) {
	// Even if a player was defeated, we allow him to counter attack, 
	// because both players have their turns.
	$P1->attack($P2);
	$P2->attack($P1);
}

// Let's say we get some information from the Database about players
$P1info = array(
	"HP" => 5000,
	"MinDMG" => 568,
	"MaxDMG" => 908,
	"CritChance" => 25,
	"Defense" => 3874,
	"Dodge" => 10,
	"AttackSpeed" => 15,
	"PlayerID" => 1
); 

$P2info = array(
	"HP" => 5000,
	"MinDMG" => 808,
	"MaxDMG" => 1380,
	"CritChance" => 20,
	"Defense" => 4295,
	"Dodge" => 10,
	"AttackSpeed" => 25,
	"PlayerID" => 2
); 
	
$player2 = new Player($P2info);
$player1 = new Player($P1info);

// Now it's time to place into an array some stuff
// We also have to define max time for a fight.
// Let's say it will be like... 600 seconds.
$time_limit = 600;
$player_turns = array();

// Inserting Player1 turns into our Turns array
$player_turns = $player1->roll_turns($time_limit, $player_turns);

// Now if we have Player1 turns generated, let's make the same for Player2
// We have to pass the same array to the method
$player_turns = $player2->roll_turns($time_limit, $player_turns);

// We have to sort our array keys, because Player2 turns were added
// ad the end of an array
ksort($player_turns);

// Let's set our fight conditions: 1: Time up; 2: Defeat;
$condition="";

// Now we have to loop through our array. We will use switch() for methods
for($i=1;$i<=$time_limit;$i++) {
	if(!empty($player_turns[$i])){
		switch($i) {
			case $player_turns[$i]==$player1->PlayerID: $player1->attack($player2); break;
			case $player_turns[$i]==$player2->PlayerID: $player2->attack($player1); break;
			case $player_turns[$i]=="Draw": Draw($player1, $player2); break;
		}
		if( ($player1->HP <=0) or ($player2->HP <=0)) {
		// Remove negative numbers
		if($player1->HP <0)
			$player1->HP=0;
		
		if($player2->HP <0)
			$player2->HP=0;
		break;
	}
	}
	
	
}


if($player1->HP <=0 and $player2->HP<=0)
	echo "Draw!";
elseif($player2->HP<=0 and $player1->HP>=0)
	echo "Player 1 wins!";
elseif($player1->HP<=0 and $player2->HP>=0)
	echo "Player 2 wins!";
exit;