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

// Handle form submission for adding a user to the 'registrar' table
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_add_registrar"])) {
    $lrn = sanitizeInput($_POST['lrn']);
    $role = "standby-ABM12"; // Set role to standby-ABM12

    // Check if LRN already exists in users table
    $sql_check = "SELECT * FROM users WHERE lrn = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "s", $lrn);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);
    $num_rows = mysqli_stmt_num_rows($stmt_check);
    mysqli_stmt_close($stmt_check);

    if ($num_rows == 0) {
        // LRN doesn't exist in users table, proceed to insert into registrar table
        $sql = "INSERT INTO registrar (lrn, role) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $lrn, $role);

        if (mysqli_stmt_execute($stmt)) {
            // User added to registrar table successfully
            $notification = "User added to registrar table successfully with role '$role'";
        } else {
            // Error adding user to registrar table
            $notification = "Error adding user to registrar table: " . mysqli_error($conn);
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        // LRN already exists in users table
        $notification = "LRN already exists in users table";
    }
}

// Handle form submission for updating a user's role to 'ABM12'
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_update_role"])) {
    $lrn = sanitizeInput($_POST['lrn']);

    // Update the role for the user in the registrar table
    $sql = "UPDATE registrar SET role = 'ABM12' WHERE lrn = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $lrn);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $notification = "Role updated successfully.";
    } else {
        $notification = "Error updating role: " . mysqli_error($conn);
    }
}

// Fetch data from the registrar table for ABM12 role and standby-ABM12 role
$sql = "SELECT lrn, role FROM registrar WHERE role IN ('ABM12', 'standby-ABM12')";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Table (ABM12)</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="container mt-4">
        <h2 style="font-size: medium;">Registrar Table (ABM12)</h2>
        <?php if (!empty($notification)) : ?>
            <div class="alert alert-<?php echo strpos($notification, 'successfully') !== false ? 'success' : 'danger'; ?>"><?php echo $notification; ?></div>
        <?php endif; ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="lrn">LRN:</label>
                <input type="text" class="form-control" id="lrn" name="lrn" required>
            </div>
            <button type="submit" class="btn btn-primary" name="submit_add_registrar">Add User to Registrar</button>
        </form>
        <table class='table table-striped mt-4'>
            <thead>
                <tr>
                    <th>LRN</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <tr>
                        <td><?php echo $row['lrn']; ?></td>
                        <td><?php echo $row['role']; ?></td>
                        <td>
                            <?php if ($row['role'] == 'standby-ABM12') : ?>
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <input type='hidden' name='lrn' value='<?php echo $row['lrn']; ?>'>
                                    <button type='submit' class='btn btn-primary' name='submit_update_role'>Update Role</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
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
