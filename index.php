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
    $age = trim($_POST['age']);
    $phone = trim($_POST['phone']);
    $description = trim($_POST['description']);
    $photoName = null;

    // Handle photo upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        // Get file content and encode it to Base64
        $fileContent = file_get_contents($_FILES['photo']['tmp_name']);
        $photoName = base64_encode($fileContent);
    }

    if ($id) {
        // Update user
        $sql = "UPDATE users SET name=?, username=?, age=?, phone=?, description=?";
        if ($photoName) {
            $sql .= ", photo=?";
        }
        $sql .= " WHERE id=?";
        $stmt = $conn->prepare($sql);
        if ($photoName) {
            $stmt->bind_param("sssssssi", $name, $username, $age, $phone, $description, $photoName, $id);
        } else {
            $stmt->bind_param("ssssssi", $name, $username, $age, $phone, $description, $id);
        }
    } else {
        // Insert user
        $stmt = $conn->prepare("INSERT INTO users (name, username, age, phone, photo, description) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $username, $age, $phone, $photoName, $description);
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
    <title>Responsive User Dashboard</title>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .btn {
            margin: 2px 0;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center">
            <h2>User Dashboard</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal">
                Add User
            </button>
        </div>

        <!-- User Modal -->
        <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="" method="POST" enctype="multipart/form-data" id="userForm" onsubmit="return validateForm()">
                        <div class="modal-header">
                            <h5 class="modal-title" id="userModalLabel">User Data Information</h5>
                            <button type="button" class="btn-close text-danger" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id">
                            <div class="mb-3">
                                <label for="name">Name:</label>
                                <input type="text" id="name" name="name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="username">Username:</label>
                                <input type="text" id="username" name="username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="age">Age:</label>
                                <input type="number" id="age" name="age" class="form-control" required min="1">
                            </div>
                            <div class="mb-3">
                                <label for="phone">Phone:</label>
                                <input type="text" id="phone" name="phone" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="photo" class="form-label">Choose a Photo:</label>
                                <input type="file" name="photo" id="photo" class="form-control" accept="image/*">
                            </div>
                            <div class="mb-3">
                                <label for="description">Description:</label>
                                <textarea id="description" name="description" class="form-control"></textarea>
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
        <div class="table-responsive mt-4">
            <table class="table table-bordered table-hover">
                <thead class="table-secondary">
                    <tr>
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
                                <td><?= htmlspecialchars($row['name']); ?></td>
                                <td><?= htmlspecialchars($row['username']); ?></td>
                                <td><?= htmlspecialchars($row['age']); ?></td>
                                <td><?= htmlspecialchars($row['phone']); ?></td>
                                <td>
                                    <?php if ($row['photo']): ?>
                                        <img src="data:image/jpeg;base64,<?= htmlspecialchars($row['photo']); ?>" alt="Photo" class="img-fluid rounded" width="50" height="50">
                                    <?php else: ?>
                                        No Photo
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['description']); ?></td>
                                <td>
                                    <a href="update_user_form.php?updateid=<?= $row['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <a href="?delete=<?= $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
       function validateForm() {
        const name = document.getElementById("name").value;
        const username = document.getElementById("username").value;
        const age = document.getElementById("age").value;
        const phone = document.getElementById("phone").value;

        // Check if Name and Username are filled
        if (!name || !username) {
            alert("Name and Username are required.");
            return false;
        }

        // Check if Age is a positive number
        if (age <= 0 || isNaN(age)) {
            alert("Please enter a valid age.");
            return false;
        }

        // Phone validation (simple check for 10 digits)
        const phoneRegex = /^[0-9]{11}$/;
        if (!phone.match(phoneRegex)) {
            alert("Phone number must be 11 digits.");
            return false;
        }

        return true;
    }
    </script>
</body>

</html>
