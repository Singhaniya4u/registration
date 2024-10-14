<?php
include 'db.php'; // Ensure you include your database connection first

$name = $dob = $gender = $aadhar = $state = $school = $uniboard = '';
$course = $yop = $roll = $email = $mob = $address = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collecting data from form submission
    $name = $_POST['name'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $aadhar = $_POST['aadhar'];
    $state = $_POST['state'];
    $school = $_POST['school'];
    $uniboard = isset($_POST['uniboard']) ? implode(", ", $_POST['uniboard']) : '';
    $course = $_POST['course'];
    $yop = $_POST['yop'];
    $roll = $_POST['roll'];
    $email = $_POST['email'];
    $mob = $_POST['mob'];
    $address = $_POST['address'];

    // Handle file uploads
    $target_dir = "uploads/";
    $target_file_pic = $target_dir . basename($_FILES["pic"]["name"]);
    $target_file_resume = $target_dir . basename($_FILES["resume"]["name"]);

    $upload_error = false;

    // Picture upload
    if ($_FILES["pic"]["error"] == UPLOAD_ERR_OK && move_uploaded_file($_FILES["pic"]["tmp_name"], $target_file_pic)) {
        echo "Profile picture uploaded successfully.<br>";
    } else {
        echo "Error uploading profile picture.<br>";
        $upload_error = true;
    }

    // Resume upload
    if ($_FILES["resume"]["error"] == UPLOAD_ERR_OK && move_uploaded_file($_FILES["resume"]["tmp_name"], $target_file_resume)) {
        echo "Resume uploaded successfully.<br>";
    } else {
        echo "Error uploading resume.<br>";
        $upload_error = true;
    }

    if (!$upload_error) {
        // Insert or update database record here
        // SQL statement based oncreating or editing a profile
        // Example for inserting:
        $sql = "INSERT INTO student (name, dob, gender, aadhar, state, school, uniboard, course, yop, roll, email, mob, address, picture, resume) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $link->prepare($sql);
        $stmt->bind_param("ssssssssssssss", $name, $dob, $gender, $aadhar, $state, $school, $uniboard, $course, $yop, $roll, $email, $mob, $address, $target_file_pic, $target_file_resume);

        if ($stmt->execute()) {
            echo "<h2>Registration Successful!</h2>";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Fetching existing data if ID is provided
$id = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : '';
if ($id) {
    $sql = "SELECT * FROM student WHERE id = ?";
    $stmt = $link->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $profiles = $result->fetch_assoc();
        // Populate form fields with existing data
        $name = $profiles['name'];
        $dob = $profiles['dob'];
        $gender = $profiles['gender'];
        $aadhar = $profiles['aadhar'];
        $state = $profiles['state'];
        $school = $profiles['school'];
        $uniboard = explode(", ", $profiles['uniboard']);
        $course = $profiles['course'];
        $yop = $profiles['yop'];
        $roll = $profiles['roll'];
        $email = $profiles['email'];
        $mob = $profiles['mobile'];
        $address = $profiles['address'];
        $picture = $profiles['picture'] ?? '';
        $resume = $profiles['resume'] ?? '';
    } else {
        echo "No records found.";
    }
    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Registration Form</title>
    <link rel="stylesheet" href="style.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <?php include 'db.php' ?>
    <script>
            const id = <?php echo isset($_GET['id']) ? json_encode($_GET['id']) : 'null'; ?>;

            function handleSubmit() {
            if (id === null) {
        // If ID is not present, perform validation
                return doCheck();
            }
    // If ID is present, skip validation and allow form submission
            return true;
            }

        function doCheck() {
            const ele = $('input[name="gender"]');
            const pictureInput = $('#pic');
            const emailInput = $('#email');
            const emailValue = emailInput.val();
            const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com|org|net|edu|gov|mil|co|info|io|biz)$/;
            const resumeInput = $('#resume');

            // Check required fields

            if ($('#name').val() === '') {
                displayError("Please Enter your name", $('#name'));
                return false;
            } else if ($('#dob').val() === '') {
                displayError("Please Enter your Date of Birth", $('#dob'));
                return false;
            } else if (!ele.is(':checked')) {
                displayError("Please mention your Gender", $('#male'));
                return false;
            } else if ($("#aadhar").val() === '') {
                displayError("Please enter your Aadhar no.", $("#aadhar"));
                return false;
            } else if ($("#state").val() === "select") {
                displayError("Please enter your state", $("#state"));
                return false;
            }

            // Picture file validation
            const picturePath = pictureInput.val();
            const allowedExtensionPic = /\.(jpg|jpeg|png|gif)$/i;
            if (picturePath === '' || !allowedExtensionPic.exec(picturePath)) {
                displayError("Please upload an image file (jpg, jpeg, png, gif).", pictureInput);
                return false;
            }

            const iFile = pictureInput[0].files[0];
            if (iFile) {
                const iFileSizeKB = (iFile.size / 1024).toFixed(2);
                if (iFileSizeKB > 500) {
                    displayError("Upload the picture under 400 KB", pictureInput);
                    return false;
                }
            }

            // Other validations
            if ($("#school").val() === '') {
                displayError("Please add your school/college name", $("#school"));
                return false;
            } else if ($('input[name="uniboard[]"]:checked').length === 0) {
                displayError("Please select your University or Board", $('input[name="uniboard[]"]').first());
                return false;
            } else if ($("#course").val() === 'select') {
                displayError("Please mention your course name", $("#course"));
                return false;
            } else if ($("#yop").val() === '' || $('#yop').val() > 2026) {
                displayError("Enter Valid Year of Passing", $("#yop"));
                return false;
            } else if ($("#roll").val() === '') {
                displayError("Enter roll no.", $("#roll"));
                return false;
            } else if (!emailPattern.test(emailValue)) {
                displayError("Please enter a valid email address (e.g., .com, .org, .net).", emailInput);
                return false;
            } else if ($("#mob").val() === '') {
                displayError("Enter mobile no.", $("#mob"));
                return false;
            } else if ($("#address").val() === '') {
                displayError("Enter your address", $("#address"));
                return false;
            }

            // Resume file validation
            const filePath = resumeInput.val();
            const allowedExtensions = /\.pdf$/i;
            if (!filePath) {
                displayError("Please upload a resume.", resumeInput);
                return false;
            } else if (!allowedExtensions.exec(filePath)) {
                displayError("Please upload your resume in PDF format.", resumeInput);
                return false;
            }

            const file = resumeInput[0].files[0];
            if (file) {
                const fileSizeKB = (file.size / 1024).toFixed(2);
                if (fileSizeKB > 1024) { 
                    displayError("Upload the resume under 1 MB", resumeInput);
                    return false;
                }
            }

            return true;
        }


        function displayError(message, inputElement) {
            alert(message);
            inputElement.focus();
        }


    </script>
</head>

<body>
    <nav>
        <ul>
            <li><a href="listing.php">Profile</a></li>
            <li><a href="index.php">Register</a></li>
        </ul>
    </nav>
    <h2>Registration Form</h2>
    <br />

    <!-- <form onsubmit="return doCheck();" action="include/upload.php" id="myForm" enctype="multipart/form-data" -->
    <form onsubmit="return handleSubmit();" action="register.php" id="myForm" enctype="multipart/form-data" method="post">
        <input type="hidden" name="created_by" value="<?php echo htmlspecialchars($creator_name); ?>" />
        <input type="hidden" name="id" value="<?php echo isset($profiles['id']) ? $profiles['id'] : ''; ?>" />
        <div class="basic-details">
            <table>
                <tr class="head">
                    <td colspan="2">
                        <h3>Basic Details</h3>
                    </td>
                </tr>
                <tr>
                    <td><label for="name">Name:</label></td>
                    <td><input type="text" id="name" value="<?php echo $name ?>" name="name" /><br /></td>
                </tr>
                <tr>
                    <td><label for="dob">Date of Birth:</label></td>
                    <td><input type="date" id="dob" value="<?php echo $dob ?>" name="dob" /> <br /></td>
                </tr>
                <tr>
                    <td><label>Gender:</label></td>
                    <td><input class="gender" type="radio" id="male" name="gender" value="Male" <?php echo ($gender === 'male') ? 'checked' : ''; ?> />
                        <label for="male">Male</label>
                        <input class="gender" type="radio" id="female" name="gender" value="Female" <?php echo ($gender === 'female') ? 'checked' : ''; ?> />
                        <label for="female">Female</label><br />
                    </td>
                </tr>
                <tr>
                    <td><label for="aadhar">Aadhar no:</label></td>
                    <td><input type="text" id="aadhar" placeholder="1234 5678 2547" value="<?php echo $aadhar; ?>"
                            maxlength="12" name="aadhar"></td>
                </tr>
                <tr>
                    <td><label for="state">State:</label></td>
                    <td>
                        <select name="state" id="state">
                            <option value="select" <?php echo ($state === 'select') ? 'selected' : ''; ?>>Select State
                            </option>
                            <option value="Bihar" <?php echo ($state === 'Bihar') ? 'selected' : ''; ?>>Bihar</option>
                            <option value="West Bengal" <?php echo ($state === 'West Bengal') ? 'selected' : ''; ?>>West
                                Bengal</option>
                            <option value="Uttar Pradesh" <?php echo ($state === 'Uttar Pradesh') ? 'selected' : ''; ?>>
                                Uttar Pradesh</option>
                            <option value="Mumbai" <?php echo ($state === 'Mumbai') ? 'selected' : ''; ?>>Mumbai</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="pic">Picture:</label></td>
                    <td><input type="file" name="pic" id="pic" accept=".jpg, .jpeg, .png" />
                        <?php if ($picture): ?><br>To update your image choose another.
                            <br><img src="include/uploads/images/<?php echo htmlspecialchars($picture); ?>"
                                alt="Profile Picture" width="100" />
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>

        <div class="educational-details">
            <table>
                <tr class="head">
                    <td colspan="2">
                        <h3>Educational Details</h3>
                    </td>
                </tr>
                <tr>
                    <td><label for="school">School/College:</label></td>
                    <td><input type="text" id="school" value="<?php echo $school; ?>" name="school" /> <br /></td>
                </tr>
                <tr>
                    <td><label>University/Board:</label></td>
                    <td>
                        <input class="board" type="checkbox" id="cbse" name="uniboard[]" value="cbse" <?php echo (in_array('cbse', $uniboard)) ? 'checked' : ''; ?> />
                        <label for="cbse">C.B.S.E</label>
                        <input class="board" type="checkbox" id="icse" name="uniboard[]" value="icse" <?php echo (in_array('icse', $uniboard)) ? 'checked' : ''; ?> />
                        <label for="icse">I.C.S.E</label>
                        <input class="board" type="checkbox" id="wbbse" name="uniboard[]" value="wbbse" <?php echo (in_array('wbbse', $uniboard)) ? 'checked' : ''; ?> />
                        <label for="wbbse">W.B.B.S.E</label>
                    </td>
                </tr>
                <tr>
                    <td><label for="course">Course:</label></td>
                    <td>
                        <select name="course" id="course">
                            <option value="" <?php echo ($course === '') ? 'selected' : ''; ?>>Select</option>
                            <option value="Bachelor of Engineering" <?php echo ($course === 'Bachelor of Engineering') ? 'selected' : ''; ?>>Bachelor of Engineering</option>
                            <option value="Bachelor of Computer Application" <?php echo ($course === 'Bachelor of Computer Application') ? 'selected' : ''; ?>>Bachelor of Computer Application</option>
                            <option value="Intermediate with Science" <?php echo ($course === 'Intermediate with Science') ? 'selected' : ''; ?>>Intermediate with Science</option>
                            <option value="Intermediate with Commerce" <?php echo ($course === 'Intermediate with Commerce') ? 'selected' : ''; ?>>Intermediate with Commerce</option>
                            <option value="Intermediate with Arts" <?php echo ($course === 'Intermediate with Arts') ? 'selected' : ''; ?>>Intermediate with Arts</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="yop">Year of Passing:</label></td>
                    <td><input type="text" id="yop" value="<?php echo $yop; ?>" name="yop" /></td>
                </tr>
                <tr>
                    <td><label for="roll">Roll no:</label></td>
                    <td><input type="text" id="roll" value="<?php echo $roll; ?>" name="roll" minlength="1"
                            maxlength="8" placeholder="Roll no./Registration Number" /></td>
                </tr>
            </table>
        </div>

        <div class="contact-details">
            <table>
                <tr class="head">
                    <td colspan="2">
                        <h3>Contact Details</h3>
                    </td>
                </tr>
                <tr>
                    <td><label for="email">Email:</label></td>
                    <td><input type="email" value="<?php echo $email; ?>" id="email" name="email" /></td>
                </tr>
                <tr>
                    <td><label for="mob">Mobile:</label></td>
                    <td><input type="tel" id="mob" name="mob" value="<?php echo $mob; ?>" pattern="\d{10}"
                            maxlength="10" placeholder="Enter 10 digit number" /></td>
                </tr>
                <tr>
                    <td><label for="address">Address:</label></td>
                    <td><textarea id="address" name="address"><?php echo $address; ?></textarea></td>
                </tr>
                <tr>
                    <td><label for="resume">Resume:</label></td>
                    <td><input type="file" name="resume" id="resume" /><span id="fileSizeDisplay"></span>
                    <?php if ($resume): ?><br>To update your resume choose another.
                            <br><img src="/Shivnath/Image/pdf.png" alt="pdf" style="width:100px; height:auto; margin-bottom: 5px;">
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>

        <br>
        <center>
            <button id="register" type="submit">Submit</button>
            <button id="reset" type="reset">Reset</button>
        </center>
        <br>
    </form>
</body>

</html>