


<head>
    <?php
        // CONFIG FILE
        include $_SERVER['DOCUMENT_ROOT'] . '/AvailabilityWeb/config/config.php';

        include $_SERVER['DOCUMENT_ROOT'] . '/AvailabilityWeb/included/header.php';
    ?>
    <title><?= strtoupper(preg_replace("/_/", " ", APPLICATION_NAME)) ?> - AvailabilityWeb</title>

</head>

<?php


$MYSQLI_connection = MYSQLI___get_connector(SCHEMA);
if (is_null($MYSQLI_connection)) exit("Erreur de connexion à la base de données");

$sql = "SELECT * FROM AvailabilityWeb.t_dependencies;";
$items = MYSQLI___request($MYSQLI_connection, $sql)['query_result'];

 //---------------------------------------------------------------------
// TABLE
$tableHtml = "
    <table id='data' class='table'>
        <thead>
            <tr>
            <th>id</th>
            <th>name</th>
            <th>url</th>
            <th>description</th>
            <th>group</th>
            <th>proxy</th>
            </tr>
        </thead>
    <tbody>";

    foreach ($items as $item) {
        
        
            $tableHtml .= "<tr>
                            <td>{$item['id']}</td>
                            <td>{$item['name']}</td>
                            <td>{$item['url']}</td>
                            <td>{$item['description']}</td>
                            <td>{$item['group']}</td>
                            <td>{$item['proxy']}</td>

                        </tr>";
        }
    
        $tableHtml .= "</tbody>
                        </table>";
                     
?>

<body id='subpage'>

    <?php
        // NAV SIDEBAR
        include $_SERVER['DOCUMENT_ROOT'] . '/rpa/included/nav.php';
    ?>

    <div class="wrapper">
        <div class="welcome m-5 ">
            <div class="content">
                <h1 class="fs-1">Dependencies </h1>
                <p class="mb-0">List dependencies</p>
            </div>
        </div>
      
        <div class="container-fluid ">
            <div class="row-mt5">
                <div class="col-12">
                     <?php echo  $tableHtml ; ?>
                 </div>
            </div>
        </div>

    </div>
</body>




