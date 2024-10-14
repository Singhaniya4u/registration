<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

    
    $target_dir_image = "uploads/images/";
    $target_dir_resume = "uploads/resume/";

    $file_extension_image = pathinfo($_FILES["pic"]["name"], PATHINFO_EXTENSION);
    $file_extension_resume = pathinfo($_FILES["resume"]["name"], PATHINFO_EXTENSION);

    $date_time = date('dmY_His'); 

    $new_file_name_image = "image_{$date_time}_" . uniqid('', true) . '.' . $file_extension_image;
    $new_file_name_resume = "resume_{$date_time}_" . uniqid('', true) . '.' . $file_extension_resume;

    
    if (isset($_FILES["pic"]) && $_FILES["pic"]["error"] == UPLOAD_ERR_OK) {
        $target_file_pic = $target_dir_image . $new_file_name_image;
        if (move_uploaded_file($_FILES["pic"]["tmp_name"], $target_file_pic)) {
            echo "Profile picture uploaded successfully.<br>";
        } else {
            echo "Error uploading profile picture.<br>";
        }
    } else {
        echo "Profile picture upload error.<br>";
    }

    
    if (isset($_FILES["resume"]) && $_FILES["resume"]["error"] == UPLOAD_ERR_OK) {
        $target_file_resume = $target_dir_resume . $new_file_name_resume;
        if (move_uploaded_file($_FILES["resume"]["tmp_name"], $target_file_resume)) {
            echo "Resume uploaded successfully.<br>";
        } else {
            echo "Error uploading resume.<br>";
        }
    } else {
        echo "Resume upload error.<br>";
    }

    
    echo "<h2>Registration Successful!</h2>";
    echo "Name: $name<br>";
    echo "DOB: $dob<br>";
    echo "Gender: $gender<br>";
    echo "Aadhar: $aadhar<br>";
    echo "State: $state<br>";
    echo "School/College: $school<br>";
    echo "University/Board: $uniboard<br>";
    echo "Course: $course<br>";
    echo "Year of Passing: $yop<br>";
    echo "Roll No: $roll<br>";
    echo "Email: $email<br>";
    echo "Mobile: $mob<br>";
    echo "Address: $address<br>";
}
?>
