<?php
session_start();
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('config.php');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$k = "regno,sname,section,address,mobile,email,dob,fname,subject_value,QA,QB,QC,QD,QE,QF,QG,QH,QI,QJ,QK,QL,QZ,co1,co2,co3,co4,co5,co6";
$placeholders = rtrim(str_repeat('?,', 28), ',');

$stmt = $conn->prepare("INSERT INTO main_table ($k) VALUES ($placeholders)");

if (!$stmt) {
    echo "Prepare failed: " . $conn->error;
    exit;
}

$values = [
    $_POST['regno'] ?? '', $_POST['sname'] ?? '', $_POST['section'] ?? '', $_POST['address'] ?? '', $_POST['mobile'] ?? '',
    $_POST['email'] ?? '', $_POST['dob'] ?? '', $_POST['fname'] ?? '', $_POST['subject_value'] ?? '',
    $_POST['QA'] ?? '', $_POST['QB'] ?? '', $_POST['QC'] ?? '', $_POST['QD'] ?? '', $_POST['QE'] ?? '',
    $_POST['QF'] ?? '', $_POST['QG'] ?? '', $_POST['QH'] ?? '', $_POST['QI'] ?? '', $_POST['QJ'] ?? '',
    $_POST['QK'] ?? '', $_POST['QL'] ?? '', $_POST['QZ'] ?? '', $_POST['co1'] ?? 0, $_POST['co2'] ?? 0,
    $_POST['co3'] ?? 0, $_POST['co4'] ?? 0, $_POST['co5'] ?? 0, $_POST['co6'] ?? 0
];

$stmt->bind_param(str_repeat('s', count($values)), ...$values);

$subjectCode = $_POST['subject_value'] ?? '';
$keysForAnalysis = ['co1', 'co2', 'co3', 'co4', 'co5', 'co6'];
$valuesForAnalysis = [];


foreach ($keysForAnalysis as $key) {
    $valuesForAnalysis[] = $_POST[$key] ?? 0;
}

$columnMap = [
    1 => "strongly_disagree",
    2 => "disagree",
    3 => "neutral",
    4 => "agree",
    5 => "strongly_agree"
];

if ($stmt->execute()) {
    foreach ($valuesForAnalysis as $index => $value) {
        if ($value == 0) {
            break;
        }

        $columnToUpdate = $columnMap[$value] ?? null;
        if (!$columnToUpdate) {
            echo "Invalid value ($value) at co" . ($index + 1);
            break;
        }

        $coColumn = "co" . ($index + 1);
        $coNumber = $index + 1;

        $stmt1 = $conn->prepare("UPDATE co_table_analysis 
                                 SET $columnToUpdate = $columnToUpdate + 1 
                                 WHERE COs = ? AND sub_code = ?");
        if (!$stmt1) {
            echo "Prepare failed: " . $conn->error;
            break;
        }

        $stmt1->bind_param("is", $coNumber, $subjectCode);

        if (!$stmt1->execute()) {
            echo "Update failed for $coColumn: " . $stmt1->error;
            break;
        }

        $stmt1->close();
    }

    // Call fetchingCOs.php silently to update co_table_analysis if needed
    // ob_start(); // Suppress any output 
    // include('fetchingCOs2.php');
    // ob_end_clean(); // Clear any echoes or prints
    $conn->autocommit(false); // manual commit

$sql = "SELECT * FROM co_table_analysis WHERE sub_code = '$subjectCode'";
$result = $conn->query($sql);

if ($result && $result->num_rows == 0) {
    // while ($row = $result->fetch_assoc()) 
    // {
    //     $subjectCode = trim($row['subject_value']);

    //     if (empty($subjectCode)) {
    //         echo "<span style='color:red;'>⚠ Skipping empty subject_value</span><br>";
    //         continue;
    //     }

    //     echo "<strong>🔍 Checking table:</strong> <span style='color:blue;'>$subjectCode</span><br>";

        // Check if table exists
        $check = $conn->query("SHOW TABLES LIKE '$subjectCode'");
        if ($check->num_rows == 0) {
            echo "Table [$subjectCode] does not exist<br>";
            
        }

        $co_sql = "SELECT co_id, co FROM $subjectCode";
        $co_result = $conn->query($co_sql);

        if ($co_result === false) {
            echo " Error reading table [$subjectCode]: " . $conn->error . "<br>";
    
        }

        while ($co_row = $co_result->fetch_assoc()) {
            $co_id = (int)$co_row['co_id'];
            echo "$co_id";
            $co = trim($co_row['co']);

            if (empty($co)) {
                echo "⚠ Empty CO statement for CO$co_id in $subjectCode<br>";
                continue;
            }

            echo "➕ Adding: <b>$subjectCode</b> | <b>CO$co_id</b> | $co<br>";
            echo "$valuesForAnalysis[$co_id]";
            // echo "$columnMap[$valuesForAnalysis[$co_id]]";
            $column4=$columnMap[$valuesForAnalysis[$co_id]];

            $stmt = $conn->prepare("INSERT INTO co_table_analysis (sub_code, COs, CO_statements, $column4)
                                    VALUES (?, ?, ?,?)
                                    ON DUPLICATE KEY UPDATE
                                    CO_statements = VALUES(CO_statements)");

            if (!$stmt) {
                echo " Statement error: " . $conn->error . "<br>";
                continue;
            }
            $var1=1;

            $stmt->bind_param("sisi", $subjectCode,$co_id, $co, $var1);

            if (!$stmt->execute()) {
                echo " Insert error: " . $stmt->error . "<br>";
            } else {
                echo " CO inserted/updated<br>";
            }

            $stmt->close();
        }
    //}

    if ($conn->commit()) {
        echo "<br> All changes committed<br>";
    } else {
        echo " Commit failed<br>";
    }
} else {
    echo "⚠ No subjects found in main_table<br>";
}

    $_SESSION['success_message'] = "Submission submitted successfully";
    header("Location: new1.html");
    exit;
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>