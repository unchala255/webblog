<?php
require_once 'config.php';
$tables = $conn->query("SHOW TABLES");
while ($table = $tables->fetch_array()) {
    $t = $table[0];
    echo "Table: $t\n";
    $columns = $conn->query("DESCRIBE $t");
    while ($col = $columns->fetch_assoc()) {
        echo "  - {$col['Field']} ({$col['Type']}) " . ($col['Key'] == 'PRI' ? '[PK]' : '') . "\n";
    }
}
?>
