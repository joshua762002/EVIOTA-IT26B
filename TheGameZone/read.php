<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "thegamezone");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$stmt = $conn->prepare("SELECT * FROM addgames WHERE deleted_at IS NULL AND name LIKE ?");
$likeSearch = "%" . $search . "%";
$stmt->bind_param("s", $likeSearch);
$stmt->execute();
$games = $stmt->get_result();

$deletedGames = $conn->query("SELECT * FROM addgames WHERE deleted_at IS NOT NULL");

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Games</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">Games</h2>
            <div>
                <a href="create.php" class="btn btn-success me-2">Add Game</a>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>

        <form class="mb-3 d-flex" method="get">
            <input type="text" name="search" class="form-control me-2" placeholder="Search games..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
        

        <table class="table table-bordered bg-white">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Genre</th>
                    <th>Price</th>
                    <th>Age</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($games->num_rows > 0): ?>
                    <?php while($game = $games->fetch_assoc()): ?>
                    <tr>
                        <td><img src="<?= $game['image'] ?>" width="60" height="60"></td>
                        <td><?= $game['name'] ?></td>
                        <td><?= $game['genre'] ?></td>
                        <td>₱<?= number_format($game['price'], 2) ?></td>
                        <td><?= $game['age'] ?></td>
                        <td>
                            <a href="update.php?id=<?= $game['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="delete.php?id=<?= $game['id'] ?>" class="btn btn-sm btn-danger">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center text-muted">No games found.</td></tr>
                <?php endif; ?>
            </tbody>

        </table>

        <h4 class="mt-5">Deleted Games</h4>
        <table class="table table-bordered bg-white">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Restore</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($deletedGames->num_rows > 0): ?>
                    <?php while($game = $deletedGames->fetch_assoc()): ?>
                    <tr>
                        <td><?= $game['name'] ?></td>
                        <td><a href="restore.php?id=<?= $game['id'] ?>" class="btn btn-sm btn-info">Restore</a></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="2" class="text-center text-muted">No deleted games.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
