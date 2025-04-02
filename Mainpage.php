<?php
session_start();
session_unset();
session_destroy();
session_start();

$server = "localhost";
$user = "root";
$pass = "";
$dbname = "net_banking_system";

$conn = mysqli_connect($server, $user, $pass, $dbname);
if (!$conn) {
    die("Not connected: " . mysqli_connect_error());
}

$pass_error = "";

if (isset($_POST["submit"])) {
    $c_no1 = trim(htmlspecialchars($_POST["c_no"]));
    $pass1 = trim($_POST["pass"]);

    if (!empty($c_no1) && !empty($pass1)) {
        $stmt = $conn->prepare("SELECT `account_no`, `crn_no`, password FROM `signup` WHERE `crn_no` = ?");
        $stmt->bind_param("s", $c_no1);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $stored_password = $row['password'];

            if ($pass1 === $stored_password) {
                $_SESSION["crn_no"] = $row['crn_no'];
                $_SESSION["acc_no"] = $row['account_no'];
                header("Location: AccountOverview.php");
                exit();
            } else {
                $pass_error = "Incorrect credentials. Try Again.";
            }
        } else {
            $pass_error = "Incorrect credentials. Try Again.";
        }
        $stmt->close();
    } else {
        $pass_error = "Please fill in all fields.";
    }
}
$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Everest Financial</title>
    <link rel="icon" href="logo.jpg"
        type="image/x-icon" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: #f4f4f9;
            color: #333;
        }
        #main_container {
            text-align: center;
            padding: 20px;
        }
        h2 {
            color: #3A92E4;
            font-weight: 600;
            letter-spacing: 1.2px;
        }
        button {
            padding: 10px 30px;
            margin: 10px;
            border: 2px solid #3A92E4;
            border-radius: 20px;
            background: transparent;
            color: #3A92E4;
            font-weight: bold;
            transition: all 0.4s ease-in-out;
            cursor: pointer;
        }
        button:hover {
            background: #3A92E4;
            color: white;
            box-shadow: 0 5px 15px rgba(58, 146, 228, 0.4);
        }
        #signup {
            float: right;
            margin-right: 50px;
        }
        hr {
            margin: 40px 0;
            border: 0;
            height: 1px;
            background: #3A92E4;
            opacity: 0.5;
        }
        footer {
            padding: 20px;
            background: #3A92E4;
            color: white;
        }
        footer img {
            height: 40px;
            margin: 10px;
        }
        a{
            margin-right:120px;
            color: #3A92E4;
        }
        a:hover{
            color: rgb(90, 68, 251);
        }
        #login_container{
            border:solid 1px;
            border-color: #3A92E4;
            width:500px;
            height:350px;
            border-radius: 15px;
            margin-left: 350px;
        }
        input{
            width: 60%;
            padding: 8px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        #footer{
            padding: 5px;
            background-color: #7ab3f0;
            color: white;
            font-size: 24px;
            font-weight: 600;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        * {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body>
    <div id="main_container">
        <h2>Everest Financials</h2><br>
        <button onclick="location.href='AccountOverview.php'">Account Overview</button>
        <button onclick="location.href='Check_Balance.php'">Check Balance</button>
        <button onclick="location.href='Statement.php'">Statement</button>
        <button onclick="location.href='Transfer_Money.php'">Transfer Money</button>
        <button onclick="location.href='loan_approval.php'">Loan Approval</button>
        <button onclick="location.href='signup.php'" id="signup">Signup</button>
        <hr>
        <div id="container2">
            <p>Welcome to Everest Financials, your trusted partner for all your financial needs.</p><br>
            <div id="login_container">
                <h2>Login</h2><br>
                <form method="POST">
                    <label for="crn">CRN Number</label>
                    <input type="text" id="crn" name="c_no" placeholder="CRN Number" required><br>
                    <label for="password">Password</label>
                    <input type="password" id="password" placeholder="Password" name="pass" required><br>
                    <span><?php echo $pass_error; ?></span>
                    <button type="submit" name="submit">Submit</button>
                    <br>
                    <a href="forgot_password.php" style="margin-left:120px;">Forgot Password</a>
                </form>
            </div>
        </div>
        <hr>
        <footer id="footer">
            <b><i>Connect with us at</i></b><br>
            <img src="logo.png" alt="logos">
            <img src="image.png" alt="social media">
        </footer>
    </div>
</body>
</html>