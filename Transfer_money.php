<?php
$db_server = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "net_banking_system";

$conn = new mysqli($db_server, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: Mainpage.php");
    exit();
}

session_start();

$sender_acc = $_SESSION["acc_no"];
$transaction_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $receiver_acc = trim($_POST['receiver_acc']);
    $amount = floatval($_POST['amount']);
    $password = $_POST['password'];

    $conn->begin_transaction();

    try {
        $sql = "SELECT s.password, t.curr_balance FROM transaction t
                JOIN signup s ON t.account_no = s.account_no WHERE t.account_no = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $sender_acc);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("Sender account not found.");
        }

        $row = $result->fetch_assoc();
        $stored_password = $row['password'];
        $sender_balance = $row['curr_balance'];

        if ($password !== $stored_password) {
            throw new Exception("Incorrect password.");
        }

        if ($sender_balance < $amount) {
            throw new Exception("Insufficient balance.");
        }

        $new_sender_balance = $sender_balance - $amount;
        $sql = "UPDATE transaction SET curr_balance = ? WHERE account_no = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ds", $new_sender_balance, $sender_acc);
        if (!$stmt->execute()) {
            throw new Exception("Error updating sender's balance.");
        }

        $sql = "SELECT curr_balance FROM transaction WHERE account_no = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $receiver_acc);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("Receiver account not found.");
        }

        $row = $result->fetch_assoc();
        $receiver_balance = $row['curr_balance'];

        $new_receiver_balance = $receiver_balance + $amount;
        $sql = "UPDATE transaction SET curr_balance = ? WHERE account_no = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ds", $new_receiver_balance, $receiver_acc);
        if (!$stmt->execute()) {
            throw new Exception("Error updating receiver's balance.");
        }

        $conn->commit();
        $transaction_message = "Transaction successful! Sent ₹" . number_format($amount, 2) . " to Account No: " . htmlspecialchars($receiver_acc);
    } catch (Exception $e) {
        $conn->rollback();
        $transaction_message = "Transaction failed: " . $e->getMessage();
    }

    $stmt->close();
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Money</title>
    <link rel="icon" href="logo.jpg" type="image/x-icon" />
    <style>
        #main_container {
            font-family: 'Poppins', sans-serif;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            margin-top: 100px;
            height: 70%;
        }
        h2 {
            text-align: center;
            color: #3A92E4;
            font-weight: 600;
            letter-spacing: 1.2px;
        }
        #main_container1 {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px 8px rgba(0, 0, 0, 0.1);
            width: 410px;
            text-align: center;
            margin-bottom: 100px;
        }
        input {
            padding: 8px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        #rec_account, #amount, #pin {
            width: 60%;
        }
        #submit_button {
            width: 60%;
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
        #submit_button:hover {
            background: #3A92E4;
            color: white;
            box-shadow: 0 5px 15px rgba(58, 146, 228, 0.4);
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
                <img src="user_profile.jpg"><br>
                <span id="user_profile" style="font-size:10px"><?php echo htmlspecialchars($sender_acc); ?></span>
            </div>
        </div>
        <div id="header_button_container">
            <div id="button_container">
                <button onclick="location.href='AccountOverview.php'">Account Overview</button>
                <button onclick="location.href='Check_Balance.php'">Check Balance</button>
                <button onclick="location.href='loan_approval.php'">Loan Approval</button>
                <button onclick="location.href='Statement.php'">Statement</button>
                <button onclick="location.href='Mainpage.php'" name="logout">Logout</button>
            </div>
        </div>
    </header>

    <div id="main_container">
        <div id="main_container1">
            <h2>TRANSFER MONEY</h2>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <label for="rec_account">Receiver's Account</label>
                <input type="text" placeholder="Account no." id="rec_account" name="receiver_acc" required><br><br>

                <label>Account Type:</label>
                <input type="radio" id="saving" name="account_type" value="saving" required>
                <label for="saving">Saving</label>
                <input type="radio" id="current" name="account_type" value="current" required>
                <label for="current">Current</label><br><br>

                <label for="amount">Amount</label>
                <input type="number" placeholder="Amount to be transferred" id="amount" name="amount" required><br><br>

                <label for="password">Password</label>
                <input type="password" placeholder="password" id="password" name="password" required><br><br>

                <input type="submit" value="Submit" id="submit_button" name="submit">
            </form>

            <div style="font-size: 14px; color: <?php echo ($transaction_message == "Transaction successful!") ? 'green' : 'red'; ?>;">
                <?php echo $transaction_message; ?>
            </div>
        </div>
    </div>
</body>
</html>
