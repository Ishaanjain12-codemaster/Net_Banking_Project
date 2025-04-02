<?php
session_start();
$server = "localhost";
$user = "root";
$pass = "";
$dbname = "net_banking_system";

$conn = mysqli_connect($server, $user, $pass, $dbname);

if (!$conn) {
    die("Not connected due to " . mysqli_connect_error());
}

$id_error = $email_error = $pass_error1 = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    $id = isset($_POST["id"]) ? trim(htmlspecialchars($_POST["id"])) : '';
    $name = isset($_POST["name"]) ? trim(htmlspecialchars($_POST["name"])) : '';
    $contact = isset($_POST["contact"]) ? trim(htmlspecialchars($_POST["contact"])) : '';
    $email = isset($_POST["email"]) ? trim(htmlspecialchars($_POST["email"])) : '';
    $address = isset($_POST["address"]) ? trim(htmlspecialchars($_POST["address"])) : '';
    $dob = isset($_POST["dob"]) ? trim(htmlspecialchars($_POST["dob"])) : '';
    $password = isset($_POST["password"]) ? trim($_POST["password"]) : '';
    $password1 = isset($_POST["password1"]) ? trim($_POST["password1"]) : '';

    if (empty($id)) {
        $id_error = "Adhaar No. is empty";
    } elseif (!preg_match("/^[0-9]{12}$/", $id)) {
        $id_error = "Incorrect Aadhaar format";
    }

    if (empty($email)) {
        $email_error = "Enter email";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_error = "Incorrect email format";
    }

    if (empty($password)) {
        $pass_error1 = "Enter password";
    } elseif (strlen($password) < 8) {
        $pass_error1 = "Password must be at least 8 characters long";
    } elseif ($password !== $password1) {
        $pass_error1 = "Passwords do not match";
    }

    if (empty($id_error) && empty($email_error) && empty($pass_error1)) {
        $acc_no = random_int(1000000000, 9999999999);
        $c_no = random_int(10000000, 99999999);

        $stmt = $conn->prepare("INSERT INTO `signup` (`customer_id`, `name`, `contact`, `email`, `address`, `dob`, `password`, `account_no`, `crn_no`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssssss", $id, $name, $contact, $email, $address, $dob, $password, $acc_no, $c_no);

        if ($stmt->execute()) {
            header("Location: Mainpage.php");
            exit();
        } else {
            echo "ERROR: " . $stmt->error;
        }

        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Signup</title>
    <link rel="icon" href="logo.jpg" type="image/x-icon" />
    <style>
        * {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }
        #header {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 15px;
            background-color: #7ab3f0;
            color: white;
            font-size: 24px;
            font-weight: 600;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        #main_container {
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .signup-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 500px;
            text-align: center;
        }
        .signup-container input {
            width: 80%;
            padding: 8px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .signup-container button {
            width: 60%;
            padding: 10px;
            margin: 10px;
            border-radius: 20px;
            background: #3A92E4;
            color: white;
            font-weight: bold;
            cursor: pointer;
            border: none;
        }
        .signup-container button:hover {
            background: #0056b3;
        }
        span {
            color: red;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <header id="header">
        🏦 EVEREST FINANCIALS
    </header>
    <div id="main_container">
        <div class="signup-container">
            <h2>SIGN UP</h2>
            <form id="signup_form" method="POST" action="signup.php">
                <input type="number" id="id" name="id" placeholder="Aadhaar No." required><br>
                <span><?php echo $id_error; ?></span>

                <input type="text" placeholder="Full Name" id="name" name="name" required>
                <input type="tel" placeholder="Contact Number" id="contact" name="contact" required>
                <input type="email" placeholder="Email" id="email" name="email" required><br>
                <span><?php echo $email_error; ?></span>
                
                <input type="text" placeholder="Address" id="address" name="address" required>
                <input type="date" id="dob" name="dob" required>
                
                <input type="password" placeholder="Password" id="password" name="password" required><br>
                <span><?php echo $pass_error1; ?></span>

                <input type="password" placeholder="Confirm Password" id="cnf_password" name="password1" required><br>
                
                <button type="submit" name="submit">Register</button><br>
                <a href="Mainpage.php" style="color:#3A92E4;">Already have an account...</a>
            </form>
        </div>
    </div>
</body>
</html>
