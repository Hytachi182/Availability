
<h1>AvailabilityWeb Installation Guide</h1>
<h2>Prerequisites:</h2>
<ul>
<li>PHP environment with Apache and MySQL.</li>
<li><code>AvailabilityWeb.sql</code> script located in the install directory for database setup.</li>
</ul>

<h2>Step 1: Clone the Repository</h2>
<p>Clone the "AvailabilityWeb" project from GitHub to your local machine or server using the following command:</p>
<pre><code>git clone https://github.com/Hytachi182/Availability.git</code></pre>

<h2>Step 2: Database Setup</h2>
<p>Before running the <code>AvailabilityWeb.sql</code> script, ensure you have a MySQL database server running.</p>
<p>Create a new schema/database named <code>AvailabilityWeb</code> in your MySQL server:</p>
<pre><code>CREATE DATABASE IF NOT EXISTS `AvailabilityWeb`;
USE `AvailabilityWeb`;</code></pre>
<p>Run the <code>AvailabilityWeb.sql</code> script located in the install directory to create necessary tables and initial data. This can be done via a MySQL client or command line.</p>

<h2>Step 3: Configuration</h2>
<p>Navigate to the config directory within your cloned project.</p>
<p>Rename <code>datasource.private.example</code> to <code>datasource.private</code> and configure it with your database details.</p>
<p>Open <code>config.php</code> to set up global configurations:</p>
<ul>
<li><strong>Session Name:</strong> A unique name for your application session to avoid conflicts.</li>
<li><strong>Root URL & Subfolder:</strong> Define the root URL and subfolder where your application is hosted.</li>
<li><strong>Application Name:</strong> A friendly name for your application.</li>
<li><strong>Schema:</strong> The name of your MySQL schema/database, which should match what you created.</li>
<li><strong>Proxy Settings:</strong> If your application needs to access external resources through a proxy, specify the proxy IP and port, along with any required authentication details.</li>
<li><strong>Timezone:</strong> Set the default timezone for your application.</li>
<li><strong>Environment Mode:</strong> For development, errors will be displayed for debugging. In production mode, errors are logged to a file for security.</li>
</ul>

<h2>Step 4: Deployment</h2>
<p>Ensure your Apache server is configured to serve the cloned "AvailabilityWeb" directory.</p>
<p>Access the application through your web browser by navigating to the URL where it's hosted.</p>

<h2>Step 5: Final Checks</h2>
<p>Upon accessing "AvailabilityWeb", you should be able to see the main interface.</p>
<p>Perform any necessary tests to ensure connectivity to the database and that all functionalities are working as expected.</p>

