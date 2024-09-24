<?php
session_start();

// Check if the user is an admin
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'admin')) {
    header("Location: \RECIPE\src\u_index.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "recipe_app";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pagination variables
$records_per_page = isset($_GET['records']) ? intval($_GET['records']) : 5; // Default to 5 records per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1; // Current page number

$start = ($page - 1) * $records_per_page; // Calculate starting point for fetching records

// Fetch total number of users
$sql_total = "SELECT COUNT(*) AS total FROM users";
$result_total = $conn->query($sql_total);
$row_total = $result_total->fetch_assoc();
$total_users = $row_total['total'];

// Calculate total pages
$total_pages = ceil($total_users / $records_per_page);

// Fetch users for the current page
$sql = "SELECT id, username, password, email, created_at, role, status FROM users LIMIT $start, $records_per_page";
$result = $conn->query($sql);

// Close the database connection for now
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="\RECIPE\src\test.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
<main>
    <h1>Admin Panel</h1>
    <h2>Manage Users</h2>

    <!-- Display Users in a Table -->
    <table border="1" id="userTable">
        <thead>
        <tr>
            <th>Select</th>
            <th>ID</th>
            <th>Username</th>
            <th>Password</th>
            <th>Email</th>
            <th>Created At</th>
            <th>Role</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr data-id='{$row['id']}'>
                            <td><input type='checkbox' class='select-user'></td>
                            <td class='user-id'>{$row['id']}</td>
                            <td class='user-username'>{$row['username']}</td>
                            <td class='user-password'>{$row['password']}</td>
                            <td class='user-email'>{$row['email']}</td>
                            <td class='user-created_at'>{$row['created_at']}</td>
                            <td class='user-role'>{$row['role']}</td>
                            <td class='user-status'>{$row['status']}</td>
                          </tr>";
            }
        } else {
            echo "<tr><td colspan='8'>No users found</td></tr>";
        }
        ?>
        </tbody>
    </table>

    <!-- Pagination controls -->
    <div class="pagination">
        <select id="recordsPerPage">
            <option value="5" <?php if ($records_per_page == 5) echo 'selected'; ?>>5 per page</option>
            <option value="10" <?php if ($records_per_page == 10) echo 'selected'; ?>>10 per page</option>
            <option value="20" <?php if ($records_per_page == 20) echo 'selected'; ?>>20 per page</option>
            <option value="50" <?php if ($records_per_page == 50) echo 'selected'; ?>>50 per page</option>
        </select>
        <ul>
            <?php
            // Display page numbers
            for ($i = 1; $i <= $total_pages; $i++) {
                echo "<li><a href='\RECIPE\src\Admin.php?page=$i&records=$records_per_page'>$i</a></li>";
            }
            ?>
        </ul>
    </div>
    <div id="CRUD">
        <!-- Buttons for CRUD operations -->
        <button id="alterBtn">Alter</button>
        <button id="addBtn">Add</button>
        <button id="deleteBtn">Delete</button>
        <button id="saveBtn">Save</button>
        <a href="\RECIPE\src\index.php"><button id="logout">Logout</button></a>
    </div>

    <!-- Form for adding or altering a user -->
    <div id="userForm" style="display:none;">
        <h2 id="formTitle">Add New User</h2>
        <form id="newUserForm">
            <input type="hidden" id="userId" name="userId">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="registered user">Registered User</option>
                <option value="admin">Admin</option>
            </select>
            <button type="button" id="submitUser">Submit</button>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            var selectedUserIdsToDelete = [];

            // Add new user form display
            $("#addBtn").click(function() {
                $("#formTitle").text("Add New User");
                $("#userForm").show();
                $("#newUserForm")[0].reset();
                $("#userId").val('');
            });

            // Alter user form display
            $("#alterBtn").click(function() {
                var selectedUsers = $("input.select-user:checked").closest("tr");
                if (selectedUsers.length === 1) {
                    $("#formTitle").text("Edit User");
                    $("#userForm").show();
                    var editingRow = selectedUsers;
                    $("#userId").val(editingRow.attr("data-id"));
                    $("#username").val(editingRow.find(".user-username").text());
                    $("#password").val(editingRow.find(".user-password").text());
                    $("#email").val(editingRow.find(".user-email").text());
                    $("#role").val(editingRow.find(".user-role").text());
                } else {
                    alert("Please select exactly one user to edit.");
                }
            });

            // Handle new user or edit user
            $("#submitUser").click(function() {
                var userId = $("#userId").val();
                var username = $("#username").val();
                var password = $("#password").val();
                var email = $("#email").val();
                var role = $("#role").val();

                if (userId) {
                    var editingRow = $("tr[data-id='" + userId + "']");
                    editingRow.find(".user-username").text(username);
                    editingRow.find(".user-password").text(password);
                    editingRow.find(".user-email").text(email);
                    editingRow.find(".user-role").text(role);
                } else {
                    $("#userTable tbody").append("<tr data-id='New'><td><input type='checkbox' class='select-user'></td><td class='user-id'>New</td><td class='user-username'>" + username + "</td><td class='user-password'>" + password + "</td><td class='user-email'>" + email + "</td><td class='user-created_at'>New</td><td class='user-role'>" + role + "</td><td class='user-status'>Active</td></tr>");
                }

                $("#userForm").hide();
                $("#newUserForm")[0].reset();
            });

            // Handle user deletion
            $("#deleteBtn").click(function() {
                var selectedUsers = $("input.select-user:checked").closest("tr");
                selectedUsers.each(function() {
                    selectedUserIdsToDelete.push($(this).attr("data-id"));
                });
                selectedUsers.remove(); // Remove user from HTML table
            });

            // Save changes to the database
            $("#saveBtn").click(function() {
                var users = [];
                $("#userTable tbody tr").each(function() {
                    var id = $(this).find(".user-id").text();
                    var username = $(this).find(".user-username").text();
                    var password = $(this).find(".user-password").text();
                    var email = $(this).find(".user-email").text();
                    var role = $(this).find(".user-role").text();
                    var status = $(this).find(".user-status").text();
                    if (selectedUserIdsToDelete.includes(id)) {
                        status = 'Deleted';
                    }
                    users.push({
                        id: id,
                        username: username,
                        password: password,
                        email: email,
                        role: role,
                        status: status
                    });
                });

                $.ajax({
                    url: "/RECIPE/src/save_changes.php",
                    type: 'POST',
                    data: JSON.stringify({ users: users, deleteIds: selectedUserIdsToDelete }),
                    contentType: 'application/json',
                    success: function(response) {
                        alert('Users updated successfully.');
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        alert('Error updating users.');
                    }
                });
            });

            // Update records per page
            $("#recordsPerPage").change(function() {
                var newRecordsPerPage = $(this).val();
                window.location.href = "/RECIPE/src/Admin.php?records=" + newRecordsPerPage;
            });
        });
    </script>
</main>
</body>
</html>
