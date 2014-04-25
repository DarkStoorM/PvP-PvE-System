<?php
class Player {
	public $HP = null;
	public $DMG = null;
	public $MinDMG = null;
	public $MaxDMG = null;
	public $CritChance = null;
	public $Defense = null;
	public $Dodge = null;
	public $AttackSpeed = null;
	public $PlayerID = null;
	
	public function __construct($info) {
		srand($this->make_seed());
		
		$this -> HP = $info["HP"];
		$this -> MinDMG = $info["MinDMG"];
		$this -> MaxDMG = $info["MaxDMG"];
		$this -> CritChance = $info["CritChance"];
		$this -> Defense = $info["Defense"];
		$this -> Dodge = $info["Dodge"];
		$this -> AttackSpeed = $info["AttackSpeed"];
		$this -> PlayerID = $info["PlayerID"];
	}
	
	public function make_seed() {
	  list($usec, $sec) = explode(' ', microtime());
	  return (float) $sec + ((float) $usec * 100000);
	}
	
	public function attack($player) {
		$this->DMG = mt_rand($this->MinDMG, $this->MaxDMG);
		$damage = $this->DMG;

		// As first, try to dodge an attack
		$dodge=mt_rand(0,100);
		if($dodge>$player->Dodge) {
			// Player was not taht lucky to dodge an attack
			// We have to calculate the damage of current player
			
			// Bro, DO YOU EVEN CRIT?
			$crit = mt_rand(0,100);

			if($crit<=$this->CritChance) {
				// Critical strike multiplies the damage by 150%.
				// Default value for every game I guess
				$damage *= 1.5;
				
				// OPTIONAL ECHO showing if we dealt a critical strike
				echo "Player ".$this->PlayerID." strikes with critical hit dealing ";
			} else {
				// OPTIONAL ECHO showing information about damage dealt
				echo "Player ".$this->PlayerID." deals ";
			}
			
			// Now we have to decrease the damage because of the Defense
			// 1 DEF point decreases damage by 0.0125%
			$penalty = round(($damage * ($player->Defense * 0.0125))/100);
			
			$damage -= $penalty;
			
			// We can remove decimals since we are working on higher values, so we don't need precision
			$damage=number_format($damage,0);
			
			$player-> HP -= $damage;
			
			// OPTIONAL ECHO showing the remaining Hit Points
			echo $damage." damage.<br>Player ".$this->PlayerID." HP: ".$this->HP." | Player ".$player->PlayerID." HP: ".$player->HP."<br><br>";
		} else {
			// Player dodged an attack, so we can inform somehow about it
			// or just do nothing
			echo "Player ".$player->PlayerID." dodged an attack.<br><br>";
		}
	}
	
	public function roll_turns ($time_limit, $player_turns) {
		// Each player has defined an Attack speed, which will help with this
		$turn = $this->AttackSpeed;
		
		for ($i=1;$i<=$time_limit;$i++) {
			if ($i == $turn) {
				if(empty($player_turns[$i]))
					$player_turns[$i] = $this->PlayerID;
				else
					$player_turns[$i] = "Draw";
				
				// Calculating the next turn
				$turn += $this->AttackSpeed;
			}
		}
		
		return $player_turns;
	}
}

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