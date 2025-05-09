<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Outcomes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear ;
            background: linear-gradient(to bottom, #c6e2ff, #c6e2ff);
            padding: 20px;
            text-align: center;
        }
        table {
            width: 80%;
            border-collapse: collapse;
            margin: 20px auto;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Course Outcomes for Selected Subject</h1>

    <?php
    // Database connection
    $servername = "localhost"; // Change as needed
    $username = "root"; // Change as needed
    $password = ""; // Change as needed
    $dbname = "student_feedback"; // Change as needed

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) 
    {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if a subject code is selected
    if (isset($_POST['sub_code']) && !empty($_POST['sub_code']))
     {
        $sub_code = $_POST['sub_code'];
        echo "Selected Subject Code: " . htmlspecialchars($sub_code) . "<br>"; // Debugging line

        // Prepare and execute the SQL query
        $sql = "SELECT COs, CO_statements, strongly_agree, agree, neutral, disagree, strongly_disagree, avg, avg(3) FROM co_table_analysis WHERE sub_code = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $sub_code);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if there are results
        if ($result->num_rows > 0) {
            echo '<table>';
            echo '<tr>
                    <th>COs</th>
                    <th>CO Statements</th>
                    <th>Strongly Agree</th>
                    <th>Agree</th>
                    <th>Neutral</th>
                    <th>Disagree</th>
                    <th>Strongly Disagree</th>
                    <th>Average</th>
                    <th>Average (3)</th>
                  </tr>';

            // Output data of each row
            while ($row = $result->fetch_assoc()) {
                echo '<tr>
                        <td>' . htmlspecialchars($row['COs']) . '</td>
                        <td>' . htmlspecialchars($row['CO_statements']) . '</td>
                        <td>' . htmlspecialchars($row['strongly_agree']) . '</td>
                        <td>' . htmlspecialchars($row['agree']) . '</td>
                        <td>' . htmlspecialchars($row['neutral']) . '</td>
                        <td>' . htmlspecialchars($row['disagree']) . '</td>
                        <td>' . htmlspecialchars($row['strongly_disagree']) . '</td>
                        <td>' . htmlspecialchars($row['avg']) . '</td>
                        <td>' . htmlspecialchars($row['avg(3)']) . '</td>
                      </tr>';
            }
            echo '</table>';
        } else {
            echo '<p>No results found for the selected subject code.</p>';
        }

        // Close the statement
        $stmt->close();
    } 
    else {
        echo '<p>Please select a subject code to view the Course Outcomes.</p>';
    }

    // Close the database connection
    $conn->close();
    ?>
</body>
</html>