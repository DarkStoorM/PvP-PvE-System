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