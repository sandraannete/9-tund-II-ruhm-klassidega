<?php
class Car {

	private $connection;

	function __construct($mysqli){

		//"this" viitab klassile, this on user
		$this->connection = $mysqli;
	}
	
function get($q, $sort, $direction) {

		//mis sort ja järjekord
		$allowedSortOptions = ["id", "plate", "color"];

		//kas sort on lubatud valikute sees
		if(!in_array($sort, $allowedSortOptions)){

		$sort = "id";
	}
	echo "Sorteerin".$sort." ";
	$orderBy = "ASC";

	if($direction == "descending"){

	$oderBy="DESC";
	}
	echo "järjekord: ".$orderBy." ";

		if($q == ""){
		
			echo "ei otsi";
			
			$stmt = $this->connection->prepare("
				SELECT id, plate, color
				FROM cars_and_colors
				WHERE deleted IS NULL
			");
		
		}else{
			
			echo "Otsib: ".$q;
			
			//teen otsisõna
			// lisan mõlemale poole %
			$searchword = "%".$q."%";
			
			$stmt = $this->connection->prepare("
				SELECT id, plate, color
				FROM cars_and_colors
				WHERE deleted IS NULL AND
				(plate LIKE ? OR color LIKE ?)
				ORDER BY $sort $orderBy
			");
			$stmt->bind_param("ss", $searchword, $searchword);
		
		}
		




	}

	}
function saveCar ($plate, $color) {
		
		$stmt = $this->connection->prepare("INSERT INTO cars_and_colors (plate, color) VALUES (?, ?)");
		
		echo $this->connection->error;
		
		$stmt->bind_param("ss", $plate, $color);
		
		if($stmt->execute()) {
			echo "salvestamine õnnestus";
		} else {
		 	echo "ERROR ".$stmt->error;
		}
		$stmt->close();
	}
	function getAllCars() {
		
		$stmt = $this->connection->prepare("SELECT id, plate, color FROM cars_and_colors");
		echo $this->connection->error;
		$stmt->bind_result($id, $plate, $color);
		$stmt->execute();
		
		//tekitan massiivi
		$result = array();
		
		//while tingimus-tee seda kuni on rida andmeid
		//mis vastab select lausele
		// while järgne sulu sisu määrab kaua korratakse
		while($stmt->fetch()) {
			
			//tekitan objekti
			$car = new StdClass();
			$car->id = $id;
			$car->plate = $plate;
			$car->color = $color;
			
			//echo $plate."<br>";
			//igakord massiivi lisan juurde numbrimärgi
			array_push($result, $car);
			
		}
		
		$stmt->close();
		
		return $result;
	}	
		function cleanInput($input){
		
		$input = trim($input);
		$input = stripslashes($input);
		$input = htmlspecialchars($input);
		
		return $input;
		
	}
		function getSingleCarData($edit_id){
    
		
		$stmt = $this->connection->prepare("SELECT plate, color FROM cars_and_colors WHERE id=? AND deleted IS NULL");

		$stmt->bind_param("i", $edit_id);
		$stmt->bind_result($plate, $color);
		$stmt->execute();
		
		//tekitan objekti
		$car = new Stdclass();
		
		//saime ühe rea andmeid
		if($stmt->fetch()){
			// saan siin alles kasutada bind_result muutujaid
			$car->plate = $plate;
			$car->color = $color;
			
			
		}else{
			// ei saanud rida andmeid kätte
			// sellist id'd ei ole olemas
			// see rida võib olla kustutatud
			header("Location: data.php");
			exit();
		}
		
		$stmt->close();

		
		return $car;
		
	}


	function updateCar($id, $plate, $color){
		
		$stmt = $this->connection->prepare("UPDATE cars_and_colors SET plate=?, color=? WHERE id=? AND deleted IS NULL");
		$stmt->bind_param("ssi",$plate, $color, $id);
		
		// kas õnnestus salvestada
		if($stmt->execute()){
			echo "salvestus õnnestus!";
		}
		
		$stmt->close();

		
	}

		function deleteCar($id){
	
	$stmt = $this->connection->prepare("UPDATE cars_and_colors SET deleted=NOW() WHERE id=? AND deleted IS NULL");
	$stmt->bind_param("i",$id);
	
	// kas õnnestus salvestada
	if($stmt->execute()){
		// õnnestus
		echo "kustutamine õnnestus!";
	}
	
	$stmt->close();
	}

?>

