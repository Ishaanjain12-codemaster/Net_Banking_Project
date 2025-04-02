<?php
session_start();

$db_server = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "net_banking_system";

$conn = new mysqli($db_server, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if(isset($_POST['logout'])){
    session_unset();
    session_destroy();
    header("Location: Mainpage.php");
    exit();
}

$acc_no = $_SESSION["acc_no"];
$status = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    $amount = isset($_POST['amount']) ? trim($_POST['amount']) : '';
    $income = isset($_POST['income']) ? trim($_POST['income']) : '';

    if (!is_numeric($amount) || !is_numeric($income) || $amount <= 0 || $income <= 0) {
        $status = "Invalid input. Please enter valid numeric values.";
    } else {
        $sql = "INSERT INTO loan (applicant_income, loan_amount, application_date) 
                VALUES (?, ?, NOW())";
        
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("dd", $income, $amount);
            
            if ($stmt->execute()) {
                $status = "Loan status will be updated in 24 hours.";
            } else {
                $status = "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $status = "SQL Error: " . $conn->error;
        }
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Approval</title>
    <link rel="icon" href="logo.jpg"
        type="image/x-icon" />
    <style>
        *{
            padding:0;
            margin:0;
            font-family: 'Poppins', sans-serif;
        }
        #header_container {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 15px;
            color: white;
            font-size: 24px;
            font-weight: 600;
        }

        #header {
            padding: 20px;
            background-color: #7ab3f0;
            color: white;
            font-size: 24px;
            font-weight: 600;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        #header_button_container {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 15px;
        }

        button {
            margin-left: 45px;
            padding: 10px 30px;
            border-radius: 50px;
            color: #3A92E4;
            border: 2px solid #3A92E4;
            font-weight: bold;
            transition: all 0.4s ease-in-out;
            cursor: pointer;
        }

        button:hover {
            background-color: #3A92E4;
            color: white;
            box-shadow: 0 5px 15px rgba(58, 146, 228, 0.4);
        }

        #profile_container {
            position: absolute;
            margin-left: 1200px;
        }

        img {
            width: 45px;
            border-radius: 150px;
        }
        #main_container{
            display: flex;
        }
        #form_container{
            border: solid 1px #7ab3f0;
            border-radius: 20px;
            margin-left: 450px;
            margin-top: 100px;
            width: 400px;
            height: 280px;
            text-align: center;
        }
        input{
            width: 60%;
            padding: 8px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <header id="header">
        <div id="header_container">
            🏦 EVEREST FINANCIALS
            <div id="profile_container">
                <img src="user_profile.jpg" alt="profile"><br>
                <span id="user_profile" style="font-size:10px"><?php echo $acc_no; ?></span>
            </div>
        </div>
        <div id="header_button_container">
            <div id="button_container">
                <button onclick="location.href='AccountOverview.php'">Account Overview</button>
                <button onclick="location.href='Check_Balance.php'">Check Balance</button>
                <button onclick="location.href='Statement.php'">Statement</button>
                <button onclick="location.href='Transfer_Money.php'">Transfer Money</button>
                <button onclick="location.href='Mainpage.php'" name="logout">Logout</button>
            </div>
        </div>
    </header>
    <div id="main_container">
        <div id="form_container">
            <h2 style="color:#7ab3f0;">Loan Approval</h2><br>
            <form method="post">
                <label for="amount">Loan Amount</label>
                <input type="text" id="amount" name="amount" required><br>
                <label for="income">Income</label>
                <input type="text" id="income" name="income" required><br><br>
                <button type="submit" id="submit" name="submit">Submit</button><br>
                <span><?php echo $status; ?></span><br>
                <h6>Rate Of Interest is 18% and the loan <br>will be sanctioned for 1 year or less</h6>
            </form>
        </div>
    </div>
    
</body>
</html>