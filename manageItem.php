<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    // Configuration file
    include './config/config.php';

    // HTML page header
    include './included/header.php';

    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {


        $name = $_POST['name'];
        $url = $_POST['url'];
        $description = $_POST['description'];
        $group = $_POST['group'];
        $proxy = $_POST['proxy'];

        // Utilisation de requêtes préparées pour éviter les injections SQL
        $insert = "INSERT INTO `AvailabilityWeb`.`t_dependencies` (`name`, `url`, `description`, `group`, `proxy`) VALUES (  '$name ',     '$url',  '$description' ,   '$group', '$proxy');";
        $resultInsert = MYSQLI___request($MYSQLI_connection, $insert,'INSERT');

        if ($resultInsert ['ret_code'] == 0 ) {

            $_SESSION['infoMessage'] = "Item  added successfully!";
            $_SESSION['infoMessageType'] = "success";
            } else {
                $_SESSION['infoMessage'] = "Item  Not added !";
                $_SESSION['infoMessageType'] = "danger";
         }
    }
    
    //GET DATA FROM TABLE
    $result = MYSQLI___request($MYSQLI_connection, 'SELECT * FROM AvailabilityWeb.t_dependencies;')['query_result'];

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

<body>
    <?php
    // include nav
    include './included/nav.php';
    ?>
    <div class="container-fluid mt-5">

        <!-- TITLE -->
        <div class="main-header">
            <div class="main-header__intro-wrapper">
                <h1 class="main-header__welcome-title">Manage Items</h1>
            </div>
        </div>
       
    <div class="row">

        <div class="col-1">
        <!-- Open Modal-->
            <button type="button" class="btn btn-secondary mb-5" data-bs-toggle="modal" data-bs-target="#addItemModal">
                <i class="fas fa-plus-circle"></i> Add
            </button>
        </div>
        <div class="col-2">
            <!-- Export Csv-->
             <button id="exportButton" class="btn btn-success mb-2"><i class="fas fa-file-export"></i> Export to CSV</button>
         </div>
         <div class="col-6">
            <?php
                displayMessage();
            ?>
         </div>
    </div>

        <!-- TABLE -->
        <div class="table-responsive">
            <table id="dataTable" class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>URL</th>
                        <th>Description</th>
                        <th>Group</th>
                        <th>Proxy</th>
                    </tr>
                </thead>
                <tbody>
                        <?php
                        foreach ($result as $row) {
                            echo "<tr>";
                            echo "<td> 
                                <button type='button' class='btn btn-danger btn-sm' data-bs-toggle='modal' data-bs-target='#deleteModal' data-bs-id='" . $row['id'] . "'>Delete</button>
                                <button type='button' class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#updateModal' 
                                data-bs-id='" . $row['id'] . "' 
                                data-bs-name='" . $row['name'] . "' 
                                data-bs-url='" . $row['url'] . "' 
                                data-bs-description='" . $row['description'] . "' 
                                data-bs-group='" . $row['group'] . "' 
                                data-bs-proxy='" . $row['proxy'] . "'>Update
                                </button>
                            
                            </td>";
                            echo "<td>" . safe_html($row['id']) . "</td>";
                            echo "<td>" . safe_html($row['name']) . "</td>";
                            echo "<td>" . safe_html($row['url']) . "</td>";
                            echo "<td>" . safe_html($row['description']) . "</td>";
                            echo "<td>" . safe_html($row['group']) . "</td>";
                            echo "<td>" . safe_html($row['proxy']) . "</td>";
                            echo "</tr>";
                        }
                        ?>
              </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Add -->
        <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addItemModalLabel">Manage Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Formulaire -->
                    <form action="manageItem.php" method="POST" id="addItem">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Name:</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="group" class="form-label">Group:</label>
                                <input type="text" class="form-control" id="group" name="group" required>
                            </div>
                            <div class="col-md-6">
                                <label for="proxy" class="form-label">Proxy:</label>
                                <select class="form-select" id="proxy" name="proxy">
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="url" class="form-label">URL:</label>
                                <input type="url" class="form-control" id="url" name="url" required>
                            </div>
                            <div class="col-12">
                                <label for="description" class="form-label">Description:</label>
                                <textarea class="form-control" id="description" name="description"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="addItem" class="btn btn-primary">Submit</button>
                </div>
                </div>
            </div>
        </div>

    <!-- Modal Delete -->
        <div class='modal fade' id='deleteModal' tabindex='-1' aria-hidden='true'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                <div class='modal-header'>
                    <h5 class='modal-title'>Confirm Delete</h5>
                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                </div>
                <div class='modal-body'>
                    Are you sure you want to delete this item?
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancel</button>
                    <button type='button' class='btn btn-danger' id='deleteConfirm'>Delete</button>
                </div>
                </div>
            </div>
        </div>

    <!-- Modal Update -->
    <div class='modal fade' id='updateModal' tabindex='-1' aria-hidden='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <form id='updateForm'>
                    <div class='modal-header'>
                        <h5 class='modal-title'>Update Item</h5>
                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                    </div>
                    <div class='modal-body'>
                        <!-- Champs de formulaire -->
                        <input type='hidden' id='updateId' name='id'>
                        <div class="mb-3">
                            <label for="updateName" class="form-label">Name:</label>
                            <input type="text" class="form-control" id="updateName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="updateUrl" class="form-label">URL:</label>
                            <input type="text" class="form-control" id="updateUrl" name="url" required>
                        </div>
                        <div class="mb-3">
                            <label for="updateDescription" class="form-label">Description:</label>
                            <textarea class="form-control" id="updateDescription" name="description"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="updateGroup" class="form-label">Group:</label>
                            <input type="text" class="form-control" id="updateGroup" name="group" required>
                        </div>
                        <div class="mb-3">
                            <label for="updateProxy" class="form-label">Proxy:</label>
                            <select class="form-select" id="updateProxy" name="proxy">
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                        </div>
                        <!-- Ajoutez d'autres champs au besoin -->
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                        <button type='submit' class='btn btn-primary'>Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>




   <script>
        $(document).ready(function() {
            
            //FUNCION
            function downloadCSV(csv, filename) {
                var csvFile;
                var downloadLink;

                // CSV file
                csvFile = new Blob([csv], {type: "text/csv"});

                // Download link
                downloadLink = document.createElement("a");

                // File name
                downloadLink.download = filename;

                // Create a link to the file
                downloadLink.href = window.URL.createObjectURL(csvFile);

                // Hide download link
                downloadLink.style.display = "none";

                // Add the link to the DOM
                $("body").append(downloadLink);

                // Click download link
                downloadLink.click();
            }
            
            
            // Initialisation de DataTable
            $('#dataTable').DataTable();

        //DELETE ---------------------------------
            // Lors de l'ouverture de la modale de suppression, on récupère l'ID et on l'attache au bouton de confirmation
            $('#deleteModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget); // Le bouton qui a ouvert la modale
                var id = button.data('bs-id'); // Récupération de l'ID
                $('#deleteConfirm').data('id', id); // Attachement de l'ID au bouton de confirmation
            });

            $('#deleteConfirm').click(function() {
                var id = $(this).data('id'); // Assurez-vous que cette partie récupère correctement l'ID

                // AJAX call
                $.ajax({
                    url: './background/_processItem.php', // Vérifiez que le chemin d'accès est correct
                    type: 'POST',
                    data: {
                        id: id,
                        action: 'delete' // Assurez-vous que 'action' est correctement défini
                    },
                    success: function(response) {
                        // Gérez ici le succès de la requête, par exemple en affichant un message ou en rechargeant la page/la table des éléments
                        console.log("delete ok ");

                        location.reload(); // Ceci rechargera la page pour mettre à jour la liste des éléments
                    },
                    error: function(xhr, status, error) {
                        // Gérez ici les erreurs potentielles
                        console.error("Error: ", status, error);
                    }
                });
            });

        //UPDATE ---------------------------------

        $('#updateModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Le bouton qui a déclenché la modal
            var id = button.data('bs-id');
            var name = button.data('bs-name');
            var url = button.data('bs-url');
            var description = button.data('bs-description');
            var group = button.data('bs-group');
            var proxy = button.data('bs-proxy');

            var modal = $(this);
            modal.find('.modal-body #updateId').val(id);
            modal.find('.modal-body #updateName').val(name);
            modal.find('.modal-body #updateUrl').val(url);
            modal.find('.modal-body #updateDescription').val(description);
            modal.find('.modal-body #updateGroup').val(group);
            modal.find('.modal-body #updateProxy').val(proxy);
        });


        // Soumission du formulaire d'update
        $('#updateForm').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serialize(); // Récupère les données du formulaire

            $.ajax({
                url: './background/_processItem.php',
                type: 'POST',
                data: formData + '&action=update',
                success: function(response) {
                    $('#updateModal').modal('hide');
                    // Mettez à jour l'interface utilisateur ici, si nécessaire
                    alert("Item updated successfully!");
                    location.reload(); // Ou rafraîchissez les données de la table sans recharger la page
                },
                error: function() {
                    alert("An error occurred during the update.");
                }
            });
        });

            $("#exportButton").click(function() {
                var csv = [];
                // Use jQuery to select the table rows
                $("table tr").each(function() {
                    var row = [];
                    // Exclude the 'Action' column from export by starting with the second cell
                    $(this).find('th:not(:first-child), td:not(:first-child)').each(function() {
                        var text = $(this).text().replace(/"/g, '""'); // Escape double-quote with two double-quotes
                        row.push('"' + text + '"');
                    });
                    csv.push(row.join(","));
                });

                 // Get the current date
                var currentDate = new Date();
                var dateString = currentDate.getFullYear() + "-" + (currentDate.getMonth() + 1) + "-" + currentDate.getDate();

                // Call function to download the CSV
                downloadCSV(csv.join("\n"), "availabilityweb_items-" + dateString + ".csv");
            });


        });
    </script>
</body>


</html>