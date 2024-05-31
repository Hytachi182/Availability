

<?php


 
// ajax_response.php
 // CONFIG FILE
 
include ($_SERVER['DOCUMENT_ROOT'] . '/AvailabilityWeb/config/config.php'); 


if (isset($_GET['id']) && isset($_GET['date'])) {
    $id = $_GET['id'];
    $date = $_GET['date'];

    $MYSQLI_connection = MYSQLI___get_connector(SCHEMA);
    if (is_null($MYSQLI_connection)) exit("Erreur de connexion à la base de données");

    $sql = "SELECT * FROM AvailabilityWeb.t_dependencies_logs WHERE id_dependencies ='".$id."' AND date ='".$date."';";
    $logs = MYSQLI___request($MYSQLI_connection, $sql)['query_result'];

    if (empty($logs)) {
        echo json_encode([
            'tableHtml' => '<p>No data available</p>',
            'graphData' => json_encode([
                'labels' => [],
                'data' => [],
                'backgroundColors' => []
            ]),
            'responseTimeData' => json_encode([
                'labels' => [],
                'data' => []
            ])
        ]);
        exit;
    }

    $sql = "SELECT * FROM AvailabilityWeb.t_dependencies WHERE id='".$logs[0]['id_dependencies'] ."' ";
    $dependancy = MYSQLI___request($MYSQLI_connection, $sql)['query_result'][0];

    //---------------------------------------------------------------------
    // TABLE
    $tableHtml = "
        <h2>".$dependancy['name']."</h2>
        <table id='data' class='table'>
            <thead>
                <tr>
                <th>Timestamp</th>
                <th>HTTP Code</th>
                <th>Response Time</th>
                <th>TTFB</th>
                <th>Connect Time</th>
                <th>Response Size</th>
                <th>Server IP</th>
                <th>Redirect Count</th>
                <th>Content Type</th>
                </tr>
            </thead>
        <tbody>";

    foreach ($logs as $log) {
        $logEntries = json_decode($log['data'], true);

        foreach ($logEntries as $timestamp => $logEntry) {
            $httpCode = $logEntry['http_code'];
            $responseTime = $logEntry['response_time'];
            $ttfb = $logEntry['ttfb'];
            $connectTime = $logEntry['connect_time'];
            $responseSize = $logEntry['response_size'];
            $serverIp = $logEntry['server_ip'];
            $redirectCount = $logEntry['redirect_count'];
            $contentType = $logEntry['content_type'];
        
            $badgeClass = ($httpCode === 200 || $httpCode === 302 || $httpCode === 401) ? 'bg-success' : 'bg-danger';
            $httpCodeBadge = "<span class='badge {$badgeClass}'>{$httpCode}</span>";
        
            $date = new DateTime($timestamp);
            $formattedDate = $date->format('Y-m-d H:i:s');
        
            $tableHtml .= "<tr>
                            <td>{$formattedDate}</td>
                            <td>{$httpCodeBadge}</td>
                            <td>{$responseTime}</td>
                            <td>{$ttfb}</td>
                            <td>{$connectTime}</td>
                            <td>{$responseSize}</td>
                            <td>{$serverIp}</td>
                            <td>{$redirectCount}</td>
                            <td>{$contentType}</td>
                          </tr>";
        }
    }  

    $tableHtml .= "</tbody>
                    </table>";

    //---------------------------------------------------------------------
    // GRAPH
    $graphData = [
        'labels' => [],
        'data' => [],
        'backgroundColors' => []
    ];

    foreach ($logs as $log) {
        $logEntries = json_decode($log['data'], true);
        foreach ($logEntries as $timestamp => $logEntry) {
            $date = new DateTime($timestamp);
            $hour = $date->format('H');

            if (!in_array($hour, $graphData['labels'])) {
                array_push($graphData['labels'], $hour);
                $isAvailable = ($logEntry['http_code'] === 200 || $logEntry['http_code'] === 302 || $logEntry['http_code'] === 401);
                array_push($graphData['data'], $isAvailable ? 1 : 0);
                array_push($graphData['backgroundColors'], $isAvailable ? '#17d766' : 'red');
            }
        }
    }

    $encodedGraphData = json_encode($graphData);

    $response = [
        'tableHtml' => $tableHtml,
        'graphData' => $encodedGraphData
    ];

    //---------------------------------------------------------------------
    $responseTimeData = [
        'labels' => [],
        'data' => []
    ];

    foreach ($logs as $log) {
        $logEntries = json_decode($log['data'], true);
        foreach ($logEntries as $timestamp => $logEntry) {
            $date = new DateTime($timestamp);
            $hour = $date->format('H');

            if (!in_array($hour, $responseTimeData['labels'])) {
                array_push($responseTimeData['labels'], $hour);
                array_push($responseTimeData['data'], $logEntry['response_time']);
            }
        }
    }

    $encodedResponseTimeData = json_encode($responseTimeData);

    $response['responseTimeData'] = $encodedResponseTimeData;

    echo json_encode($response);
}
?>



