<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Correctly capture form data
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null; // Capture ID for update
    $name = mysqli_real_escape_string($link, $_POST['name']);
    $creator_name = mysqli_real_escape_string($link, $_POST['name']);
    $modifier_name = mysqli_real_escape_string($link, $_POST['name']);
    $dob = mysqli_real_escape_string($link, $_POST['dob']);
    $gender = mysqli_real_escape_string($link, $_POST['gender']);
    $aadhar = mysqli_real_escape_string($link, $_POST['aadhar']);
    $email = mysqli_real_escape_string($link, $_POST['email']);
    $mob = mysqli_real_escape_string($link, $_POST['mob']);
    $address = mysqli_real_escape_string($link, $_POST['address']);
    $school = mysqli_real_escape_string($link, $_POST['school']);
    $uniboard = isset($_POST['uniboard']) ? mysqli_real_escape_string($link, implode(", ", $_POST['uniboard'])) : '';
    $course = mysqli_real_escape_string($link, $_POST['course']);
    $yop = mysqli_real_escape_string($link, $_POST['yop']);
    $roll = mysqli_real_escape_string($link, $_POST['roll']);
    $state = mysqli_real_escape_string($link, $_POST['state']);
    
    // Handle file uploads
    $target_dir_image = "/var/www/html/Shivnath/html/include/uploads/images/";
    $target_dir_resume = "/var/www/html/Shivnath/html/include/uploads/resume/";

    $new_file_name_image = null;
    $new_file_name_resume = null;

    // Picture upload
    if (isset($_FILES["pic"]) && $_FILES["pic"]["error"] == UPLOAD_ERR_OK) {
        $file_extension_image = pathinfo($_FILES["pic"]["name"], PATHINFO_EXTENSION);
        $date_time = date('dmY_His');
        $new_file_name_image = "image_{$date_time}_" . uniqid('', true) . '.' . $file_extension_image;
        $target_file_pic = $target_dir_image . $new_file_name_image;

        if (!move_uploaded_file($_FILES["pic"]["tmp_name"], $target_file_pic)) {
            echo "Error uploading profile picture.<br>";
        }
    }

    // Resume upload
    if (isset($_FILES["resume"]) && $_FILES["resume"]["error"] == UPLOAD_ERR_OK) {
        $file_extension_resume = pathinfo($_FILES["resume"]["name"], PATHINFO_EXTENSION);
        $date_time = date('dmY_His');
        $new_file_name_resume = "resume_{$date_time}_" . uniqid('', true) . '.' . $file_extension_resume;
        $target_file_resume = $target_dir_resume . $new_file_name_resume;

        if (!move_uploaded_file($_FILES["resume"]["tmp_name"], $target_file_resume)) {
            echo "Error uploading resume.<br>";
        }
    }

    // Prepare SQL query based on whether we're creating or updating a record
    if ($id) {
        // Update existing record
        $sql = "UPDATE student SET 
                    name='$name', dob='$dob', gender='$gender', aadhar='$aadhar', 
                    email='$email', mobile='$mob', address='$address', 
                    school='$school', uniboard='$uniboard', 
                    course='$course', yop='$yop', roll='$roll', 
                    date_modified=NOW(), modified_by = '$modifier_name',state='$state'" .
                    ($new_file_name_resume ? ", resume='$new_file_name_resume'" : "") .
                    ($new_file_name_image ? ", picture='$new_file_name_image'" : "") . 
                " WHERE id=$id";
    } else {
        // Insert new record
        $sql = "INSERT INTO student (name, dob, gender, aadhar, email, mobile, address, school, uniboard, course, yop, roll, date_entered, state, resume, picture, date, created_by)
                VALUES ('$name', '$dob', '$gender', '$aadhar', '$email', '$mob', '$address', '$school', '$uniboard', '$course', '$yop', '$roll', NOW(), '$state', '$new_file_name_resume', '$new_file_name_image', NOW(), '$name')";
    }

    if (mysqli_query($link, $sql)) {
        include 'listing.php';
    } else {
        echo "Error: " . mysqli_error($link) . "<br> SQL: " . $sql; 
    }

    mysqli_close($link);
}
?>
