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
		$this -> HP = $info["HP"];
		$this -> MinDMG = $info["MinDMG"];
		$this -> MaxDMG = $info["MaxDMG"];
		$this -> CritChance = $info["CritChance"];
		$this -> Defense = $info["Defense"];
		$this -> Dodge = $info["Dodge"];
		$this -> AttackSpeed = $info["AttackSpeed"];
		$this -> PlayerID = $info["PlayerID"];
	}
	
	public function attack($player) {
		// Pick a random value for player damage from his damage range
		$this->DMG = mt_rand($this->MinDMG, $this->MaxDMG);
		
		// Store his damage in a var to avoid overwriting it by modifiers
		$damage = $this->DMG;

		// As first, try to dodge an attack
		$dodge=mt_rand(0,100);
		
		// Attack is dodged, if the selected random value is lower than player Dodge chance
		// Otherwise, do the proper stuff
		if($dodge>$player->Dodge) {
			// Player was not taht lucky to dodge an attack
			// We have to calculate the damage of current player
			
			// Bro, DO YOU EVEN CRIT?
			$crit = mt_rand(0,100);

			// Damage will be multiplied if the selected random value is lower than player Critical strike chance
			// Otherwise, do nothing. Normal damage.
			if($crit<=$this->CritChance) {
				// Critical strike !!multiplies!! the damage by 150%.
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
			
			// Decrease the player damage
			$damage -= $penalty;
			
			// We can remove decimals since we are working on higher values, so we don't need precision
			$damage=number_format($damage,0);
			
			// Decrease enemy Hit Points
			$player-> HP -= $damage;
			
			// OPTIONAL ECHO showing the remaining Hit Points
			echo $damage." damage.<br>Player ".$this->PlayerID." HP: ".$this->HP." | Player ".$player->PlayerID." HP: ".$player->HP."<br><br>";
		} else {
			// Player dodged an attack, so we can inform somehow about it
			// or just do nothing
			echo "Player ".$player->PlayerID." dodged an attack.<br><br>";
		}
	}
	
	// Method inserting player turns based on their attack speed
	public function roll_turns ($time_limit, $player_turns) {
		// Each player has defined an Attack speed, which will help with this
		$turn = $this->AttackSpeed;
		
		for ($i=1;$i<=$time_limit;$i++) {
			// If the actual time is matched with the attack speed, insert it into the array
			if ($i == $turn) {
				// This is the Turn-conflict solving problem
				// If no player takes his turn at the ckecked time, just insert his turn. Otherwise we will call the Draw() function
				// ... so both players can take their turns at the same time.
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