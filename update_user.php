<?php
// Centralized database connection
$conn = new mysqli("localhost", "root", "", "user_management");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $description = trim($_POST['description']);
    $photoName = null;

    // Handle file upload securely
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileMime = mime_content_type($_FILES['photo']['tmp_name']);
        if (in_array($fileMime, $allowedTypes)) {
            $photoName = uniqid() . "_" . basename($_FILES['photo']['name']);
            $photoName = preg_replace('/[^A-Za-z0-9_\.-]/', '', $photoName); // Sanitize filename
            move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . $photoName);
        } else {
            echo "Invalid file type!";
            exit;
        }
    }

    // Update user data
    $sql = "UPDATE users SET name=?, username=?, email=?, phone=?, description=?";
    if ($photoName) {
        $sql .= ", photo=?";
    }
    $sql .= " WHERE id=?";

    $stmt = $conn->prepare($sql);

    if ($photoName) {
        $stmt->bind_param("ssssssi", $name, $username, $email, $phone, $description, $photoName, $id);
    } else {
        $stmt->bind_param("sssssi", $name, $username, $email, $phone, $description, $id);
    }

    if ($stmt->execute()) {
        header("Location: index.php?success=1");
        exit();
    } else {
        echo "Error: " . htmlspecialchars($stmt->error);
    }
    $stmt->close();
}
$conn->close();
?>
