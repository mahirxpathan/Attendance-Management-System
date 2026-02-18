<?php
// db_viewer.php
// This file will display your database structure

// Include database connection
require_once 'includes/database.php';

// Check if we can connect to the database
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get all tables in the database
$tables_query = $conn->query("SHOW TABLES");
$tables = [];
while ($row = $tables_query->fetch_array()) {
    $tables[] = $row[0];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Structure Viewer</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: #f5f7fa;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        h1 {
            text-align: center;
            color: #4b6cb7;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #eee;
        }
        
        .database-info {
            background: #e8f4fc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .table-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .table-header {
            background: #4b6cb7;
            color: white;
            padding: 15px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .table-content {
            padding: 0;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        
        .table-content.active {
            max-height: 1000px;
        }
        
        .column-row {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 1fr;
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
        
        .column-row.header {
            font-weight: bold;
            background: #f8f9fa;
        }
        
        .no-tables {
            text-align: center;
            padding: 30px;
            color: #666;
        }
        
        .action-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #4b6cb7;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        
        .action-btn:hover {
            background: #3a559d;
        }
        
        .instructions {
            background: #fff9e6;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #ffc107;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Database Structure Viewer</h1>
        
        <div class="instructions">
            <h3>Instructions:</h3>
            <p>This page displays the structure of your database. Please share this information with me so I can help you fix the student dashboard.</p>
            <p>You can take a screenshot of this page or copy the table structures.</p>
        </div>
        
        <div class="database-info">
            <p><strong>Database Name:</strong> <?php echo $dbname; ?></p>
            <p><strong>Total Tables:</strong> <?php echo count($tables); ?></p>
        </div>
        
        <?php if (count($tables) > 0): ?>
            <?php foreach ($tables as $table): ?>
                <div class="table-card">
                    <div class="table-header" onclick="toggleTable('<?php echo $table; ?>')">
                        <span>Table: <?php echo $table; ?></span>
                        <span class="toggle-icon">▼</span>
                    </div>
                    <div class="table-content" id="<?php echo $table; ?>">
                        <?php
                        // Get table structure
                        $columns_query = $conn->query("DESCRIBE $table");
                        if ($columns_query->num_rows > 0): ?>
                            <div class="column-row header">
                                <div>Column Name</div>
                                <div>Type</div>
                                <div>Null</div>
                                <div>Key</div>
                                <div>Default</div>
                            </div>
                            <?php while ($column = $columns_query->fetch_assoc()): ?>
                                <div class="column-row">
                                    <div><?php echo $column['Field']; ?></div>
                                    <div><?php echo $column['Type']; ?></div>
                                    <div><?php echo $column['Null']; ?></div>
                                    <div><?php echo $column['Key']; ?></div>
                                    <div><?php echo $column['Default']; ?></div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div style="padding: 15px; text-align: center;">
                                No columns found or couldn't describe the table.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-tables">
                <h3>No tables found in the database.</h3>
                <p>It seems your database is empty. You might need to run the setup script.</p>
                <a href="create_tables.php" class="action-btn">Run Database Setup</a>
            </div>
        <?php endif; ?>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="index.php" class="action-btn">Back to Home</a>
        </div>
    </div>

    <script>
        function toggleTable(tableName) {
            const content = document.getElementById(tableName);
            content.classList.toggle('active');
            
            const header = content.previousElementSibling;
            const icon = header.querySelector('.toggle-icon');
            icon.textContent = content.classList.contains('active') ? '▲' : '▼';
        }
        
        // Open the first table by default
        document.addEventListener('DOMContentLoaded', function() {
            const firstTable = document.querySelector('.table-card');
            if (firstTable) {
                const firstTableHeader = firstTable.querySelector('.table-header');
                firstTableHeader.click();
            }
        });
    </script>
</body>
</html>