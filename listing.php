<?php 

include 'db.php';

// Initialize the profiles array
$profiles = [];

// Check if a search query and dates were submitted
$searchQuery = isset($_POST['search']) ? trim($_POST['search']) : '';
$startDate = isset($_POST['from']) ? $_POST['from'] : '';
$endDate = isset($_POST['to']) ? $_POST['to'] : '';
$dateRange = isset($_POST['date_range']) ? $_POST['date_range'] : '';

if (isset($_POST['action']) && $_POST['action'] === 'show_all') {
    // Clear filters
    $searchQuery = '';
    $startDate = '';
    $endDate = '';
    $dateRange = '';
}

// Initialize conditions array
$conditions = [];

// Construct the base SQL query
$sql = "SELECT * FROM student WHERE deleted = 0";

// Prepare the SQL statement with search conditions
if ($searchQuery) {
    $conditions[] = "(name LIKE ? OR email LIKE ? OR mobile LIKE ? OR address LIKE ?)";
}

// Date range logic based on dropdown selection
if ($dateRange) {
    $today = date('Y-m-d');
    switch ($dateRange) {
        case 'today':
            $conditions[] = "date = ?";
            $params[] = $today;
            break;
        case 'yesterday':
            $conditions[] = "date = ?";
            $params[] = date('Y-m-d', strtotime('-1 day'));
            break;
        case 'last_7_days':
            $conditions[] = "date >= ?";
            $params[] = date('Y-m-d', strtotime('-7 days'));
            break;
        case 'last_30_days':
            $conditions[] = "date >= ?";
            $params[] = date('Y-m-d', strtotime('-30 days'));
            break;
        case 'last_year':
            $conditions[] = "date >= ?";
            $params[] = date('Y-m-d', strtotime('-1 year'));
            break;
    }
}

// Add manual date conditions based on provided inputs
if ($startDate && $endDate) {
    // Both dates provided
    $conditions[] = "date BETWEEN ? AND ?";
} elseif ($startDate) {
    // Only start date provided
    $conditions[] = "date >= ?";
} elseif ($endDate) {
    // Only end date provided
    $conditions[] = "date <= ?";
}

// Combine conditions into the SQL query
if ($conditions) {
    $sql .= " AND " . implode(' AND ', $conditions);
}

// Prepare the SQL statement
$stmt = $link->prepare($sql);

// Bind parameters
$params = [];
if ($searchQuery) {
    $searchParam = '%' . $searchQuery . '%';
    $params[] = $searchParam; // for name
    $params[] = $searchParam; // for email
    $params[] = $searchParam; // for mobile
    $params[] = $searchParam; // for address
}

if ($startDate) {
    $params[] = $startDate; // Start date
}

if ($endDate) {
    $params[] = $endDate; // End date
}

// Bind parameters for date range if applicable
if ($dateRange) {
    $params[] = ($dateRange == 'today') ? $today : date('Y-m-d', strtotime('-1 day'));
}

// Determine the types of the parameters
$types = str_repeat('s', count($params)); // Assuming all parameters are strings

if ($params) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $profiles[] = $row;
        }
    } else {
        echo "No records found.";
    }
} else {
    echo "Error: " . mysqli_error($link);
}

$stmt->close();

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Profile</title>
    <link rel="stylesheet" href="listing.css" />
</head>

<body>
    <table class="navbar">
        <tr>
            <td><a href="listing.php">Profile</a></td>
            <td id="nameH"><a href="index.php">Register</a></td>
        </tr>
    </table>
    <div class="top">
    <form method="POST" action="listing.php" style="display:block;">
    <input type="search" name="search" placeholder="Search by name or email" />
    Start <input type="date" name="from" value = "<?php echo $startDate?>">
    End <input type="date" name="to" value="<?php echo $endDate ?>" >
    <select name="date_range" onchange="this.form.submit()">
        <option value="">Select Date Range</option>
        <option value="today" <?php if($dateRange == 'today') echo 'selected' ?> >Today</option>
        <option value="yesterday" <?php if($dateRange == 'yesterday') echo 'selected' ?> >Yesterday</option>
        <option value="last_7_days" <?php if($dateRange == 'last_7_days') echo 'selected' ?> >Last 7 Days</option>
        <option value="last_30_days" <?php if($dateRange == 'last_30_days') echo 'selected' ?> >Last 30 Days</option>
        <option value="last_year" <?php if($dateRange == 'last_year') echo 'selected' ?> >Last Year</option>
    </select>
    <button type="submit" name="action" value="submit_dates">Submit Dates</button>
    <button type="submit" name="action" value="show_all">Show All Records</button>
