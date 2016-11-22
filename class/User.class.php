<?php 
class User {
	
	private $connection;
	function __construct($mysqli){
		//this viitab klassile (this == User)
		$this->connection = $mysqli;	
	}
	
	/*TEISED FUNKTSIOONID*/
	
	function signUp ($username, $email, $password) {
		$this->connection->set_charset("utf8");
		$stmt = $this->connection->prepare("
		INSERT INTO project_users (username, email, password) 
		VALUES (?, ?, ?)");
		echo $this->connection->error;
		$stmt->bind_param("sss", $username, $email, $password);  //asendan küsimärgid
		
		if($stmt->execute()) {
			//echo "salvestamine õnnestus";
		} else {
		 	echo "ERROR ".$stmt->error;                  //ERROR Duplicate entry
		}
		$stmt->close();
	}
	
	function login ($email, $password) {
		$msg = "";
		$this->connection->set_charset("utf8");
		$stmt = $this->connection->prepare("
		SELECT user_id, username, email, password 
		FROM project_users
		WHERE email = ?");
		echo $this->connection->error;
		$stmt->bind_param("s", $email);      //asendan küsimärgi
		
		//andmebaasist väärtused muutujatesse
		$stmt->bind_result($idFromDb, $usernameFromDb, $emailFromDb, $passwordFromDb);
		$stmt->execute();
		
		
		if($stmt->fetch()){        // on tõene kui on vähemalt üks vaste
			$hash = hash("sha512", $password);       //password millega kasutaja tahab sisse logida
			if ($hash == $passwordFromDb) {
				//echo "Kasutaja logis sisse ".$usernameFromDb;
				
	  //SESSIOONI MUUTUJAD, millele saan ligi teistelt lehtedelt
				$_SESSION["userId"] = $idFromDb;
				$_SESSION["username"] = $usernameFromDb;
				//$_SESSION["message"] = "<h1>Tere tulemast!</h1>";
				header("Location: user.php"); //?id=".$_SESSION["userId"]);
				exit();	
			}else {
				$msg = "Sisestatud salasõna on vale!";
			}
				
		} else {
			$msg = "Sellist e-posti aadressi ei leitud!";
		}
		
		return $msg;	
	}
	
	
}
?>