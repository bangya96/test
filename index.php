<?php
// Connection details
$username = "your_oracle_username"; 
$password = "your_oracle_password"; 
$connection_string = "localhost/XEPDB1"; 
// Example for Oracle XE: localhost/XE 
// Or with service name: ip:port/servicename

// Connect to Oracle
$conn = oci_connect($username, $password, $connection_string);
if (!$conn) {
    $e = oci_error();
    die("Connection failed: " . $e['message']);
}

// Handle search
$search = "";
if (isset($_POST['search'])) {
    $search = $_POST['search'];

    // Build SQL query (use bind variable to avoid SQL injection)
    $sql = "SELECT * FROM MPLN.LINDUK WHERE UPPER(COLUMN_NAME) LIKE UPPER(:search)";

    $stid = oci_parse($conn, $sql);
    oci_bind_by_name($stid, ":search", $param);
    $param = "%" . $search . "%";

    oci_execute($stid);

    echo "<table border='1' cellpadding='5'>";
    $ncols = oci_num_fields($stid);

    // Print header
    echo "<tr>";
    for ($i = 1; $i <= $ncols; $i++) {
        $colname = oci_field_name($stid, $i);
        echo "<th>$colname</th>";
    }
    echo "</tr>";

    // Print rows
    while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
        echo "<tr>";
        foreach ($row as $item) {
            echo "<td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}
?>

<!-- Simple HTML form -->
<form method="post">
    <label>Search: </label>
    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>">
    <input type="submit" value="Search">
</form>