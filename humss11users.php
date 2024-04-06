<?php
session_start();

// Include database connection
include "database.php";

// Function to sanitize input data
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Initialize variables for notifications
$notification = "";

// Handle form submission for updating user roles
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["submit_update_role"])) {
        $lrn = sanitizeInput($_POST["lrn"]);
        $selectedRole = "HUMMS"; // Set role to HUMMS

        // Update the role for the user in the database
        $sql = "UPDATE users SET role = ? WHERE lrn = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ss", $selectedRole, $lrn);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $notification = "Role updated successfully.";
        } else {
            $notification = "Error: " . mysqli_error($conn);
        }
    } elseif (isset($_POST["submit_add_user"])) {
        $lrn = sanitizeInput($_POST['lrn']);
        $role = "HUMMS"; // Set role to HUMMS

        // Prepare and execute SQL query to insert user
        $sql = "INSERT INTO users (lrn, role) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $lrn, $role);

        if (mysqli_stmt_execute($stmt)) {
            // User added successfully
            $notification = "User added successfully";
        } else {
            // Error adding user
            $notification = "Error adding user: " . mysqli_error($conn);
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    }
}

// Fetch data from the users table with role 'HUMMS'
$sql = "SELECT lrn, email, role FROM users WHERE role = 'HUMMS'";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Table (HUMMS)</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="container mt-4">
        <h2 style="font-size: medium;">Users Table (HUMMS)</h2>
        <?php if (!empty($notification)) : ?>
            <div class="alert alert-<?php echo strpos($notification, 'successfully') !== false ? 'success' : 'danger'; ?>"><?php echo $notification; ?></div>
        <?php endif; ?>
        <table class='table table-striped'>
            <thead>
                <tr>
                    <th>LRN</th>
                    <th>Email</th>
                    <th>Strand (Role)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <tr>
                        <td><?php echo $row['lrn']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['role']; ?></td>
                        <td>
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                <input type='hidden' name='lrn' value='<?php echo $row['lrn']; ?>'>
                                <input type='hidden' name='role' value='HUMMS'> <!-- Set role to HUMMS -->
                                <!-- Keep the update role button -->
                                <button type='submit' class='btn btn-primary' name='submit_update_role'>Update Role</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
                <tr>
                    <td colspan="4">
                        <h2 style="font-size: medium;">Add User:</h2>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="mb-3">
                                <label for="lrn" class="form-label">LRN:</label>
                                <input type="text" class="form-control" id="lrn" name="lrn" required>
                            </div>
                            <input type="hidden" name="role" value="HUMMS"> <!-- Default role to HUMMS -->
                            <button type="submit" class="btn btn-primary" name="submit_add_user">Add User</button>
                        </form>
                    </td>
                </tr>
            </tbody>
        </table>
        <a href="adminhome.php" class="btn btn-secondary">Back</a> <!-- Back button -->
    </div>
</body>
</html>

<?php
// Close the database connection
mysqli_close($conn);
?>
