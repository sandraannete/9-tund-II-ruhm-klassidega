<?php 
class User {

	private $connection;
	public $name;

	function __construct($mysqli){

		//"this" viitab klassile, this on user
		$this->connection = $mysqli;
	

	}
	//Teised funktsioonid
	function signUp ($email, $password) {
		
		
		$stmt = $this->connection->prepare("INSERT INTO user_sample (email, password) VALUES (?, ?)");
	
		echo $this->connection->error;
		
		$stmt->bind_param("ss", $email, $password);
		
		if($stmt->execute()) {
			echo "salvestamine õnnestus";
		} else {
		 	echo "ERROR ".$stmt->error;
		}
		
		$stmt->close();
		$this->connection->close();
	
	}
	//1.11 Sisselogimise funktsiooni saab kätte $User->login (ehk user -> ja funkts. nimi)
	function login ($email, $password){
		
		$error = "";
		
		 
		$stmt = $this->connection->prepare("
		SELECT id, email, password, created FROM user_sample WHERE email = ?");
		
		echo $this->connection->error;
		
		//asendad küsimärgi bind_param- võtab muutuja ja asendab selle väärtusesse, mida on kolm :"s", "i", "d"
		$stmt->bind_param("s", $email);
		
		
		//määran väärtused muutujasse
		$stmt->bind_result($id, $emailFromDb, $passwordFromDb, $created);
		$stmt->execute();
		
		//tõene kui on vähemalt üks vaste
		//andmed tulid andmebaasist v ei
		if($stmt->fetch()){
			
			//oli sellise meiliga kasutaja
			//password millega kasutaja tahab sisse logida
			$hash = hash("sha512", $password);
			if ($hash == $passwordFromDb) {
				
				echo "kasutaja logis sisse".$id;
				
				//määran sessiooni muutujad, millele saan ligi teistelt lehtedelt
				$_SESSION["userId"] = $id;
				$_SESSION["userEmail"] = $emailFromDb;
				
				
				//$_SESSION["message"] = <h1>Tere tulemast</h1>;
				//kui ühe näitame siis kustuta ära, et pärast refreshi ei näitaks
				unset($_SESSION["message"]);
				
				header("Location: data.php");
				
			}else {
				$error = "vale parool";
				
			}
		
			
		} else {
				//ei leidnud kasutajat sellise meiliga
				$error = "ei ole sellist emaili";
				
		}	

		return $error;
		
	}

}
?>

