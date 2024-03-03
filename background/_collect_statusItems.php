<?php
// Include the configuration file
include $_SERVER['DOCUMENT_ROOT'] . '/AvailabilityWeb/config/config.php';


// Check the 'type' parameter to determine output format
$outputType = isset($_GET['type']) ? $_GET['type'] : '';

// Initialize an array to hold dependency data
$dependenciesData = [];

// Fetch all dependencies
$sql = "SELECT * FROM AvailabilityWeb.t_dependencies;";
$dependencies = MYSQLI___request($MYSQLI_connection, $sql)['query_result'];

foreach ($dependencies as $dependency) {
    // Initialize cURL
    $ch = curl_init($dependency['url']);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_HTTPGET => true,
    ]);

    // Add proxy if mentioned in the config
    if ($dependency['proxy'] === 'yes') {
        curl_setopt($ch, CURLOPT_PROXY, PROXY);
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, PWD_PROXY);
    }

    // Execute cURL request
    $response = curl_exec($ch);
    $info = curl_getinfo($ch);
    $http_code = $response ? $info['http_code'] : "error_connection";
    
    // Close cURL session
    curl_close($ch);

    // Store dependency data
    $dependenciesData[] = [
        'name' => $dependency['name'],
        'http_code' => $http_code,
        'response_time' => $info['total_time'] ?? null,
        'server_ip' => $info['primary_ip'] ?? null,
    ];
}

// Output handling
switch ($outputType) {
    case 'json':
        header('Content-Type: application/json');
        echo json_encode($dependenciesData);
        break;
    case 'csv':
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="dependencies_' . date('Y-m-d') . '.csv"');
        $fp = fopen('php://output', 'w');
        fputcsv($fp, array_keys($dependenciesData[0]));
        foreach ($dependenciesData as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);
        break;
    case 'xml':
        header('Content-Type: text/xml');
        $xml = new SimpleXMLElement('<dependencies/>');
        foreach ($dependenciesData as $dependency) {
            $dependencyNode = $xml->addChild('dependency');
            foreach ($dependency as $key => $value) {
                $dependencyNode->addChild($key, $value);
            }
        }
        echo $xml->asXML();
        break;
    default:
        // Include header file for HTML output
        include $_SERVER['DOCUMENT_ROOT'] . '/AvailabilityWeb/included/header.php';
        foreach ($dependenciesData as $dependency) {
            $alertClass = $dependency['http_code'] === "error_connection" ? 'alert-danger' : 'alert-info';
            echo "<div class='alert {$alertClass}' role='alert'>" . htmlspecialchars($dependency['name']) . " - HTTP code: " . htmlspecialchars($dependency['http_code']) . "</div>";
        }
}


?>