</form>
        <button class="new"><a href="index.php">New</a></button>
    </div>
    <!-- AJAX -->
        <div id="content-area" class="scrollable-container">
            <table border="1" align="center">
        <tr>
            <th colspan="3">Basic Details</th>
            <th colspan="4">Contact Details</th>
        </tr>
        <tr>
            <th>Select <input type="checkbox" onclick="toggleAllCheckboxes(this)" name="selectAll" id="selectAll"></th>
            <th>Name</th>
            <th>Picture</th>
            <th>Email</th>
            <th>Mobile</th>
            <th>Address</th>
            <th>Action</th>
        </tr>
        <?php foreach ($profiles as $profile): ?>
            <tr>
                <td><input type="checkbox" onclick="selectRow(this)" class="rowcheckbox"
                value="<?php echo htmlspecialchars($profile['id']); ?>" name="recordCheckbox"></td>
                <td>
                    <span class="name" onclick="toggleContent('id<?php echo htmlspecialchars($profile['id']); ?>')">
                        <?php echo htmlspecialchars($profile['name']); ?>
                    </span>
                    <!-- Details are hidden by default -->
                    <div class="modal" id="id<?php echo htmlspecialchars($profile['id']); ?>">
                        <div class="modal-content">
                            <span class="close"
                                onclick="toggleContent('id<?php echo htmlspecialchars($profile['id']); ?>')">&times;</span>
                            <h1>Details</h1>
                            <form>
                                <table id ="table" style="border: 1px solid #515e6d; width:600px; ">
                                    <tr>
                                        <div class="form-group">
                                            <td style="border:1px solid black">
                                                <label>Name: </label><span>
                                                    </td>
                                            <td>
                                                <?php echo htmlspecialchars($profile['name']); ?></span>
                                            </td>
                                        </div>
                                    </tr>
                                    <tr>
                                        <div class="form-group">
                                            <td style="border:1px solid black">
                                                <label>Dob: </label><span>
                                                    </td>
                                                    <td>
                                                <?php echo htmlspecialchars($profile['dob']); ?></span>
                                            </td>
                                        </div>
                                    </tr>
                                    <tr>
                                        <div class="form-group">
                                            <td style="border:1px solid black">
                                                <label>Gender: </label><span>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($profile['gender']); ?></span>
                                            </td>
                                        </div>
                                    </tr>
                                    <tr>
                                        <div class="form-group">
                                            <td style="border:1px solid black">
                                                <label>Aadhar: </label><span>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($profile['aadhar']); ?></span>
                                            </td>
                                        </div>
                                    </tr>
                                    <tr>
                                        <div class="form-group">
                                            <td style="border:1px solid black">
                                                <label>State: </label><span>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($profile['state']); ?></span>
                                            </td>
                                        </div>
                                    </tr>
                                    <tr>
                                        <div class="form-group">
                                            <td style="border:1px solid black">
                                                <label>School: </label><span>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($profile['school']); ?></span>
                                            </td>
                                        </div>
                                    </tr>
                                    <tr>
                                        <div class="form-group">
                                            <td style="border:1px solid black">
                                                <label>University/Board: </label><span>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($profile['uniboard']); ?></span>
                                            </td>
                                        </div>
                                    </tr>
                                    <tr>

                                        <div class="form-group">
                                            <td style="border:1px solid black">
                                                <label>Course: </label><span>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($profile['course']); ?></span>
                                            </td>
                                        </div>
                                    </tr>
                                    <tr>
                                        <div class="form-group">
                                            <td style="border:1px solid black">
                                                <label>Year of Passing: </label><span>
                                                    </td>
                                            <td>
                                                <?php echo htmlspecialchars($profile['yop']); ?></span>
                                            </td>
                                        </div>
                                    </tr>
                                    <tr>
                                        <div class="form-group">
                                            <td style="border:1px solid black">
                                                <label>Roll No: </label><span>
                                                    </td>
                                            <td>
                                                <?php echo htmlspecialchars($profile['roll']); ?></span>
                                            </td>
                                        </div>
                                    </tr>
                                    <tr>
                                        <div class="form-group">
                                            <td style="border:1px solid black">
                                                <label>Email: </label><span>
                                                    </td>
                                            <td>
                                                <?php echo htmlspecialchars($profile['email']); ?></span>
                                            </td>
                                        </div>
                                    </tr>
                                    <tr>
                                        <div class="form-group">
                                            <td style="border:1px solid black">
                                                <label>Mobile Number: </label><span>
                                                    </td>
                                            <td>
                                                <?php echo htmlspecialchars($profile['mobile']); ?></span>
                                            </td>
                                        </div>
                                    </tr>
                                    <tr>
                                        <div class="form-group">
                                            <td style="border:1px solid black">
                                                <label>Address: </label><span>
                                                    </td>
                                                    <td>
                                                <?php echo htmlspecialchars($profile['address']); ?></span>
                                            </td>
                                        </div>
                                    </tr>
                                    <tr>
                                        <div class="form-group">
                                            <td style="border:1px solid black">
                                                <label>Profile Picture: </label>
                                            </td>
                                            <td>
                                                <img src="include/uploads/images/<?php echo htmlspecialchars($profile['picture']); ?>"
                                                    alt="Profile Picture" height="auto" width="100px">
                                            </td>
                                        </div>
                                    </tr>
                                    <tr>
                                        <div class="form-group">
                                            <td style="border:1px solid black">
                                                <label>Resume: </label><span>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($profile['resume']) ?></span>
                                                <img src="include/uploads/resume/<?php echo htmlspecialchars($profile['resume']) ?>"
                                                    alt="" srcset="">
                                            </td>
                                        </div>
                                    </tr>
                                </table>

                                <br> <br>
                            </form>
                        </div>
                    </div>
                </td>
                <td><span class="picture"><img
                            src="include/uploads/images/<?php echo htmlspecialchars($profile['picture']); ?>"
                            alt="Profile Picture" height="100" width="50"></span></td>
                <td><span class="email"><?php echo htmlspecialchars($profile['email']); ?></span></td>
                <td><span class="mob"><?php echo htmlspecialchars($profile['mobile']); ?></span></td>
                <td><span class="address"><?php echo htmlspecialchars($profile['address']); ?></span></td>
                <td class="action">
                    <button type="button" class="edit-btn"
                        onclick="editProfile(<?php echo htmlspecialchars(json_encode($profile)); ?>)">Edit</button>
                    <button type="button" onclick="delConfirm(<?php echo htmlspecialchars($profile['id']); ?>)"
                        class="delete-btn">Delete</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <button id="deleteAll" onclick="deleteAll()">Delete All</button>
    <!-- <button id="ajax" onclick="request()">ajax</button> -->
    <script>
        //function to toggle checkboxes
        function toggleAllCheckboxes(source) {
            const checkboxes = document.querySelectorAll('.rowcheckbox');
            checkboxes.forEach(checkbox => checkbox.checked = source.checked);
            selectRow();
        }

        // selecting a row
        function selectRow() {
            const checkboxes = document.querySelectorAll('.rowcheckbox');
            let checkedCount = Array.from(checkboxes).filter(checkbox => checkbox.checked).length;
            document.getElementById('deleteAll').style.display = checkedCount >= 1 ? 'block' : 'none';
        }

        // for delete purpose
        function delConfirm(id) {
            const userConfirmed = confirm("Are you sure, You want to delete this Record??");
            if (userConfirmed) {
                fetch('update_deleted.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'id=' + encodeURIComponent(id)
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // alert(data.message);
                            location.reload();
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                    });
            } else {
                alert("Action canceled.");
            }
        }

        // Deleting all selected
        function deleteAll() {
            const selectedIds = getSelectedIds();
            if (selectedIds.length === 0) {
                alert("No records selected for deletion.");
                return;
            }

            if (confirm('Are you sure you want to delete all selected records?<?php //\n + selectedIds.join(', ') ?>')) {
                fetch('update_deleted.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'ids=' + encodeURIComponent(JSON.stringify(selectedIds))
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // alert(data.message);
                            location.reload();
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                    });
            } else {
                alert("Action canceled.");
            }
        }

        // get the ids of checkboxes selected
        function getSelectedIds() {
            const checkboxes = document.querySelectorAll('input[name="recordCheckbox"]:checked');
            return Array.from(checkboxes).map(checkbox => checkbox.value);
        }

        //editing profile
        function editProfile(profile) {
            const queryParams = new URLSearchParams({ id: profile.id }).toString();
            window.location.href = 'index.php?' + queryParams;
        }

        // show information on toggling the name
        function toggleContent(contentId) {
            const content = document.getElementById(contentId);
            console.log(contentId);
            content.style.display = content.style.display === 'none' || content.style.display === '' ? 'block' : 'none';
        }
    </script>
</body>

</html>