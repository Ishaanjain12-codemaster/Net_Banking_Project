<?php
$db_server = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "net_banking_system";

$conn = new mysqli($db_server, $db_user, $db_pass, $db_name);
if(isset($_POST['logout'])){
    session_unset();
    session_destroy();
    header("Location: Mainpage.php");
    exit();
}

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
session_start(); 

$acc_no = $_SESSION["acc_no"];

$sql = "SELECT * FROM transaction WHERE account_no = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("SQL error: " . $conn->error);
}

$stmt->bind_param("s", $acc_no);
$stmt->execute();
$result = $stmt->get_result();

$transactions = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Overview</title>
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
            margin-top: 100px;
            min-height: 100%;
        }

        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px 8px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 800px;
        }

        h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        h2 {
            font-size: 20px;
            color: #007bff;
            margin-bottom: 15px;
        }

        .account-details {
            margin-bottom: 30px;
        }

        .detail {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .label {
            font-weight: bold;
            color: #555;
        }

        .value {
            color: #333;
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

        * {
            margin: 0;
            padding: 0;
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
            margin-left: 50px;
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
                <button onclick="location.href='loan_approval.php'">Loan Approval</button>
                <button onclick="location.href='Check_Balance.php'">Check Balance</button>
                <button onclick="location.href='Statement.php'">Statement</button>
                <button onclick="location.href='Transfer_Money.php'">Transfer Money</button>
                <button onclick="location.href='Mainpage.php'" name="logout">Logout</button>
            </div>
        </div>
    </header>

    <div id="main_container">
        <div class="container">
            <h1>Account Overview</h1>
            <div class="account-details">
                <h2>Account Information</h2>
                <div class="detail">
                    <span class="label">Account Number:</span>
                    <span class="value"><?php echo htmlspecialchars($acc_no); ?></span>
                </div>
                <div class="detail">
                    <span class="label">Account Type:</span>
                    <span class="value">Savings Account</span>
                </div>
            </div>

            <div class="recent-transactions">
                <h2>Recent Transactions</h2>
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
                        <?php if (!empty($transactions)): ?>
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
                                <td colspan="4">No transactions found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
