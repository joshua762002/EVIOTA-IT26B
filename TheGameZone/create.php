<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $conn = new mysqli("localhost", "root", "", "thegamezone");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $uploadDir = 'uploads/';
    $imageName = basename($_FILES["image"]["name"]);
    $imagePath = $uploadDir . time() . "_" . $imageName;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath)) {
        $stmt = $conn->prepare("INSERT INTO addgames (name, genre, price, age, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdis", $_POST['name'], $_POST['genre'], $_POST['price'], $_POST['age'], $imagePath);
        $stmt->execute();
        $stmt->close();
        $conn->close();
        header("Location: read.php");
        exit;
    } else {
        echo "Image upload failed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Game</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4 bg-light">
    <div class="container">
        <h2>Add New Game</h2>
        <form method="post" enctype="multipart/form-data">
            <input name="name" class="form-control mb-2" placeholder="Name" required>
            <input name="genre" class="form-control mb-2" placeholder="Genre" required>
            <input name="price" class="form-control mb-2" type="number" step="0.01" placeholder="Price" required>
            <input name="age" class="form-control mb-2" type="number" placeholder="Age" required>
            <input name="image" class="form-control mb-2" type="file" required>
            <button class="btn btn-success">Save</button>
            <a href="read.php" class="btn btn-secondary">Back</a>
        </form>
    </div>
</body>
</html>
