<?php
// Centralized database connection
$conn = new mysqli("localhost", "root", "", "user_management");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user details for the form
$id = intval($_GET['updateid']);
$result = $conn->query("SELECT * FROM users WHERE id=$id");
$user = $result->fetch_assoc();
if (!$user) {
    die("User not found!");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <title>Update User</title>
</head>
<body>
    <?php include 'navbar.php' ?>
<div class="container mt-5">
    <h2>Update User</h2>
    <form action="update_user.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']); ?>">

        <div class="mb-3">
            <label for="name" class="form-label">Name:</label>
            <input type="text" name="name" class="form-control" id="name" value="<?= htmlspecialchars($user['name']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="username" class="form-label">Username:</label>
            <input type="text" name="username" class="form-control" id="username" value="<?= htmlspecialchars($user['username']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Age:</label>
            <input type="text" name="email" class="form-control" id="email" value="<?= htmlspecialchars($user['email']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Phone:</label>
            <input type="text" name="phone" class="form-control" id="phone" value="<?= htmlspecialchars($user['phone']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="photo" class="form-label">Photo:</label>
            <input type="file" name="photo" class="form-control" id="photo">
            <?php if ($user['photo']): ?>
                <img src="uploads/<?= htmlspecialchars($user['photo']); ?>" alt="Current Photo" class="img-thumbnail mt-2" width="100">
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description:</label>
            <textarea name="description" class="form-control" id="description" rows="4"><?= htmlspecialchars($user['description']); ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="user_list.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
