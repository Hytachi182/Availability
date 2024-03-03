<?php



// --------------------------------- MESSAGE ----------------------------------

	function displayMessage()
	{
		if (isset($_SESSION['infoMessage']) && !empty($_SESSION['infoMessage'])) {
			echo '<div class="alert alert-' . $_SESSION['infoMessageType'] . ' alert-dismissible fade show" role="alert">
					' . $_SESSION['infoMessage'] . '
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				  </div>';
			unset($_SESSION['infoMessage']);
			unset($_SESSION['infoMessageType']);
		}
		
	}

// --------------------------------- ENCRYPTION ----------------------------------

	function bobbyCrypt($string, $action = 'e', $secret_key ='') {

		$secret_iv = 'bobbyIsTheBest';
	
		$output = false;
		$encrypt_method = "AES-256-CBC";
		$key = hash('sha256', $secret_key);
		$iv = substr(hash('sha256', $secret_iv), 0, 16);
	
		if ($action == 'e') {
			$output = base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
		} else if ($action == 'd') {
			$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
		}
	
		return strval($output);
	
	}

// ---------------------------------- DATABASE -----------------------------------

    function MYSQLI___get_connector($schema) {


        // INITIATING MYSQL CONNECTION
        $MYSQLI_watchdog = 0;
        do {

            // TRYING TO CONNECT 3 TIMES BEFORE ABANDON
            if($MYSQLI_watchdog++) sleep(1);
            $MYSQLI_LINK = new mysqli(SOURCES[$schema]["host"], SOURCES[$schema]["user"], SOURCES[$schema]["pwd"], SOURCES[$schema]["schema"],  (!empty(SOURCES[$schema]["port"])? SOURCES[$schema]["port"] : 3306));
            if ($MYSQLI_LINK->connect_error || !$MYSQLI_LINK) return null; else return $MYSQLI_LINK;

        } while (is_null($MYSQLI_LINK) && $MYSQLI_watchdog <= 2);

        return $MYSQLI_LINK;

    }

	function MYSQLI___request($link, $sql_request, $output_mode = "ARRAY_OF_VALUES", $params = []) {
		// Vérification des modes de sortie valides
		if (!in_array($output_mode, ["SINGLE_VALUE", "ARRAY_OF_VALUES", "INSERT", "UPDATE", "DELETE"])) {
			return [
				"ret_code"      => 666,
				"message"       => "Invalid output mode ($output_mode).",
				"affected_rows" => 0,
				"query_result"  => NULL
			];
		}
	
		if (is_null($link)) {
			return [
				"ret_code"      => 999,
				"message"       => "MYSQLI connection invalid",
				"affected_rows" => 0,
				"query_result"  => NULL
			];
		}
	
		// Détecte si la requête nécessite une préparation
		if (strpos($sql_request, '?') !== false && !empty($params)) {
			$stmt = $link->prepare($sql_request);
			if ($stmt === false) {
				// Gérer l'erreur de préparation
				return [
					"ret_code"      => $link->errno,
					"message"       => "Prepare failed: " . $link->error,
					"affected_rows" => 0,
					"query_result"  => NULL
				];
			}
	
			// Bind parameters dynamically
			$stmt->bind_param(str_repeat("s", count($params)), ...$params);
			$stmt->execute();
			$result = $stmt->get_result();
		} else {
			// Exécution directe pour les requêtes sans placeholders
			$result = $link->query($sql_request);
		}
	
		// Traitement du résultat en fonction du mode de sortie
		switch ($output_mode) {
			case "SINGLE_VALUE":
				$output_value = ($result) ? $result->fetch_row()[0] : null;
				break;
	
			case "DELETE":
			case "INSERT":
			case "UPDATE":
				$output_value = $link->insert_id;
				break;
	
			default: // "ARRAY_OF_VALUES"
				$output_value = ($result) ? $result->fetch_all(MYSQLI_ASSOC) : [];
		}
	
		return [
			"ret_code"      => $link->errno,
			"message"       => $link->error,
			"affected_rows" => $link->affected_rows,
			"query_result"  => $output_value
		];
	}
	

// ---------------------------------- TOOLBOX ------------------------------------

	function debug($array) 
	{
		echo '<pre>';
		print_r($array);
		echo '</pre>';
	}

	function safe_html($string) {
		return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
	}
	
// -------------------------------------------------------------------------------