<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    // Configuration file
    include './config/config.php';

    // HTML page header
    include './included/header.php';

    // RECUPERE tous les groupes uniques
    $sql = "SELECT DISTINCT `group` FROM AvailabilityWeb.t_dependencies ORDER BY `group` ASC;";
    $groups = MYSQLI___request($MYSQLI_connection, $sql)['query_result'];


    // Test GET parameter for duration
    if (!empty($_GET['duration'])) {
        $validDurations = [7, 15, 30, 60, 90];
        $duration = intval($_GET['duration']);

        if (!in_array($duration, $validDurations)) {
            // If duration is not valid, redirect or show an error
            // You can uncomment the next line if you have a forbidden function or use another way to handle it
            // forbidden();
        }
    } else {
        $duration = 90; // Default duration
    }

    // Après avoir récupéré tous les groupes uniques
    if (!isset($_GET['group']) || empty($_GET['group'])) {
        // Si aucun groupe n'est explicitement sélectionné, prenez le premier de la liste
        $currentGroup = $groups[0]['group'];
    } else {
        // Sinon, utilisez le groupe sélectionné
        $currentGroup = $_GET['group'];
    }

    // Assurez-vous de nettoyer la valeur pour éviter les injections SQL et autres vulnérabilités
    $currentGroup = htmlspecialchars($currentGroup);


    $durationOptions = [7, 15, 30, 60, 90];
    $baseWidth = 11; // Base width for 90 days
    $maxWidth = 60; // Maximum width for a bar
    $adjustmentFactor = 90 / $duration; // Adjustment factor
    $barWidth = min($baseWidth * $adjustmentFactor, $maxWidth);



    ?>



    <style>
        .bar {
            float: left;
            width: <?= $barWidth; ?>px;
            /* Make sure to include 'px' for width */
            height: 30px;
            border-left: 1px solid #fff;
            cursor: pointer;
        }
    </style>
</head>


