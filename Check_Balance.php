<?php
session_start();

$db_server = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "net_banking_system";

$conn = new mysqli($db_server, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if(isset($_POST['logout'])){
    session_unset();
    session_destroy();
    header("Location: Mainpage.php");
    exit();
}

$acc_no = $_SESSION["acc_no"];
$balance = "$000";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit'])) {
        $pass = $_POST['password'];

        $sql = "SELECT s.password, t.curr_balance 
                FROM transaction t
                JOIN signup s ON t.account_no = s.account_no
                WHERE t.account_no = ?";

        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die('MySQL prepare error: ' . $conn->error);
        }

        $stmt->bind_param("s", $acc_no);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $stored_password = $row['password'];
            $curr_balance = $row['curr_balance'];

            if ($pass === $stored_password) {
                $balance = "$" . number_format($curr_balance, 2);
            } else {
                $balance = "Incorrect password!";
            }
        } else {
            $balance = "No account found.";
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
    <title>Bank Balance Check</title>
    <link rel="icon" href="logo.jpg"
        type="image/x-icon" />
    <style>
        #main_container {
            font-family: Arial, sans-serif;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            margin-top: 100px;;
            height: 100%;
        }

        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 400px;
            height:300px;
            text-align: center;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        input[type="text"],
        input[type="password"] {
            width: 80%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            text-align: center;
            margin-left: 40px;;
        }

        button {
            padding: 10px 30px;
            margin: 10px;
            border: 2px solid #3A92E4;
            border-radius: 20px;
            background: transparent;
            color: #3A92E4;
            font-weight: bold;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        button:hover {
            background: #3A92E4;
            color: white;
            box-shadow: 0 5px 15px rgba(58, 146, 228, 0.4);
        }

        .result {
            margin-top: 20px;
            padding: 10px;
            background-color: #e9ecef;
            border-radius: 4px;
            color: #333;
        }

        .result h2 {
            font-size: 20px;
            margin-bottom: 10px;
        }

        .result p {
            margin: 5px 0;
        }
        * {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }
        #header_container{
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 15px;
            color: white;
            font-size: 24px;
            font-weight: 600;
        }
        #header{
            padding:20px;
            background-color: #7ab3f0;
            color: white;
            font-size: 24px;
            font-weight: 600;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        #header_button_container{
            display:flex;
            justify-content: center;
            align-items: center;
            padding: 15px;
        }
        button{
            margin-left: 50px;
            padding: 10px 30px;
            border-radius: 50px;
            color:#3A92E4;
            border: 2px solid #3A92E4;
            background-color: whitesmoke;
            font-weight: bold;
            transition: all 0.4s ease-in-out;
            cursor:pointer;
        }
        button:hover{
            background-color: #3A92E4;
            color:white;
            box-shadow: 0 5px 15px rgba(58, 146, 228, 0.4);
        }
        #profile_container{
            position:absolute;
            margin-left:1200px;
        }
        img{
            width:45px;
            border-radius: 150px;
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
                <button onclick="location.href='loan_approval.php'">Loan Approval</button>
                <button onclick="location.href='Statement.php'">Statement</button>
                <button onclick="location.href='Transfer_Money.php'">Transfer Money</button>
                <button onclick="location.href='Mainpage.php'" name="logout">Logout</button>
            </div>
        </div>
    </header>
    <div id="main_container">
        <div class="container">
            <h1>Check Your Bank Balance</h1>
            <form method="post">
                <div class="form-group">
                    <label for="accountNumber">Account Number</label>
                    <input type="text" id="accountNumber" name="accountNumber" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" name="submit">Check Balance</button>
            </form>
            <div id="balanceResult" class="result">
                <?php echo htmlspecialchars($balance); ?>
            </div>
        </div>
    </div>
</body>
</html>