<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}
session_regenerate_id(true);

// Database connection
$conn = new mysqli("localhost", "root", "", "user_management");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add or update user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null; // For update
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    // $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $phone = trim($_POST['phone']);
    $description = trim($_POST['description']);
    $photoName = null;

    // Handle file upload securely
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['photo']['type'], $allowedTypes)) {
            $photoName = uniqid() . "_" . basename($_FILES['photo']['name']);
            $photoName = preg_replace('/[^A-Za-z0-9_\.-]/', '', $photoName); // Sanitize file name
            move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . $photoName);
        } else {
            echo "Invalid file type!";
        }
    }

    if ($id) {
        // Update user
        $sql = "UPDATE users SET name=?, username=?, email=?, phone=?, description=?";
        if ($photoName) {
            $sql .= ", photo=?";
        }
        $sql .= " WHERE id=?";
        $stmt = $conn->prepare($sql);
        if ($photoName) {
            $stmt->bind_param("sssssssi", $name, $username, $email, $phone, $description, $photoName, $id);
        } else {
            $stmt->bind_param("ssssssi", $name, $username, $email, $phone, $description, $id);
        }
    } else {
        // Insert user
        $stmt = $conn->prepare("INSERT INTO users (name, username, email, phone, photo, description) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $username, $email, $phone, $photoName, $description);
    
    
        // $stmt = $conn->prepare("INSERT INTO users (name, username, email, phone, photo, description) VALUES (?, ?, ?, ?, ?, ?)");
        // $stmt->bind_param("ssssss", $name, $username, $email, $phone, $photoName, $description);
        
    
    
    }

    if ($stmt->execute()) {
        echo "<script>alert('User saved successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . htmlspecialchars($stmt->error) . "');</script>";
    }
}

// Delete user
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<script>alert('User deleted successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . htmlspecialchars($stmt->error) . "');</script>";
    }
}

// Fetch all users
$result = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <title>User Dashboard</title>
</head>

<body style="background-color: white;">
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <!-- <h1>Welcome</h1> -->

        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal">Add Data</button>

        <!-- User Modal -->
        <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="modal-header">
                            <h5 class="modal-title" id="userModalLabel">User Data Information</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id">
                            <div class="mb-3">
                                <label>Name:</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Username:</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Age:</label>
                                <input type="text" name="email" class="form-control" required>
                            </div>
                            <!-- <div class="mb-3">
                                <label>Password:</label>
                                <input type="password" name="password" class="form-control" required>
                            </div> -->
                            <div class="mb-3">
                                <label>Phone:</label>
                                <input type="text" name="phone" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label>Photo:</label>
                                <input type="file" name="photo" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label>Description:</label>
                                <textarea name="description" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- User Table -->
        <h2 class="mt-4">User List</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Age</th>
                    <th>Phone</th>
                    <th>Photo</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id']; ?></td>
                            <td><?= htmlspecialchars($row['name']); ?></td>
                            <td><?= htmlspecialchars($row['username']); ?></td>
                            <td><?= htmlspecialchars($row['email']); ?></td>
                            <td><?= htmlspecialchars($row['phone']); ?></td>
                            <td>
                                <?php if ($row['photo']): ?>
                                    <img src="uploads/<?= htmlspecialchars($row['photo']); ?>" alt="Photo" width="50">
                                <?php else: ?>
                                    No Photo
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['description']); ?></td>
                            <td>
                            <!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal"> <a href="update_user_form.php?updateid= class="btn btn-sm btn-warning">Edit</a></button> -->
                            <a href="update_user_form.php?updateid=<?= $row['id']; ?>" class="btn btn-sm btn-primary" onclick="return confirm('Do you want to update?')">Edit</a>
                            <a href="?delete=<?= $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