<body >

    <div >
    <?php
        // include nav
        include './included/nav.php';
    ?>

        <!-- TITLE -->
        <div class="main-header">
            <div class="main-header__intro-wrapper">
                <h1 class="main-header__welcome-title">Availability Web</h1>
            </div>
        </div>

        <section class="info mb-5">
            <div class="container">
                <form method="GET" action="statusItems.php">
                    <div class="row align-items-end g-3">
                        <!-- Historic Period Selector -->
                        <div class="col-md">
                            <label for="duration" class="form-label">Choose historic period:</label>
                            <select class="form-select" name="duration" id="duration">
                                <?php foreach ($durationOptions as $option) : ?>
                                <option value="<?= $option; ?>" <?= ($duration == $option) ? 'selected' : ''; ?>>
                                    <?= $option; ?> days
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Group Selector -->
                        <div class="col-md">
                            <label for="group" class="form-label">Category:</label>
                            <select class="form-select" name="group" id="group" onchange="this.form.submit()">
                                <option value="">Select a group</option>
                                <?php foreach ($groups as $group) : ?>
                                <option value="<?= htmlspecialchars($group['group']); ?>" <?= (isset($_GET['group']) && $_GET['group'] === $group['group']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($group['group']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Legend -->
                        <div class="col-md-auto">
                            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-start gap-2">
                                <div class="d-flex align-items-center">
                                    <div class="legend-colour border"></div>
                                    <p class="mb-0">No Data</p>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="legend-colour green"></div>
                                    <p class="mb-0">100% Available</p>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="legend-colour yellow"></div>
                                    <p class="mb-0">0 - 30 min not Available</p>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="legend-colour red"></div>
                                    <p class="mb-0">30+ min not Available</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>


        <h4 class='mt-4 mb-4 text-center'>Availability is calculated only on days when data is available.</h4>
       
        <?php
        displayMessage();
        ?>

        <section class="stats mb-5">

            <?php


                // Assurez-vous de nettoyer la valeur pour l'utiliser dans un attribut HTML
                $groupId = htmlspecialchars($currentGroup);
                echo '<div class="group border">';
                echo '<button class="btn" type="button" data-bs-toggle="collapse" data-bs-target="#collapse' . $groupId . '" aria-expanded="true" aria-controls="collapse' . $groupId . '">';
                echo '<h2></i> ' . strtoupper($groupId) . '</h2>';
                echo '</button>';
                echo '<div class="collapse show" id="collapse' . $groupId . '">';

                // RECUPERE tous les items appartenant à ce groupe
                $sql = "SELECT * FROM AvailabilityWeb.t_dependencies WHERE `group` = '" . $currentGroup . "';";
                $items = MYSQLI___request($MYSQLI_connection, $sql)['query_result'];

                foreach ($items as $item) {

                    echo '<article>';
                    echo '<div class="service ml-3 pb-1">' . $item['name'] . ' </div>';
                    echo "<button class='info-btn' data-url='" . $item['url'] . "'><i class='fas fa-question-circle'></i></button>";
                    echo '<div class="chart">
                            <div class="status-bar-container">
                                <div class="status-bar">
                                <div class="status-bar-labels">
                                    <span class="label-left">' . $duration . ' days ago</span>
                                    <span id="available_' . $item['name'] . '" class="label-center">689.61% uptime</span>
                                    <span class="label-right">Today</span>
                                </div>
                                <div class="status-bar-background">
                    
                    ';
                    // RECUPERE les logs sur 90 jours
                    $sql = "SELECT * FROM AvailabilityWeb.t_dependencies_logs WHERE id_dependencies ='" . $item['id'] . "' AND date >= CURDATE() - INTERVAL " . $duration . " DAY ORDER BY date ASC;";
                    $logs = MYSQLI___request($MYSQLI_connection, $sql)['query_result'];


                    $logData = [];
                    foreach ($logs as $log) {
                        $logData[$log['date']] = json_decode($log['data'], true);
                    }

                    // Générer une barre pour chaque jour des $duration derniers jours
                    $startDate = new DateTime($duration . ' days ago');

                    // Date de fin : demain (ce qui inclut tout aujourd'hui)
                    $endDate = new DateTime('tomorrow');

                    // Intervalle de 1 jour
                    $interval = new DateInterval('P1D');

                    // Période incluant chaque jour de la plage

                    $period = new DatePeriod($startDate, $interval, $endDate);
                    $countTotal = 0;
                    $countAvailigity = 0;

                    foreach ($period as $day) {

                        $countTotal =  $countTotal + 1;
                        $dayFormatted = $day->format('Y-m-d');
                        $totalDowntime = 0;
                        $lastDowntimeStart = null;

                        $dataExistsForDay = isset($logData[$dayFormatted]) && !empty($logData[$dayFormatted]);

                        if ($dataExistsForDay) {

                            foreach ($logData[$dayFormatted] as $time => $logEntry) {
                                $http_code = $logEntry['http_code'];

                                if (!in_array($http_code, [200, 401, 302]) && $lastDowntimeStart === null) {
                                    $lastDowntimeStart = new DateTime($time);
                                } elseif (in_array($http_code, [200, 401, 302]) && $lastDowntimeStart !== null) {
                                    $currentTimestamp = new DateTime($time);
                                    $totalDowntime += $currentTimestamp->getTimestamp() - $lastDowntimeStart->getTimestamp();
                                    $lastDowntimeStart = null;
                                }
                            }

                            if ($lastDowntimeStart !== null) {
                                $currentTimestamp = new DateTime("now");
                                $totalDowntime += $currentTimestamp->getTimestamp() - $lastDowntimeStart->getTimestamp();
                            }
                        }


                        if ($dataExistsForDay) {
                            if ($totalDowntime == 0) {
                                $barColor = "green";
                                $tooltipText = "100% Availability";
                                $countAvailigity = $countAvailigity + 1;
                            } elseif ($totalDowntime > 0 && $totalDowntime < 30 * 60) {
                                $barColor = "yellow";
                                $tooltipText = "0 - 30 min not Available";
                            } elseif ($totalDowntime >= 30 * 60) {
                                $barColor = "red";
                                $tooltipText = "30+ min not Available";
                            }
                        } else {
                            $barColor = "border"; // couleur par défaut pour "aucune donnée"
                            $tooltipText = "No data available";
                            $countAvailigity = $countAvailigity + 1;
                        }


                        echo "<div class='custom-tooltip bar $barColor' data-bs-toggle='modal' data-bs-target='#detailsModal' data-name='" . $item['name'] . "' data-date=" . $dayFormatted . " data-id='" . $item['id'] . "'>";
                        echo "<span class='tooltip'>$tooltipText</span>";
                        echo "</div>";
                    }

                    $p = (100 * $countAvailigity) / $countTotal;

                    echo '</div>
                <div class="background-bar"></div>
                <div id="percentage_' . $item['name'] . '" class="d-none">' . round($p, 2) . '</div>
                    </div>
                </div>  
                    </article>';
                    }
                    echo '</div>'; // Fin de .collapse
                    echo '</div>'; // Fin de .group
           
            ?>

        </section>

    </div>

    <!-- Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 id='modal-title-custom'></h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- GRAPH -->
                    <div style="width:100%; height:200px;">
                        <canvas id="availabilityChart"></canvas>
                    </div>
                    <div id="detailsTable">
                        <!-- ADDED BY JS -->
                    </div>
                    <!-- GRAPH -->
                    <div clas='mt-3' style="width:100%; height:200px;">
                        <canvas id="responseTimeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
    $(document).ready(function() {

        let availabilityChart; 
        let responseTimeChart; 

        $('[id^="percentage_"]').each(function() {
            var itemName = this.id.split('_')[1].replace(/ /g, '\\ '); // Échappez les espaces
            var percentageValue = $(this).text();
            $('#available_' + itemName).text(percentageValue + ' % available');
        });

        $('#duration').change(function() {
            $(this).closest('form').submit();
        });

        $('[data-bs-togglebis="tooltip"]').tooltip();

        $('.info-btn').hover(function() {
            var url = $(this).data('url');
            var tooltip = $('<div class="tooltip-url">' + url + '</div>');
            $(this).append(tooltip);
            tooltip.fadeIn();
        }, function() {
            $('.tooltip-url').remove();
        });

        $('#detailsModal').on('show.bs.modal', function(event) {
    var button = $(event.relatedTarget);
    var id = button.data('id'); // Récupérer id de l'élément cliqué
    var date = button.data('date'); // Récupérer la date de l'élément cliqué
    var name = button.data('name'); // Récupérer le nom de l'élément cliqué

    var modalTitle = name + " - " + date;
    $(this).find('#modal-title-custom').text(modalTitle);

    $.ajax({
        url: './background/_statusItems.php',
        type: 'GET',
        data: {
            'id': id,
            'date': date
        }, // Envoyer à la fois l'id et la date
        success: function(response) {
            var data = JSON.parse(response);
            var table = $('#detailsTable');
            table.empty();

            if (data.tableHtml && data.tableHtml.trim() !== "") {
                table.html(data.tableHtml);

                var responseTimeData = JSON.parse(data.responseTimeData);
                createResponseTimeChart(responseTimeData);

                var graphData = JSON.parse(data.graphData);
                createAvailabilityChart(graphData);

                new DataTable('#data');
            } else {
                table.html("<p>No data available</p>");
                $('#availabilityChart').remove(); // Optionnel : Supprimer le canvas si pas de données
                $('#responseTimeChart').remove(); // Optionnel : Supprimer le canvas si pas de données
            }
        },
        error: function(xhr, status, error) {
            console.error("error : " + error);
        }
    });
});

function createAvailabilityChart(graphData) {
    const ctx = document.getElementById('availabilityChart').getContext('2d');
    if (availabilityChart) {
        availabilityChart.destroy(); // Détruire le graphique précédent s'il existe
    }
    availabilityChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: graphData.labels,
            datasets: [{
                label: 'Availability per hour',
                data: graphData.data,
                backgroundColor: graphData.backgroundColors
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        max: 1 // Car les valeurs sont soit 0, soit 1
                    }
                },
                x: {
                    scaleLabel: {
                        display: true,
                        labelString: 'Hour'
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        }
    });
}

function createResponseTimeChart(responseTimeData) {
    const ctx = document.getElementById('responseTimeChart').getContext('2d');
    if (responseTimeChart) {
        responseTimeChart.destroy(); // Détruire le graphique précédent s'il existe
    }
    responseTimeChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: responseTimeData.labels,
            datasets: [{
                label: 'Response time',
                data: responseTimeData.data,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                fill: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Response time (seconds)'
                    }
                },
                x: {
                    scaleLabel: {
                        display: true,
                        labelString: 'Hours'
                    }
                }
            }
        }
    });
}


        function createAvailabilityChart(graphData) {
            const ctx = document.getElementById('availabilityChart').getContext('2d');
            if (availabilityChart) {
                availabilityChart.destroy(); // Détruire le graphique précédent s'il existe
            }
            availabilityChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: graphData.labels,
                    datasets: [{
                        label: 'Availability per hour',
                        data: graphData.data,
                        backgroundColor: graphData.backgroundColors
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                max: 1 // Car les valeurs sont soit 0, soit 1
                            }
                        },
                        x: {
                            scaleLabel: {
                                display: true,
                                labelString: 'Hour'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    }
                }
            });
        }

        function createResponseTimeChart(responseTimeData) {
            const ctx = document.getElementById('responseTimeChart').getContext('2d');
            if (responseTimeChart) {
                responseTimeChart.destroy(); // Détruire le graphique précédent s'il existe
            }
            responseTimeChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: responseTimeData.labels,
                    datasets: [{
                        label: 'Response time',
                        data: responseTimeData.data,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            scaleLabel: {
                                display: true,
                                labelString: 'Response time (seconds)'
                            }
                        },
                        x: {
                            scaleLabel: {
                                display: true,
                                labelString: 'Hours'
                            }
                        }
                    }
                }
            });
        }

    });
</script>
</body>


</html>
