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

$transactions = [];
$acc_no = $_SESSION["acc_no"];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    
    $sql = "SELECT t.transaction_id, t.amount_send, t.receiver_account_no, t.transaction_date 
            FROM transaction t
            JOIN signup s ON t.account_no = s.account_no
            WHERE t.account_no = ? AND t.transaction_date BETWEEN ? AND ?";
    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("sss", $acc_no, $start_date, $end_date);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $transactions[] = $row;
        }
        
        $stmt->close();
    } else {
        die("SQL Error: " . $conn->error);
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Statement Check</title>
    <link rel="icon" href="logo.jpg"
        type="image/x-icon" />
    <style>
        #main_container {
            font-family: 'Poppins', sans-serif;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            margin-top: 100px;    
            height: 60%;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 350px;
            text-align: center;
        }
        h2 {
            color: #3A92E4;
            font-weight: 600;
            letter-spacing: 1.2px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            text-align: left;
            margin-top: 10px;
            font-weight: bold;
            color: #555;
        }
        input {
            margin-top: 5px;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        button {
            margin-top: 15px;
            padding: 12px;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease-in-out;
        }
        button:hover {
            background-color: #0056b3;
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
        .recent-transactions table {
            width: 100%;
            border-collapse: collapse;
        }

        .recent-transactions th,
        .recent-transactions td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .recent-transactions th {
            background-color: #007bff;
            color: white;
        }

        .recent-transactions tr:hover {
            background-color: #f1f1f1;
        }

        .status {
            font-weight: bold;
        }

        .status.completed {
            color: green;
        }

        .status.pending {
            color: orange;
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
                <button onclick="location.href='Check_Balance.php'">Check Balance</button>
                <button onclick="location.href='Transfer_Money.php'">Transfer Money</button>
                <button onclick="location.href='Mainpage.php'" name="logout">Logout</button>
            </div>
        </div>
    </header>
    <div id="main_container">
        <div class="container">
            <h2>Bank Statement</h2>
            <form action="#" method="post">
                <label for="account-number">Account Number:</label>
                <input type="text" id="account-number" name="account-number" placeholder="Enter your account number" required>
                
                <label for="start-date">Start Date:</label>
                <input type="date" id="start-date" name="start_date" required>
                
                <label for="end-date">End Date:</label>
                <input type="date" id="end-date" name="end_date" required>
                
                <button type="submit" name="submit">Check Statement</button>
            </form>
        </div>
    </div>
    <br><br>
    <div class="recent-transactions">
            <table>
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Amount Sent</th>
                        <th>Receiver Account</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($transactions) && isset($_POST['submit'])): ?>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($transaction['transaction_id']); ?></td>
                                <td><?php echo htmlspecialchars($transaction['amount_send']); ?></td>
                                <td><?php echo htmlspecialchars($transaction['receiver_account_no']); ?></td>
                                <td><?php echo htmlspecialchars($transaction['transaction_date']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center;">No transactions found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
</body>
</html>
