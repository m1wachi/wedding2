<?php
session_start();

// Database connection details
$host = 'localhost'; 
$port = 3307;
$dbName = 'user_auth'; 
$user = 'root';
$password = '';

//data source name
$dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8";

// CREATE PDO
try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

// Handle edit and delete guest operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['edit_guest'])) {
        $id = $_POST['id'];
        $rsvp_status = $_POST['rsvp_status'];
        $no_of_guest = $_POST['no_of_guest'];

        if ($rsvp_status === "No") {
            $no_of_guest = 0;
        } else {
        
            if ($no_of_guest < 1) {
                $no_of_guest = 1;
            }
        }

        $sql = "UPDATE guest SET rsvp_status = ?, no_of_guest = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$rsvp_status, $no_of_guest, $id]);
    }

    if (isset($_POST['delete_guest'])) {
        $id = $_POST['id'];

        $sql = "DELETE FROM guest WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
    }
}

$sql = "SELECT id, fullname, email, rsvp_status, no_of_guest FROM guest";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$guests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total number of guests
$totalGuests = 0;
foreach ($guests as $guest) {
    $totalGuests += $guest['no_of_guest'];
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bride & Groom Page</title>
    <style>
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        h1 {
            color: #e91e63;
            text-align: center;
        }
        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 6px 9px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #d81b60;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        tr:hover {
            background-color: #e9ecef;
        }
        .button {
            padding: 5px 10px;
            background-color: #c126646e;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }
        .button:hover {
            background-color: #d81b60;
        }
        .delete-button {
            background-color: #dc3545;
        }
        .delete-button:hover {
            background-color: #c82333;
        }

        /* Popup Modal Styles */
        #deleteModal {
            display: none;
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 10;
            width: 300px;
            text-align: center;
        }

        #overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 5;
        }

        .button {
            padding: 10px 20px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
        }

        .confirm-delete {
            background-color: #dc3545;
            color: white;
        }

        .cancel-delete {
            background-color: #6c757d;
            color: white;
        }

        
        #editForm {
            display: none;
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 10;
            width: 300px;
        }
        #editForm h2 {
            margin: 0 0 20px 0;
            text-align: center;
        }
        input, select, button {
            display: block;
            margin: 10px auto;
            padding: 10px;
            width: 80%;
            font-size: 16px;
        }
        button {
            width: auto;
        }

        /* Background overlay */
        #overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 5;
        }

        .logout-button {
            position: absolute;
            bottom: 20px;
            right: 20px;
            background-color: #d81b60;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            text-decoration: none;
            border-radius: 5px;
            z-index: 10;
        }
        .logout-button:hover {
            background-color: #e91e63;
        }
        .error {
            color: red;
        }
        footer {
            position: absolute;
            bottom: 0;
            right: 0;
            padding: 10px;
            background-color: #fff; /* Optional: to match the body */
            box-shadow: 0 -1px 5px rgba(0,0,0,0.1);
        }

        .share-button {
    position: absolute;
    bottom: 9px;
    right: 130px;
    background-color: #4caf50;
    color: white;
    border: none;
    padding: 10px 20px;
    cursor: pointer;
    text-decoration: none;
    border-radius: 5px;
    z-index: 10;
}
.share-button:hover {
    background-color: #45a049;
}

/* Style for the share options */
.share-options {
    display: none;
    position: absolute;
    bottom: 60px;
    right: 210px;
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}
.share-options a {
    display: block;
    padding: 8px 0;
    text-decoration: none;
    color: #333;
}
.share-options a:hover {
    color: #e91e63;
}

/* Style for the "Link copied!" pop-up */
.shared-message {
    display: none;
    position: fixed;
    bottom: 100px;
    left: 50%;
    transform: translateX(-50%);
    background-color: #4caf50;
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}


    </style>
