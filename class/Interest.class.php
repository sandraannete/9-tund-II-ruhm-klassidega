<?php 
class Interest {

	private $connection;
	public $name;

	function __construct($mysqli){

		//"this" viitab klassile, this on user
		$this->connection = $mysqli;
	

	}

function saveInterest ($interest) {
		
		$database = "if16_sandra_2";
		$this->connection = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $database);

		$stmt = $this->connection->prepare("INSERT INTO interests (interest) VALUES (?)");
	
		echo $this->connection->error;
		
		$stmt->bind_param("s", $interest);
		
		if($stmt->execute()) {
			echo "salvestamine õnnestus";
		} else {
		 	echo "ERROR ".$stmt->error;
		}
		
		$stmt->close();
		$this->connection->close();
		
	}

function saveUserInterest ($interest) {
	
	$database = "if16_sandra_2";
	$this->connection = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $database);

	$stmt = $this->connection->prepare("
		SELECT id FROM user_interests 
		WHERE user_id=? AND interest_id=?
	");
	$stmt->bind_param("ii", $_SESSION["userId"], $interest);
	$stmt->bind_result($id);
	
	$stmt->execute();
	
	if ($stmt->fetch()) {
		// oli olemas juba selline rida
		echo "juba olemas";
		// pärast returni midagi edasi ei tehta funktsioonis
		return;
		
	} 
	
	$stmt->close();
	
	// kui ei olnud siis sisestan
	
	$stmt = $this->connection->prepare("
		INSERT INTO user_interests
		(user_id, interest_id) VALUES (?, ?)
	");
	
	echo $this->connection->error;
	
	$stmt->bind_param("ii", $_SESSION["userId"], $interest);
	
	if ($stmt->execute()) {
		echo "salvestamine õnnestus";
	} else {
		echo "ERROR ".$stmt->error;
	}
	
}
	
	
function getAllInterests() {
		
		$database = "if16_sandra_2";
		$this->connection = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $database);
		
		$stmt = $this->connection->prepare("
			SELECT id, interest
			FROM interests
		");
		echo $this->connection->error;
		
		$stmt->bind_result($id, $interest);
		$stmt->execute();
		
		
		//tekitan massiivi
		$result = array();
		
		// tee seda seni, kuni on rida andmeid
		// mis vastab select lausele
		while ($stmt->fetch()) {
			
			//tekitan objekti
			$i = new StdClass();
			
			$i->id = $id;
			$i->interest = $interest;
		
			array_push($result, $i);
		}
		
		$stmt->close();
		$this->connection->close();
		
		return $result;
	}
		
	function getAllUserInterests() {
		
		$database = "if16_sandra_2";
		$this->connection = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $database);
		
		$stmt = $this->connection->prepare("
			SELECT interest FROM interests
			JOIN user_interests 
			ON interests.id=user_interests.interest_id
			WHERE user_interests.user_id = ?
		");

		$stmt->bind_param ("i", $_SESSION["userId"]);
		echo $this->connection->error;
		
		$stmt->bind_result($interest);
		$stmt->execute();
		
		
		//tekitan massiivi
		$result = array();
		
		// tee seda seni, kuni on rida andmeid
		// mis vastab select lausele
		while ($stmt->fetch()) {
			
			//tekitan objekti
			$i = new StdClass();
			
		
			$i->interest = $interest;
		
			array_push($result, $i);
		}
		
		$stmt->close();
		$this->connection->close();
		
		return $result;
	}		
}
?>	