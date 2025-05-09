<?php
session_start();
include 'config.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    die("405 Method Not Allowed: Request method is not POST");
}

$username = $_POST['name'] ?? '';
$password = $_POST['password'] ?? '';

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$query = "SELECT * FROM student_details WHERE name = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // Debugging: See the actual password from DB
    echo "Entered password: $password<br>";
    echo "Actual password from DB: " . $row['password'] . "<br>";

    // Compare plaintext passwords directly (Not secure)
    if ($password === $row['password']) {
        echo "Password matched!<br>";
        //$_SESSION['user_id'] = $row['id'];
        $_SESSION['name'] = $row['name'];

        header('Location: new1.html');
        exit();
    } else {
        echo "Password did NOT match!<br>";
    }
    exit();
} else {
    echo "<script>alert('User not found!'); window.location.href='signinpage.html';</script>";
}

$stmt->close();
$conn->close();
?>
