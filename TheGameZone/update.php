<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "thegamezone");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $genre = $_POST['genre'];
    $price = $_POST['price'];
    $age = $_POST['age'];

    if ($_FILES['image']['error'] === 0) {
        $imagePath = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
    } else {
        $imagePath = $_POST['existing_image'];
    }

    $stmt = $conn->prepare("UPDATE addgames SET name=?, genre=?, price=?, age=?, image=? WHERE id=?");
    $stmt->bind_param("ssdisi", $name, $genre, $price, $age, $imagePath, $id);
    $stmt->execute();
    header("Location: read.php");
    exit;
}

$id = $_GET['id'];
$game = $conn->query("SELECT * FROM addgames WHERE id = $id")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Game</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4 bg-light">
    <div class="container">
        <h2>Edit Game</h2>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $game['id'] ?>">
            <input type="hidden" name="existing_image" value="<?= $game['image'] ?>">

            <input name="name" class="form-control mb-2" value="<?= $game['name'] ?>" required>
            <input name="genre" class="form-control mb-2" value="<?= $game['genre'] ?>" required>
            <input name="price" class="form-control mb-2" type="number" step="0.01" value="<?= $game['price'] ?>" required>
            <input name="age" class="form-control mb-2" type="number" value="<?= $game['age'] ?>" required>

            <label class="form-label">Current Image</label><br>
            <img src="<?= $game['image'] ?>" width="80" class="mb-2"><br>

            <label class="form-label">Change Image (optional)</label>
            <input name="image" type="file" class="form-control mb-3">

            <button class="btn btn-primary">Update</button>
            <a href="read.php" class="btn btn-secondary">Back</a>
        </form>
    </div>
</body>
</html>