</head>
<body>
    <h1>Bride & Groom Page</h1>

    <h2>Guest List (Total Guests: <?= htmlspecialchars($totalGuests) ?>)</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>RSVP Status</th>
                <th>Number of Guests</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($guests) > 0): ?>
                <?php foreach ($guests as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['fullname']) ?></td>
                        <td><?= htmlspecialchars($row['rsvp_status']) ?></td>
                        <td><?= htmlspecialchars($row['no_of_guest']) ?></td>
                        <td>
                            <button class="button" onclick="showEditForm('<?= htmlspecialchars($row['id']) ?>', '<?= htmlspecialchars($row['rsvp_status']) ?>', '<?= htmlspecialchars($row['no_of_guest']) ?>')">Edit</button>
                            <button class="button delete-button" onclick="showDeleteModal('<?= htmlspecialchars($row['id']) ?>')">Delete</button>                       
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No guest information available</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div id="deleteModal">
        <h2>Confirm Delete</h2>
        <p>Are you sure you want to delete this guest?</p>
        <form id="deleteForm" method="POST">
            <input type="hidden" name="id" id="deleteId">
            <button type="button" class="button cancel-delete" onclick="hideDeleteModal()">Cancel</button>
            <button type="submit" name="delete_guest" class="button confirm-delete">Delete</button>
        </form>
    </div>

    <!-- Background overlay -->
    <div id="overlay" onclick="hideDeleteModal()"></div>

    <!-- Edit form (pop-up) -->
    <div id="editForm">
        <h2>Edit Guest</h2>
        <form method="POST">
            <input type="hidden" name="id" id="editId">
            <label for="editRsvpStatus">RSVP Status:</label>
            <select name="rsvp_status" id="editRsvpStatus" required>
                <option value="Yes">Yes</option>
                <option value="No">No</option>
                <option value="Maybe">Maybe</option>
            </select>
            <label for="editNoOfGuest">Number of Guests:</label>
            <input type="number" name="no_of_guest" id="editNoOfGuest" required min="0">
            <button type="submit" name="edit_guest" class="button">Save Changes</button>
        </form>
    </div>

    <br>
    <br>
    <br>


    <!-- Background overlay -->
    <div id="overlay" onclick="hideEditForm()"></div>

    <script>
        // Show the edit form as a modal
        function showEditForm(id, rsvp_status, no_of_guest) {
            document.getElementById('editId').value = id;
            document.getElementById('editRsvpStatus').value = rsvp_status;
            document.getElementById('editNoOfGuest').value = no_of_guest;
            document.getElementById('editForm').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        }

        // Hide the edit form
        function hideEditForm() {
            document.getElementById('editForm').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        }

        
        // Show the delete modal
        function showDeleteModal(id) {
            document.getElementById('deleteId').value = id;
            document.getElementById('deleteModal').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        }

        // Hide the delete modal
        function hideDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        }
 

    // Function to show share options
    function showShareOptions() {
        document.getElementById('shareOptions').style.display = 'block';
    }

    // Function to show "Link shared!" message
    function showPopup() {
        document.getElementById('sharedMessage').style.display = 'block';
        setTimeout(function() {
            document.getElementById('sharedMessage').style.display = 'none';
        }, 2000); // Hide after 2 seconds
    }

    </script>

<footer>
    <a href="logout.php" class="logout-button">Logout</a>
    <button onclick="showShareOptions()" class="share-button">Share RSVP</button>
    
    <!-- Share options next to share button -->
    <div id="shareOptions" class="share-options">
        <a href="https://wa.me/?text=You're%20invited%20to%20Remy%20and%20Melati's%20wedding!%20RSVP%20here:%20http://yourwebsite.com/Mainpage.html" target="_blank">WhatsApp</a>
        <a href="mailto:?subject=Wedding%20RSVP&body=You're%20invited%20to%20Remy%20and%20Melati's%20wedding!%20RSVP%20here:%20http://yourwebsite.com/Mainpage.html">Email</a>
        <a href="#" onclick="showPopup()">Copy link</a>
    </div>

    <div id="sharedMessage" class="shared-message">Link copied!</div>
</footer>


</body>
</html>