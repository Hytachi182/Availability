<!DOCTYPE html>
<html lang="en">
<head>
    <?php
    // Configuration file
    include './config/config.php';

    // HTML page header
    include './included/header.php';
    ?>
</head>

<body>
    <div>
        <?php
        // include nav
        include './included/nav.php';
        ?>

        <!-- TITLE -->
        <div class="main-header">
            <div class="main-header__intro-wrapper">
                <h1 class="main-header__welcome-title">Get Status</h1>
            </div>
        </div>
        <div class="container mt-5">
            <h2>How to Trigger the Script Externally</h2>
            <p>This guide provides examples on how to call the <code>/background/_collect_statusItems.php</code> script from various programming languages and command-line tools.</p>

            <h3>Python Example</h3>
            <pre><code class="language-python">import requests

        response = requests.get("http://yourdomain.com/background/_collect_statusItems.php?type=json")
        print(response.json())</code></pre>

            <h3>PHP Example</h3>
            <pre><code class="language-php">$response = file_get_contents('http://yourdomain.com/background/_collect_statusItems.php?type=json');
            $data = json_decode($response, true);
            print_r($data);</code></pre>

            <h3>Unix (cURL) Example</h3>
            <pre><code class="language-bash">curl "http://yourdomain.com/background/_collect_statusItems.php?type=json"</code></pre>

            <h3>PowerShell Example</h3>
            <pre><code class="language-powershell">(Invoke-WebRequest -Uri "http://yourdomain.com/background/_collect_statusItems.php?type=json").Content</code></pre>

            <p>Replace <code>http://yourdomain.com</code> with the actual URL to your application.</p>
        </div>

        <hr>
        <div class="container mt-4">
        <h2>Execute from this page </h2>
            <div class="btn-group" role="group" aria-label="Export Buttons">
                <button type="button" class="btn btn-info m-2" onclick="fetchData('html')">HTML</button>
                <button type="button" class="btn btn-primary m-2" onclick="fetchData('json')">JSON</button>
                <button type="button" class="btn btn-secondary m-2" onclick="fetchData('csv')">CSV</button>
                <button type="button" class="btn btn-success m-2" onclick="fetchData('xml')">XML</button>
            </div>
            <!-- Result Display -->
            <div id="resultDisplay" class="mt-4">
                Result Here (wait after click)
            </div>
        </div>

        <?php
        displayMessage();
        ?>
    </div>
        <script>
        function fetchData(format) {
            var url = `./background/_collect_statusItems.php`;
            // For HTML format, modify the request URL or keep it without parameters based on your backend setup.
            if (format !== 'html') {
                url += `?type=${format}`;
            }

            // Handle CSV format by navigating to the URL to trigger the download.
            if (format === 'csv') {
                window.location.href = url;
            } else {
                // Use jQuery AJAX for JSON, XML, and HTML formats.
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(data) {
                        var displayDiv = $('#resultDisplay');
                        if (format === 'json') {
                            // Pretty-print JSON data.
                            displayDiv.text(JSON.stringify(data, null, 2));
                        } else if (format === 'xml') {
                            // Display XML data as a string.
                            displayDiv.text((new XMLSerializer()).serializeToString(data));
                        } else if (format === 'html') {
                            // Directly insert HTML data.
                            displayDiv.html(data);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        // Handle any errors during the request.
                        console.error('Error fetching data:', textStatus, errorThrown);
                        $('#resultDisplay').text('Error fetching data: ' + textStatus);
                    },
                    dataType: format === 'json' ? 'json' : format === 'xml' ? 'text' : 'html' // Set expected data type.
                });
            }
        }
    </script>


</script>

</body>
</html>
