<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['loggedin'])) {
    header("Location: /");
    exit;
}
if (basename($_SERVER['PHP_SELF']) !== 'view.php') {
    unset($_SESSION['access_granted_view']);
}
// DB Connection
$host = '';
$db   = '';
$user = '';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query with correct column names
$query = "
SELECT 
    SUM(COALESCE(bill_amount, 0)) AS total_bill,
    SUM(COALESCE(cheque_amount, 0)) AS total_cheque,
    SUM(COALESCE(gst_1_5, 0)) AS total_gst_1_5,
    SUM(COALESCE(remaining_gst, 0)) AS total_gst_remaining,
    SUM(COALESCE(cashier_percent, 0)) AS total_cashier,
    SUM(COALESCE(ag_percent, 0)) AS total_ag,
    SUM(COALESCE(office_percent, 0)) AS total_office_percent,
    SUM(COALESCE(balance, 0)) AS total_balance
FROM sales";

$result = $conn->query($query);
$data = $result->fetch_assoc();

function money($amount) {
    return 'Rs ' . number_format($amount ?? 0, 2);
}
?>
<?php if (isset($_SESSION['success'])): ?>
  <div class="bg-green-100 text-green-700 p-2 rounded mb-2">
    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
  </div>
<?php endif; ?>


<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard</title>
<link rel="icon" type="image/png" sizes="32x32" href="/images/weblogo.png?v=2">
<link rel="icon" type="image/png" sizes="16x16" href="/images/weblogo.png?v=2">
<link rel="shortcut icon" href="/images/weblogo.png?v=2">

  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
<link rel="icon" type="image/png" sizes="32x32" href="/images/weblogo.png?v=2">
<link rel="icon" type="image/png" sizes="16x16" href="/images/weblogo.png?v=2">
<link rel="shortcut icon" href="/images/weblogo.png?v=2">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- html2pdf -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

  <style>
    body {
      padding-top: 64px; /* height of fixed navbar */
    }
  </style>
</head>
<body class="bg-gray-100 text-gray-800">
<!-- Fixed Top Navbar -->
<div class="menu-bar fixed top-0 left-0 w-full bg-[#6b3de6] shadow z-50">
  <div class="flex justify-between items-center px-6 py-3 text-white font-medium">
    
    <!-- Centered Menu Links -->
    <div class="flex-1 flex justify-center space-x-6">
      <a href="/home" class="hover:bg-purple-800 px-4 py-2 rounded" style="background-color: #5830be;">Dashboard</a>
      <a href="/sales" class="hover:bg-purple-800 px-4 py-2 rounded">Sales</a>
      <a href="/purchase" class="hover:bg-purple-800 px-4 py-2 rounded">Purchases</a>
      <a href="/department" class="hover:bg-purple-800 px-4 py-2 rounded">Department</a>
      <a href="/staff" class="hover:bg-purple-800 px-4 py-2 rounded">Staff</a>
      <a href="/Form" class="hover:bg-purple-800 px-4 py-2 rounded">Form</a>
      <a href="/ledger" class="hover:bg-purple-800 px-4 py-2 rounded">Ledger</a>           
      <a href="/expense" class="hover:bg-purple-800 px-4 py-2 rounded">Expense</a>
      <a href="/view" class="hover:bg-purple-800 px-4 py-2 rounded">View</a>
    </div>

   <!-- Right Side: Username with Dropdown -->
<div class="relative inline-block text-left">
  <!-- Username Button -->
  <button id="userMenuButton" class="flex items-center text-sm font-semibold focus:outline-none">
    <?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?>
    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
    </svg>
  </button>

  <!-- Dropdown Menu -->
  <div id="userMenu" class="hidden absolute right-0 mt-2 w-48 bg-white border rounded-lg shadow-lg z-50">
    <a href="change_password.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
      Change Password
    </a>
    <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
      Logout
    </a>
  </div>
</div>

<script>
  // Toggle dropdown on click
  document.getElementById('userMenuButton').addEventListener('click', function () {
    document.getElementById('userMenu').classList.toggle('hidden');
  });

  // Close dropdown if clicked outside
  window.addEventListener('click', function (e) {
    const button = document.getElementById('userMenuButton');
    const menu = document.getElementById('userMenu');
    if (!button.contains(e.target) && !menu.contains(e.target)) {
      menu.classList.add('hidden');
    }
  });
</script>


  </div>
</div>



  <!-- Heading -->
  <h2 class="text-3xl font-bold mb-6 text-center pt-10">Sales Financial Summary</h2>

  <!-- Cards -->
  <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 px-4">
    <div class="p-6 bg-purple-100 rounded-xl shadow text-center">
      <h4 class="font-semibold text-purple-700 mb-2">Total Bill Amount</h4>
      <p class="text-2xl font-bold"><?php echo money($data['total_bill']); ?></p>
    </div>
    <div class="p-6 bg-blue-100 rounded-xl shadow text-center">
      <h4 class="font-semibold text-blue-700 mb-2">Cheque Amount</h4>
      <p class="text-2xl font-bold"><?php echo money($data['total_cheque']); ?></p>
    </div>
    <div class="p-6 bg-indigo-100 rounded-xl shadow text-center">
      <h4 class="font-semibold text-indigo-700 mb-2">Balance</h4>
      <p class="text-2xl font-bold"><?php echo money($data['total_balance']); ?></p>
    </div>
  </div>

  <!-- Chart -->
  <div class="max-w-4xl mx-auto mt-10 bg-white p-6 rounded-xl shadow">
    <h3 class="text-xl font-bold mb-4 text-center">Sales Breakdown Chart</h3>
    <canvas id="salesChart" height="100"></canvas>
  </div>

  <!-- Chart Script -->
  <script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Bill Amount', 'Cheque', 'GST 1/5', 'Remaining GST', 'Cashier 10%', 'AG 4%', 'Office Percent', 'Balance'],
        datasets: [{
          label: 'Amount (Rs)',
          backgroundColor: [
            '#c084fc', '#d1d5db', '#fde68a', '#facc15',
            '#bfdbfe', '#bbf7d0', '#fecaca', '#ddd6fe'
          ],
          data: [
            <?= $data['total_bill'] ?? 0 ?>,
            <?= $data['total_cheque'] ?? 0 ?>,
            <?= $data['total_gst_1_5'] ?? 0 ?>,
            <?= $data['total_gst_remaining'] ?? 0 ?>,
            <?= $data['total_cashier'] ?? 0 ?>,
            <?= $data['total_ag'] ?? 0 ?>,
            <?= $data['total_office_percent'] ?? 0 ?>,
            <?= $data['total_balance'] ?? 0 ?>
          ]
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: function(context) {
                return 'Rs ' + context.parsed.y.toLocaleString();
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return 'Rs ' + value;
              }
            }
          }
        }
      }
    });
  </script>
  <script>
  document.querySelector("form").addEventListener("submit", function(e) {
    e.preventDefault(); // Page reload na ho

    let name = document.getElementById("name").value; // Form se naam lo
    document.getElementById("username-display").innerText = name; // Menu me set karo

    // Optional: Agar server pe bhejna ho to yaha AJAX ya fetch ka use karein
  });
</script>

</body>
</html>

