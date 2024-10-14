<?php
$name = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : '';
$dob = isset($_GET['dob']) ? htmlspecialchars($_GET['dob']) : '';
$gender = isset($_GET['gender']) ? htmlspecialchars($_GET['gender']) : '';
$aadhar = isset($_GET['aadhar']) ? htmlspecialchars($_GET['aadhar']) : '';
$state = isset($_GET['state']) ? htmlspecialchars($_GET['state']) : '';
$picture = isset($_GET['picture']) ? htmlspecialchars($_GET['picture']) : '';
$school = isset($_GET['school']) ? htmlspecialchars($_GET['school']) : '';
$uniboard = isset($_GET['uniboard']) ? explode(", ", $_GET['uniboard']) : [];
$course = isset($_GET['course']) ? htmlspecialchars($_GET['course']) : '';
$yop = isset($_GET['yop']) ? htmlspecialchars($_GET['yop']) : '';
$roll = isset($_GET['roll']) ? htmlspecialchars($_GET['roll']) : '';
$email = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';
$mob = isset($_GET['mob']) ? htmlspecialchars($_GET['mob']) : '';
$address = isset($_GET['address']) ? htmlspecialchars($_GET['address']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
</head>
<body>
    <h2>Registration Form</h2>
    <form action="register.php" method="post" enctype="multipart/form-data">
        <div>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo $name; ?>" required />
        </div>
        <div>
            <label for="dob">Date of Birth:</label>
            <input type="date" id="dob" name="dob" value="<?php echo $dob; ?>" required />
        </div>
        <div>
            <label>Gender:</label>
            <input type="radio" id="male" name="gender" value="Male" <?php echo ($gender === 'Male') ? 'checked' : ''; ?> required />
            <label for="male">Male</label>
            <input type="radio" id="female" name="gender" value="Female" <?php echo ($gender === 'Female') ? 'checked' : ''; ?> required />
            <label for="female">Female</label>
        </div>
        <div>
            <label for="aadhar">Aadhar no:</label>
            <input type="text" id="aadhar" name="aadhar" value="<?php echo $aadhar; ?>" required />
        </div>
        <div>
            <label for="state">State:</label>
            <select name="state" id="state" required>
                <option value="" <?php echo ($state === '') ? 'selected' : ''; ?>>Select State</option>
                <option value="Bihar" <?php echo ($state === 'Bihar') ? 'selected' : ''; ?>>Bihar</option>
                <option value="West Bengal" <?php echo ($state === 'West Bengal') ? 'selected' : ''; ?>>West Bengal</option>
                <option value="Uttar Pradesh" <?php echo ($state === 'Uttar Pradesh') ? 'selected' : ''; ?>>Uttar Pradesh</option>
                <option value="Mumbai" <?php echo ($state === 'Mumbai') ? 'selected' : ''; ?>>Mumbai</option>
            </select>
        </div>
        <div>
            <label for="school">School/College:</label>
            <input type="text" id="school" name="school" value="<?php echo $school; ?>" required />
        </div>
        <div>
            <label>University/Board:</label>
            <input type="checkbox" id="cbse" name="uniboard[]" value="cbse" <?php echo (in_array('cbse', $uniboard)) ? 'checked' : ''; ?> />
            <label for="cbse">C.B.S.E</label>
            <input type="checkbox" id="icse" name="uniboard[]" value="icse" <?php echo (in_array('icse', $uniboard)) ? 'checked' : ''; ?> />
            <label for="icse">I.C.S.E</label>
            <input type="checkbox" id="wbbse" name="uniboard[]" value="wbbse" <?php echo (in_array('wbbse', $uniboard)) ? 'checked' : ''; ?> />
            <label for="wbbse">W.B.B.S.E</label>
        </div>
        <div>
            <label for="course">Course:</label>
            <select name="course" id="course" required>
                <option value="" <?php echo ($course === '') ? 'selected' : ''; ?>>Select</option>
                <option value="Bachelor of Engineering" <?php echo ($course === 'Bachelor of Engineering') ? 'selected' : ''; ?>>Bachelor of Engineering</option>
                <option value="Bachelor of Computer Application" <?php echo ($course === 'Bachelor of Computer Application') ? 'selected' : ''; ?>>Bachelor of Computer Application</option>
                <option value="Intermediate with Science" <?php echo ($course === 'Intermediate with Science') ? 'selected' : ''; ?>>Intermediate with Science</option>
                <option value="Intermediate with Commerce" <?php echo ($course === 'Intermediate with Commerce') ? 'selected' : ''; ?>>Intermediate with Commerce</option>
                <option value="Intermediate with Arts" <?php echo ($course === 'Intermediate with Arts') ? 'selected' : ''; ?>>Intermediate with Arts</option>
            </select>
        </div>
        <div>
            <label for="yop">Year of Passing:</label>
            <input type="text" id="yop" name="yop" value="<?php echo $yop; ?>" required />
        </div>
        <div>
            <label for="roll">Roll no:</label>
            <input type="text" id="roll" name="roll" value="<?php echo $roll; ?>" required />
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo $email; ?>" required />
        </div>
        <div>
            <label for="mob">Mobile:</label>
            <input type="tel" id="mob" name="mob" value="<?php echo $mob; ?>" required />
        </div>
        <div>
            <label for="address">Address:</label>
            <textarea id="address" name="address" required><?php echo $address; ?></textarea>
        </div>
        <div>
            <label for="picture">Profile Picture:</label>
            <input type="file" id="picture" name="picture" accept="image/*" />
            <?php if ($picture): ?>
                <p>Current Picture: <img src="<?php echo $picture; ?>" alt="Profile Picture" width="100" /></p>
            <?php endif; ?>
        </div>
        <div>
            <button type="submit">Submit</button>
        </div>
    </form>
</body>
</html>
