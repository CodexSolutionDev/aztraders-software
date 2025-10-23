<?php
session_start(); // Start session

// Password
$correct_password = "51268"; 

// Agar session mein access_granted_view hai to allow karo
if (!empty($_SESSION['access_granted_view']) && $_SESSION['access_granted_view'] === true) {
    $access_granted = true;
}

// Agar form submit hua aur session empty hai
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($access_granted)) {
    if (!empty($_POST['password']) && $_POST['password'] === $correct_password) {
        $_SESSION['access_granted_view'] = true; // sirf view ke liye session set
        $access_granted = true;
    } else {
        $error = "Wrong password!";
    }
}

// Agar access_granted empty hai to form dikhao
if (empty($access_granted)) {
    ?>
<!DOCTYPE html>
<html>
<head>
<title>Password Protected</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: linear-gradient(135deg, #d8bfd8, #6b3de6);
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }
    .box {
        background: rgba(255, 255, 255, 0.9);
        padding: 30px 40px;
        border-radius: 15px;
        box-shadow: 0px 8px 20px rgba(0,0,0,0.15);
        text-align: center;
        max-width: 350px;
        width: 100%;
        backdrop-filter: blur(10px);
    }
    h2 {
        margin-bottom: 20px;
        color: #6b3de6;
    }
    input[type=password], 
    input[type=submit] {
        padding: 12px;
        width: 90%;
        margin: 8px 0;
        font-size: 16px;
        border-radius: 8px;
        border: 1px solid #ccc;
        outline: none;
    }
    input[type=password]:focus {
        border-color: #6b3de6;
        box-shadow: 0 0 5px rgba(166,77,121,0.5);
    }
    input[type=submit] {
        background: #6b3de6;
        color: white;
        border: none;
        cursor: pointer;
        font-weight: bold;
        transition: background 0.3s ease;
    }
    input[type=submit]:hover {
        background: #6b3de6;
    }
    .error {
        color: red;
        font-size: 14px;
        margin-bottom: 10px;
    }
</style>
</head>
<body>
    <div class="box">
        <h2>Enter Password</h2>
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="post">
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Login">
        </form>
    </div>
</body>
</html>

<?php
    exit; // Stop page from loading until password correct
}
?>
<?php
$mysqli = new mysqli("", "", "", "");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$mode = $_GET['mode'] ?? 'sales'; // Default mode
$selectedTable = $_GET['table'] ?? null;
$tables = [];
$tableData = null;

if ($mode === 'sales') {
    // Get all tables except 'customers' and 'sales'
    $excludedTables = ['customers', 'sales'];
    $result = $mysqli->query("SHOW TABLES");
    if ($result) {
        while ($row = $result->fetch_array()) {
            if (!in_array($row[0], $excludedTables)) {
                $tables[] = $row[0];
            }
        }
    }

    if ($selectedTable && in_array($selectedTable, $tables)) {
        $tableData = $mysqli->query("SELECT * FROM `$selectedTable` ORDER BY id DESC");
        if (!$tableData) {
            die("Query failed: " . $mysqli->error);
        }
    }
} else if ($mode === 'customers') {
    $tables = ['customers']; // just for consistency
    $tableData = $mysqli->query("SELECT * FROM `customers` ORDER BY id DESC");
    if (!$tableData) {
        die("Query failed: " . $mysqli->error);
    }
}
?>
<?php
$mode = $_GET['mode'] ?? 'sales';
$selectedTable = $_GET['table'] ?? null;

if ($mode === 'purchase') {
  $db = new mysqli("", "", "", "");

  if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
  }

  // Get all table names
  $tables = [];
  $result = $db->query("SHOW TABLES");
  if ($result) {
    while ($row = $result->fetch_array()) {
      $tables[] = $row[0];
    }
  }

  // Get data from selected table
  if ($selectedTable && in_array($selectedTable, $tables)) {
    $tableData = $db->query("SELECT * FROM `$selectedTable` ORDER BY id DESC");
  }
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>View</title>
  <link rel="icon" type="image/png" sizes="32x32" href="/images/weblogo.png?v=2">
<link rel="icon" type="image/png" sizes="16x16" href="/images/weblogo.png?v=2">
<link rel="shortcut icon" href="/images/weblogo.png?v=2">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<head>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
  .menu-bar {
    background-color: #6b3de6;
    padding: 12px 0;
    text-align: center;
    box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
    width: 100%;
    top: 0;
    position: fixed;
        z-index: 9999;         /* 👈 sab elements ke upar rahe */

  }

  .menu-bar a {
    color: white;
    padding: 14px 24px;
    text-decoration: none;
    font-size: 16px;
    font-weight: 500;
    transition: background-color 0.3s;
  }

  .menu-bar a:hover {
    background-color: #5830be;
  }

 body {
    margin: 0;
    font-family: Arial, sans-serif;
    background-color: #f5f7fa;
  }
</style>

</head>
<body>

 <div class="menu-bar">
  <a href="/home">Dashboard</a>
  <a href="/sales">Sales</a>
  <a href="/purchase">Purchases</a>
  <a href="/department">Departments</a>
  <a href="/staff">Staff</a>
    <a href="/Form">Form</a>
    <a href="/expense">Expense</a>
    <a href="/ledger">Ledger</a>
    <a href="/view"style="background-color: #5830be;">View</a>

</div>
<!-- Existing content -->
<!-- Existing content -->
<div class="existing-content">
    <p>Yahan pehla content hai.</p>
</div>

<?php
  $mode = $_GET['mode'] ?? 'sales';
?>
<div style="display: flex; justify-content: center; margin-top: 20px;">
  <div style="
      border: 1px solid #ccc;
      border-radius: 12px;
      padding: 10px 15px;
      background-color: #f9f9f9;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      display: flex;
      gap: 8px;
    " tabindex="-1" role="tablist" aria-label="chat-list-filters">

    <!-- Sales Button -->
    <a href="?mode=sales" role="tab"
      style="box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
          z-index: 1000;
padding: 4px 10px; font-size: 12px; border: none;
        background: <?= $mode === 'sales' ? '#6b3de6' : '#e0e0e0' ?>;
        color: <?= $mode === 'sales' ? 'white' : 'black' ?>;
        border-radius: 6px; cursor: pointer; text-decoration: none;">
      Sales
    </a>

    <!-- Customer Button -->
    <a href="?mode=customers" role="tab"
      style="box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
          z-index: 1000;
padding: 4px 10px; font-size: 12px; border: none;
        background: <?= $mode === 'customers' ? '#6b3de6' : '#e0e0e0' ?>;
        color: <?= $mode === 'customers' ? 'white' : 'black' ?>;
        border-radius: 6px; cursor: pointer; text-decoration: none;">
      Departments
    </a>

    <!-- Form Button -->
     <a href="?mode=ledger" role="tab"
      style="box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
          z-index: 1000;padding: 4px 10px; font-size: 12px; border: none;
        background: <?= $mode === 'ledger' ? '#6b3de6' : '#e0e0e0' ?>;
        color: <?= $mode === 'ledger' ? 'white' : 'black' ?>;
        border-radius: 6px; cursor: pointer; text-decoration: none;">
      Ledger
    </a>

    <!-- Purchase Button -->
    <a href="?mode=purchase" role="tab"
      style="box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
          z-index: 1000;padding: 4px 10px; font-size: 12px; border: none;
        background: <?= $mode === 'purchase' ? '#6b3de6' : '#e0e0e0' ?>;
        color: <?= $mode === 'purchase' ? 'white' : 'black' ?>;
        border-radius: 6px; cursor: pointer; text-decoration: none;">
      Expense
    </a>
    <!-- Form Button -->
    <a href="?mode=form" role="tab"
      style="box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
          z-index: 1000;padding: 4px 10px; font-size: 12px; border: none;
        background: <?= $mode === 'form' ? '#6b3de6' : '#e0e0e0' ?>;
        color: <?= $mode === 'form' ? 'white' : 'black' ?>;
        border-radius: 6px; cursor: pointer; text-decoration: none;">
      Form
    </a>
<a href="?mode=invoices" role="tab"
      style="box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
          z-index: 1000;padding: 4px 10px; font-size: 12px; border: none;
        background: <?= $mode === 'form' ? '#6b3de6' : '#e0e0e0' ?>;
        color: <?= $mode === 'form' ? 'white' : 'black' ?>;
        border-radius: 6px; cursor: pointer; text-decoration: none;">
      Invoices
    </a>
  </div>
</div>

<style>
    body {
      margin: 0;
      font-family: "Segoe UI", sans-serif;
      background-color: #f4f6fa;
    }

    .wrapper {
      display: flex;
      min-height: 100vh;
    }

    .sidebar {
      width: 250px;
      background-color: #fff;
      border-right: 1px solid #ddd;
      padding: 20px;
    }

    .sidebar h3 {
      margin-bottom: 15px;
      color: #333;
    }

    .sidebar a {
      display: block;
      padding: 10px 15px;
      margin-bottom: 8px;
      background-color: #f1f1f1;
      color: #333;
      text-decoration: none;
      border-radius: 6px;
      transition: background 0.2s;
    }

    .sidebar a:hover,
    .sidebar a.active {
      background-color: #007bff;
      color: white;
    }

    .main-content {
      flex-grow: 1;
      padding: 30px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background-color: white;
      box-shadow: 0 0 5px rgba(0,0,0,0.05);
    }

    th, td {
      border: 1px solid #ddd;
      padding: 10px;
      font-size: 14px;
    }

    thead {
      background-color: #007bff;
      color: white;
    }

    h2 {
      color: #333;
      margin-bottom: 20px;
    }
    tfoot tr {
  background-color: #f0f0f0;
  font-size: 15px;
}

  </style>
</head>
<body>
<?php if ($mode === 'sales'): ?>
  <!-- Search Bar Only -->
  <div style="margin: 20px auto; width: 300px; position: relative;">
    <input 
      type="text" 
      id="tableSearch" 
      placeholder="Search Department" 
      style="width: 100%; padding: 8px 10px; border-radius: 6px; border: 1px solid #ccc; font-size: 14px;text-align:center;"
      onkeyup="filterSuggestions()"
      onfocus="showSuggestions()"
      onclick="showSuggestions()"
    >
    <div id="tableSuggestions" style="
      position: absolute;
      top: 38px;
      left: 0;
      right: 0;
      background: white;
      border: 1px solid #ccc;
      border-top: none;
      border-radius: 0 0 6px 6px;
      max-height: 200px;
      overflow-y: auto;
      display: none;
      z-index: 999;
    ">
      <?php foreach ($tables as $table): 
        $cleanName = ucwords(str_replace('_', ' ', $table));
      ?>
        <a href="?mode=sales&table=<?= htmlspecialchars($table) ?>" class="table-suggestion" style="
          display: block;
          padding: 8px 10px;
          text-decoration: none;
          color: #333;
          border-bottom: 1px solid #f0f0f0;
        " onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='white'">
          <?= $cleanName ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>

  <script>
    function filterSuggestions() {
      const input = document.getElementById('tableSearch').value.toLowerCase();
      const suggestions = document.querySelectorAll('.table-suggestion');
      let anyVisible = false;

      suggestions.forEach(item => {
        const text = item.textContent.toLowerCase();
        const match = text.includes(input);
        item.style.display = match ? 'block' : 'none';
        if (match) anyVisible = true;
      });

      document.getElementById('tableSuggestions').style.display = anyVisible ? 'block' : 'none';
    }

    function showSuggestions() {
      document.getElementById('tableSuggestions').style.display = 'block';
    }

    document.addEventListener('click', function(e) {
      const box = document.getElementById('tableSuggestions');
      const input = document.getElementById('tableSearch');
      if (!box.contains(e.target) && e.target !== input) {
        box.style.display = 'none';
      }
    });
  </script>


<div class="main-content" style="flex: 1;">
  <?php if ($selectedTable && $tableData): ?>
    <!-- Title + Buttons Row -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
      <h2 style="margin: 0;">
        <?= ucwords(str_replace('_', ' ', $selectedTable)) ?>
      </h2>
      <div>
        <button id="downloadBtn" style="padding:6px 12px; background:#6b3de6; color:white; border:none; border-radius:5px; cursor:pointer; margin-left:8px;">
          Download PDF
        </button>
        <button id="downloadCSVBtn" style="padding:6px 12px; background:#6b3de6; color:white; border:none; border-radius:5px; cursor:pointer; margin-left:8px;">
          Download CSV
        </button>
        <button id="editBtn" style="padding:6px 12px; background:#6b3de6; color:white; border:none; border-radius:5px; cursor:pointer; margin-left:8px;">
Edit        </button>
        <button id="deleteBtn" style="padding:6px 12px; background:#6b3de6; color:white; border:none; border-radius:5px; cursor:pointer; margin-left:8px;">
Delete        </button>
       

      </div>
    </div>
    <!-- 🔹 Hidden Edit Form (initially hidden) -->
<div id="editbtn" style="display:none; background:#f9f9f9; padding:20px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1); margin-bottom:20px;">
  <h3>Edit Sale Record</h3>
  <form id="editSaleForm">
    <input type="hidden" id="editId">

    <div style="display:flex; flex-wrap:wrap; gap:15px;">
      <div>
        <label>Bill Amount</label><br>
        <input type="number" id="bill" step="0.01" style="width:120px;">
      </div>
      <div>
        <label>Cheque Amount</label><br>
        <input type="number" id="cheque" step="0.01" style="width:120px;">
      </div>
      <div>
        <label>Total GST</label><br>
        <input type="number" id="totalGst" step="0.01" style="width:120px;">
      </div>
      <div>
        <label>GST 1.5%</label><br>
        <input type="number" id="gst1_5" step="0.01" readonly style="width:120px;">
      </div>
      <div>
        <label>Remaining GST</label><br>
        <input type="number" id="remGst" step="0.01" readonly style="width:120px;">
      </div>
      <div>
        <label>Vendor %</label><br>
        <input type="number" id="vendorPercent" step="0.01" style="width:80px;">
      </div>
      <div>
        <label>Cashier %</label><br>
        <input type="number" id="cashierPercent" step="0.01" style="width:80px;">
      </div>
      <div>
        <label>AG %</label><br>
        <input type="number" id="agPercent" step="0.01" style="width:80px;">
      </div>
      <div>
        <label>Office %</label><br>
        <input type="number" id="officePercent" step="0.01" style="width:80px;">
      </div>
      <div>
        <label>Balance</label><br>
        <input type="number" id="balance" step="0.01" readonly style="width:120px;">
      </div>
    </div>

    <div style="margin-top:15px;">
      <button type="button" id="saveEdit" style="padding:6px 12px; background:#28a745; color:white; border:none; border-radius:5px;">💾 Save</button>
      <button type="button" id="cancelEdit" style="padding:6px 12px; background:#999; color:white; border:none; border-radius:5px;">✖ Cancel</button>
    </div>
  </form>
</div>
<script>
let selectedRowId = null;

// ✅ When "Edit" button is clicked
document.getElementById("editBtn").addEventListener("click", () => {
  const selected = document.querySelector(".rowCheckbox:checked");
  if (!selected) {
    alert("Please select a row to edit!");
    return;
  }

  const row = selected.closest("tr");
  selectedRowId = selected.value;

  // Fill form fields from table row
  document.getElementById("editId").value = selectedRowId;
  document.getElementById("bill").value = row.children[3].innerText;
  document.getElementById("cheque").value = row.children[4].innerText;
  document.getElementById("totalGst").value = row.children[5].innerText;
  document.getElementById("gst1_5").value = row.children[6].innerText;
  document.getElementById("remGst").value = row.children[7].innerText;
  document.getElementById("vendorPercent").value = row.children[8].innerText;
  document.getElementById("cashierPercent").value = row.children[9].innerText;
  document.getElementById("agPercent").value = row.children[10].innerText;
  document.getElementById("officePercent").value = row.children[11].innerText;
  document.getElementById("balance").value = row.children[12].innerText;

  // Show form
document.getElementById("editbtn").style.display = "block";
  calculateRow(); // run calculation once
});

// ✅ Hide form on Cancel
document.getElementById("cancelEdit").addEventListener("click", () => {
  document.getElementById("editbtn").style.display = "none";
});

// ✅ Calculate values dynamically
["bill", "cheque", "totalGst", "vendorPercent", "cashierPercent", "agPercent", "officePercent"].forEach(id => {
  document.getElementById(id).addEventListener("input", calculateRow);
});

function calculateRow() {
  const bill = parseFloat(document.getElementById("bill").value) || 0;
  const cheque = parseFloat(document.getElementById("cheque").value) || 0;
  const totalGst = parseFloat(document.getElementById("totalGst").value) || 0;

  const gst1_5 = totalGst * 0.20;
  const remGst = totalGst - gst1_5;

  const vendorPercent = parseFloat(document.getElementById("vendorPercent").value) || 0;
  const cashierPercent = parseFloat(document.getElementById("cashierPercent").value) || 0;
  const agPercent = parseFloat(document.getElementById("agPercent").value) || 0;
  const officePercent = parseFloat(document.getElementById("officePercent").value) || 0;

  const vendor = cheque * vendorPercent / 100;
  const cashier = cheque * cashierPercent / 100;
  const ag = cheque * agPercent / 100;
  const office = cheque * officePercent / 100;

  const balance = cheque - (remGst + vendor + cashier + ag + office);

  document.getElementById("gst1_5").value = gst1_5.toFixed(2);
  document.getElementById("remGst").value = remGst.toFixed(2);
  document.getElementById("balance").value = balance.toFixed(2);
}

// ✅ Save to DB
document.getElementById("saveEdit").addEventListener("click", () => {

  // 🟢 YE LINE YAHAN ADD KARO — page ke URL se table name nikalne ke liye
  const tableName = new URLSearchParams(window.location.search).get("table");

  const data = {
    id: selectedRowId,
    table: tableName, // 👈 ab ye backend ko bheja ja raha hai
    bill_amount: document.getElementById("bill").value,
    cheque_amount: document.getElementById("cheque").value,
    total_gst: document.getElementById("totalGst").value,
    gst_1_5: document.getElementById("gst1_5").value,
    remaining_gst: document.getElementById("remGst").value,
    vendor_percent: document.getElementById("vendorPercent").value,
    cashier_percent: document.getElementById("cashierPercent").value,
    ag_percent: document.getElementById("agPercent").value,
    office_percent: document.getElementById("officePercent").value,
    balance: document.getElementById("balance").value
  };

  fetch("update_sales_view.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data)
  })
  .then(res => res.text())
  .then(res => {
    alert("✅ Record updated successfully!");
    location.reload();
  })
  .catch(err => console.error(err));
});

// ✅ Delete selected rows
document.getElementById("deleteBtn").addEventListener("click", () => {
  const selected = Array.from(document.querySelectorAll(".rowCheckbox:checked")).map(cb => cb.value);
  if (selected.length === 0) {
    alert("Please select at least one row to delete!");
    return;
  }

  if (!confirm("Are you sure you want to delete selected record(s)?")) return;

  // 🔹 delete ke liye bhi same table detect kar le
  const tableName = new URLSearchParams(window.location.search).get("table");

  fetch("delete_row.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ ids: selected, table: tableName })
  })
  .then(res => res.text())
  .then(res => {
    alert("🗑️ Selected record(s) deleted!");
    location.reload();
  })
  .catch(err => console.error(err));
});
</script>


<table id="dataTable" border="1" cellpadding="6" cellspacing="0" width="100%">
  <thead>
    <tr style="background:#6b3de6; color:white;">
      <th><input type="checkbox" id="selectAll"></th> <!-- ✅ master checkbox -->
      <th>S.No</th>
      <th>Username</th>
      <th>Bill Amount</th>
      <th>Cheque Amount</th>
      <th>Total GST</th>
      <th>GST 1.5%</th>
      <th>Remaining GST</th>
      <th>Vendor %</th>
      <th>Cashier %</th>
      <th>AG %</th>
      <th>Office %</th>
      <th>Balance</th>
      <th>Firm</th>
      <th>Date</th> <!-- ✅ Add this line -->
      <th>Invoice</th>
    </tr>
  </thead>
  <tbody>
    <?php
      $i = 1;
      while ($row = $tableData->fetch_assoc()):
    ?>
    <tr>
      <td><input type="checkbox" class="rowCheckbox" value="<?= $row['id'] ?? '' ?>"></td>
      <td><?= $row['id'] ?? '' ?></td>
      <td><?= $row['username'] ?? '' ?></td>
      <td><?= $row['bill_amount'] ?? 0 ?></td>
      <td><?= $row['cheque_amount'] ?? 0 ?></td>
      <td><?= $row['total_gst'] ?? 0 ?></td>
      <td><?= $row['gst_1_5'] ?? 0 ?></td>
      <td><?= $row['remaining_gst'] ?? 0 ?></td>
      <td><?= $row['vendor_percent'] ?? 0 ?></td>
      <td><?= $row['cashier_percent'] ?? 0 ?></td>
      <td><?= $row['ag_percent'] ?? 0 ?></td>
      <td><?= $row['office_percent'] ?? 0 ?></td>
      <td><?= $row['balance'] ?? 0 ?></td>
<td><?= $row['firm'] ?? '' ?></td>
<td><?= $row['created_at'] ?? '' ?></td> <!-- ✅ Add this -->

<?php
  $createdAt = $row['created_at'] ?? '';
  $fileLink = '<span style="color:gray;">No File</span>';

  if (!empty($createdAt)) {
      // Convert DB time (2025-10-12 21:04:30) → file time (2025-10-12 21-04-30)
      $fileTime = str_replace(':', '-', $createdAt);

      // Search for file with this time (any extension)
      $pattern = __DIR__ . '/Img/' . $fileTime . '.*';
      $files = glob($pattern);

      if (!empty($files)) {
          // File mil gayi → link show karo
          $fileName = basename($files[0]);
          $fileLink = '<a href="Img/' . htmlspecialchars($fileName) . '" target="_blank" download>Invoice</a>';
      }
  }
?>
<td><?= $fileLink ?></td>
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>
      <tfoot>
        <tr style="font-weight: bold; background-color: #f1f1f1;">
          <td colspan="11" style="text-align: right;">Total Balance:</td>
          <td colspan="2">Rs. <?= number_format($totalBalance, 2) ?></td>
        </tr>
      </tfoot>
    </table>
  <?php endif; ?>
</div>
<script>
  const csvBtn = document.getElementById("downloadCSVBtn");

  csvBtn.addEventListener("click", function () {
    const table = document.querySelector("#dataTable");
    if (!table) {
      alert("No table found!");
      return;
    }

    const rows = [];
    const checkboxes = document.querySelectorAll(".rowCheckbox");

    checkboxes.forEach((cb) => {
      if (cb.checked) {
        const row = cb.closest("tr");
        const cells = row.querySelectorAll("td");
        const rowData = [];

        cells.forEach((cell, i) => {
          // Skip checkbox (0) and Invoice column (last one)
          if (i > 0 && i < cells.length - 1) {
            rowData.push(cell.innerText.replace(/\s+/g, " ").trim());
          }
        });

        rows.push(rowData);
      }
    });

    if (rows.length === 0) {
      alert("Please select at least one row!");
      return;
    }

    // Get table headers (skip checkbox + invoice)
    const headers = [];
    const allHeaders = table.querySelectorAll("thead th");
    allHeaders.forEach((th, i) => {
      if (i > 0 && i < allHeaders.length - 1) {
        headers.push(th.innerText.trim());
      }
    });

    // Convert to CSV
    const csvContent = [headers.join(","), ...rows.map(r => r.join(","))].join("\n");

    // Create and download CSV
    const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
    const url = URL.createObjectURL(blob);
    const tableName = document.querySelector("h2").innerText || "table_data";
    const link = document.createElement("a");
    link.href = url;
    link.download = tableName.replace(/\s+/g, "_") + "_Selected.csv";
    link.click();
    URL.revokeObjectURL(url);
  });
</script>


<script>
function calculateRow(row) {
  const bill = parseFloat(row.querySelector(".bill")?.value) || 0;
  const cheque = parseFloat(row.querySelector(".cheque")?.value) || 0;
  const totalGst = parseFloat(row.querySelector(".totalGst")?.value) || 0;

  const gst1_5 = totalGst * 0.20;
  const remGst = totalGst - gst1_5;

  const vendorPercent = parseFloat(row.querySelector(".vendor")?.value) || 0;
  const cashierPercent = parseFloat(row.querySelector(".cashier")?.value) || 0;
  const agPercent = parseFloat(row.querySelector(".ag")?.value) || 0;
  const officePercent = parseFloat(row.querySelector(".office")?.value) || 0;

  const vendor = cheque * vendorPercent / 100;
  const cashier = cheque * cashierPercent / 100;
  const ag = cheque * agPercent / 100;
  const office = cheque * officePercent / 100;

  const balance = cheque - (remGst + vendor + cashier + ag + office);

  row.querySelector(".gst1_5").value = gst1_5.toFixed(2);
  row.querySelector(".remGst").value = remGst.toFixed(2);
  row.querySelector(".balance").value = balance.toFixed(2);

  saveRow(row); // <-- yahan DB me save hoga
}

function saveRow(row) {
  const id = row.dataset.id;
  const data = {
    bill_amount: row.querySelector(".bill").value,
    cheque_amount: row.querySelector(".cheque").value,
    total_gst: row.querySelector(".totalGst").value,
    gst_1_5: row.querySelector(".gst1_5").value,
    remaining_gst: row.querySelector(".remGst").value,
    vendor_percent: row.querySelector(".vendor").value,
    cashier_percent: row.querySelector(".cashier").value,
    ag_percent: row.querySelector(".ag").value,
    office_percent: row.querySelector(".office").value,
    balance: row.querySelector(".balance").value,
  };

  fetch("update_view.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id, ...data })
  })
  .then(res => res.text())
  .then(res => console.log("Saved:", res))
  .catch(err => console.error(err));
}

// Event listener for all inputs inside table
document.querySelectorAll("#dataTable tbody input").forEach(input => {
  input.addEventListener("input", function () {
    const row = this.closest("tr");
    calculateRow(row);
  });
});
</script>

<script>
// Row inline editing
document.querySelectorAll("#dataTable td.editable").forEach(td => {
  td.addEventListener("click", function () {
    if (this.querySelector("input")) return; // already editing

    let currentText = this.innerText;
    let input = document.createElement("input");
    input.type = "text";
    input.value = currentText.replace("Rs. ", "").replace("%", ""); // cleanup for numbers
    input.style.width = "100%";

    this.innerHTML = "";
    this.appendChild(input);
    input.focus();

    input.addEventListener("blur", () => {
      this.innerText = input.value;
      // TODO: AJAX request send karo yahan to update in DB
    });

    input.addEventListener("keydown", e => {
      if (e.key === "Enter") {
        input.blur();
      }
    });
  });
});
</script>

<!-- jsPDF + AutoTable -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>

<script>
  const selectAll = document.getElementById("selectAll");
  const checkboxes = document.querySelectorAll(".rowCheckbox");
  const downloadBtn = document.getElementById("downloadBtn");

  // Select All Checkbox
  selectAll.addEventListener("change", function () {
    checkboxes.forEach(cb => cb.checked = this.checked);
  });

  // Download PDF
  downloadBtn.addEventListener("click", function () {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    let rows = [];
    let headers = [];

    // Table name from H2 heading
    const tableName = document.querySelector("h2").innerText || "Table Data";

    // Table headers
    document.querySelectorAll("#dataTable thead th").forEach((th, i) => {
      if (i > 0) headers.push(th.innerText); // skip checkbox col
    });

    // Selected rows
    document.querySelectorAll("#dataTable tbody tr").forEach(row => {
      const checkbox = row.querySelector(".rowCheckbox");
      if (checkbox.checked) {
        let rowData = [];
        row.querySelectorAll("td").forEach((td, i) => {
          if (i > 0) rowData.push(td.innerText); // skip checkbox col
        });
        rows.push(rowData);
      }
    });

    if (rows.length === 0) {
      alert("Please select at least one row!");
      return;
    }

    // Add centered heading
    doc.setFontSize(14);
    doc.text(tableName, doc.internal.pageSize.getWidth() / 2, 15, { align: "center" });

    // Add selected rows table
    doc.autoTable({
      head: [headers],
      body: rows,
      startY: 25,
    });

    doc.save(tableName.replace(/\s+/g, "_") + "_Selected.pdf");
  });
</script>
<?php endif; ?>


<?php if ($mode === 'purchase'): ?>
<!-- Search Bar -->
<div style="margin: 20px auto; width: 300px; position: relative;">
  <input 
    type="text" 
    id="purchaseTableSearch" 
    placeholder="Search Department" 
    style="width: 100%; padding: 8px 10px; border-radius: 6px; border: 1px solid #ccc; font-size: 14px; text-align:center;"
    onkeyup="filterPurchaseSuggestions()"
    onfocus="showPurchaseSuggestions()"
    onclick="showPurchaseSuggestions()"
  >
  <div id="purchaseTableSuggestions" style="
    position: absolute;
    top: 38px;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ccc;
    border-top: none;
    border-radius: 0 0 6px 6px;
    max-height: 200px;
    overflow-y: auto;
    display: none;
    z-index: 999;
  ">
    <?php foreach ($tables as $table): 
      $cleanName = ucwords(str_replace('_', ' ', $table));
    ?>
      <a href="?mode=purchase&table=<?= htmlspecialchars($table) ?>" class="purchase-suggestion" style="
        display: block;
        padding: 8px 10px;
        text-decoration: none;
        color: #333;
        border-bottom: 1px solid #f0f0f0;
      " onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='white'">
        <?= $cleanName ?>
      </a>
    <?php endforeach; ?>
  </div>
</div>

<script>
  function filterPurchaseSuggestions() {
    const input = document.getElementById('purchaseTableSearch').value.toLowerCase();
    const suggestions = document.querySelectorAll('.purchase-suggestion');
    let anyVisible = false;

    suggestions.forEach(item => {
      const text = item.textContent.toLowerCase();
      const match = text.includes(input);
      item.style.display = match ? 'block' : 'none';
      if (match) anyVisible = true;
    });

    document.getElementById('purchaseTableSuggestions').style.display = anyVisible ? 'block' : 'none';
  }

  function showPurchaseSuggestions() {
    document.getElementById('purchaseTableSuggestions').style.display = 'block';
  }

  document.addEventListener('click', function(e) {
    const box = document.getElementById('purchaseTableSuggestions');
    const input = document.getElementById('purchaseTableSearch');
    if (!box.contains(e.target) && e.target !== input) {
      box.style.display = 'none';
    }
  });
</script>
<!-- Table Display -->
<?php if ($selectedTable && $tableData): ?>
  <!-- Title + Buttons Row -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 30px; margin-bottom: 20px;">
    <h2 style="margin: 0;">Table: <?= ucwords(str_replace('_', ' ', $selectedTable)) ?></h2>
    <div>
      <button id="downloadBtn" style="padding:6px 12px; background:#6b3de6; color:white; border:none; border-radius:5px; cursor:pointer;">
        Download PDF
      </button>
      <button id="downloadCSVBtn" style="padding:6px 12px; background:#6b3de6; color:white; border:none; border-radius:5px; cursor:pointer; margin-left:8px;">
          Download CSV
        </button>
        <button id="editform" style="padding:6px 12px; background:#6b3de6; color:white; border:none; border-radius:5px; cursor:pointer; margin-left:8px;">
Edit        </button>
        <button id="deleterow" style="padding:6px 12px; background:#6b3de6; color:white; border:none; border-radius:5px; cursor:pointer; margin-left:8px;">
Delete        </button>
       

      </div>
    </div>
  </div>

 <!-- 🔹 Inline Edit Form (Sales jaisa) -->
<div id="editPurchaseForm" style="display:none; background:#f9f9f9; padding:20px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1); margin-bottom:20px;">
  <h3>Edit Purchase Record</h3>
  <form id="purchaseEditForm">
    <input type="hidden" id="editId">

    <div style="display:flex; flex-wrap:wrap; gap:15px;">
      <div>
        <label>Date</label><br>
        <input type="date" id="editDate" style="width:160px;">
      </div>
      <div>
        <label>Detail</label><br>
        <input type="text" id="editDetail" style="width:180px;">
      </div>
      <div>
        <label>Quantity</label><br>
        <input type="number" id="editQty" step="0.01" style="width:100px;">
      </div>
      <div>
        <label>Rate</label><br>
        <input type="number" id="editRate" step="0.01" style="width:100px;">
      </div>
      <div>
        <label>Amount</label><br>
        <input type="number" id="editAmount" readonly style="width:120px; background:#f0f0f0;">
      </div>
    </div>

    <div style="margin-top:15px;">
      <button type="button" id="saveEdit" style="padding:6px 12px; background:#28a745; color:white; border:none; border-radius:5px;">💾 Save</button>
      <button type="button" id="cancelEdit" style="padding:6px 12px; background:#999; color:white; border:none; border-radius:5px;">✖ Cancel</button>
    </div>
  </form>
</div>

<!-- 🔹 Purchase Data Table -->
<table id="dataTable" border="1" cellpadding="6" cellspacing="0" width="100%">
  <thead>
    <tr style="background:#6b3de6; color:white;">
      <th><input type="checkbox" id="selectAll"></th>
      <th>Username</th>
      <th>Date</th>
      <th>Detail</th>
      <th>Qty</th>
      <th>Rate</th>
      <th>Amount</th>
      <th>Created At</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($row = $tableData->fetch_assoc()): ?>
      <tr data-id="<?= $row['id'] ?>">
        <td><input type="checkbox" class="rowCheckbox" value="<?= $row['id'] ?>"></td>
        <td><?= htmlspecialchars($row['username'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['date'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['detail'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['qty'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['rate'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['amount'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['created_at'] ?? '') ?></td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<script>
let selectedRowId = null;

// 🟢 Edit Button Click
document.getElementById("editform").addEventListener("click", () => {
  const selected = document.querySelector(".rowCheckbox:checked");
  if (!selected) {
    alert("Please select a row to edit!");
    return;
  }

  const row = selected.closest("tr");
  selectedRowId = selected.value;

  document.getElementById("editId").value = selectedRowId;
  document.getElementById("editDate").value = row.children[2].innerText.trim();
  document.getElementById("editDetail").value = row.children[3].innerText.trim();
  document.getElementById("editQty").value = row.children[4].innerText.trim();
  document.getElementById("editRate").value = row.children[5].innerText.trim();
  document.getElementById("editAmount").value = row.children[6].innerText.trim();

  document.getElementById("editPurchaseForm").style.display = "block";
  calculatePurchaseAmount();
});

// 🟢 Cancel Button
document.getElementById("cancelEdit").addEventListener("click", () => {
  document.getElementById("editPurchaseForm").style.display = "none";
});

// 🧮 Auto-calc amount
["editQty", "editRate"].forEach(id => {
  document.getElementById(id).addEventListener("input", calculatePurchaseAmount);
});
function calculatePurchaseAmount() {
  const qty = parseFloat(document.getElementById("editQty").value) || 0;
  const rate = parseFloat(document.getElementById("editRate").value) || 0;
  document.getElementById("editAmount").value = (qty * rate).toFixed(2);
}

// 🟢 Save Edit
document.getElementById("saveEdit").addEventListener("click", () => {
  const tableName = new URLSearchParams(window.location.search).get("table");
  const data = {
    id: selectedRowId,
    table: tableName,
    date: document.getElementById("editDate").value,
    detail: document.getElementById("editDetail").value,
    qty: document.getElementById("editQty").value,
    rate: document.getElementById("editRate").value,
    amount: document.getElementById("editAmount").value
  };

  fetch("update_form_purchase.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data)
  })
  .then(res => res.text())
  .then(res => {
    alert("✅ Record updated successfully!");
    location.reload();
  })
  .catch(err => console.error(err));
});

// 🗑️ Delete Row
document.getElementById("deleterow").addEventListener("click", () => {
  const selected = Array.from(document.querySelectorAll(".rowCheckbox:checked")).map(cb => cb.value);
  if (selected.length === 0) {
    alert("Please select at least one row to delete!");
    return;
  }
  if (!confirm("Are you sure you want to delete selected record(s)?")) return;

  const tableName = new URLSearchParams(window.location.search).get("table");

  fetch("delete_purchase.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ ids: selected, table: tableName })
  })
  .then(res => res.text())
  .then(res => {
    alert("🗑️ Selected record(s) deleted!");
    location.reload();
  })
  .catch(err => console.error(err));
});
// ---------------- PDF & CSV DOWNLOAD ------------------

// ✅ DOWNLOAD PDF using html2pdf
document.getElementById("downloadBtn").addEventListener("click", () => {
  const table = document.getElementById("dataTable");
  if (!table) return alert("No table found!");

  const selectedTable = new URLSearchParams(window.location.search).get("table") || "purchase";
  const title = `Purchase Table: ${selectedTable.replace(/_/g, ' ').toUpperCase()}`;

  // Make a temporary wrapper for clean PDF layout
  const pdfWrapper = document.createElement("div");
  pdfWrapper.innerHTML = `
    <h2 style="text-align:center; margin-bottom:10px;">${title}</h2>
  `;
  pdfWrapper.appendChild(table.cloneNode(true));

  const opt = {
    margin: 0.3,
    filename: `${selectedTable}-purchase.pdf`,
    image: { type: 'jpeg', quality: 0.98 },
    html2canvas: { scale: 2 },
    jsPDF: { unit: 'in', format: 'A4', orientation: 'landscape' }
  };

  html2pdf().from(pdfWrapper).set(opt).save();
});
// ---------------- PDF & CSV DOWNLOAD (SELECTED ROWS ONLY) ------------------

// ✅ DOWNLOAD PDF of selected rows
document.getElementById("downloadBtn").addEventListener("click", () => {
  const table = document.getElementById("dataTable");
  const selectedRows = Array.from(table.querySelectorAll(".rowCheckbox:checked"))
    .map(cb => cb.closest("tr"));

  if (selectedRows.length === 0) {
    alert("Please select at least one row to download PDF!");
    return;
  }

  // Clone table header + selected rows only
  const newTable = document.createElement("table");
  newTable.border = "1";
  newTable.cellPadding = "6";
  newTable.cellSpacing = "0";
  newTable.style.width = "100%";
  newTable.innerHTML = "<thead>" + table.querySelector("thead").innerHTML + "</thead>";

  const tbody = document.createElement("tbody");
  selectedRows.forEach(row => {
    tbody.appendChild(row.cloneNode(true));
  });
  newTable.appendChild(tbody);

  // Create wrapper for PDF
  const selectedTable = new URLSearchParams(window.location.search).get("table") || "purchase";
  const wrapper = document.createElement("div");
  wrapper.innerHTML = `<h2 style="text-align:center;">Selected Rows - ${selectedTable.replace(/_/g, ' ')}</h2>`;
  wrapper.appendChild(newTable);

  const opt = {
    margin: 0.3,
    filename: `${selectedTable}-selected.pdf`,
    image: { type: 'jpeg', quality: 0.98 },
    html2canvas: { scale: 2 },
    jsPDF: { unit: 'in', format: 'A4', orientation: 'landscape' }
  };

  html2pdf().from(wrapper).set(opt).save();
});

// ---------------- PDF & CSV DOWNLOAD (SELECTED ROWS ONLY, NO CHECKBOX COLUMN) ------------------

// ✅ DOWNLOAD PDF of selected rows
document.getElementById("downloadBtn").addEventListener("click", () => {
  const table = document.getElementById("dataTable");
  const selectedRows = Array.from(table.querySelectorAll(".rowCheckbox:checked"))
    .map(cb => cb.closest("tr"));

  if (selectedRows.length === 0) {
    alert("Please select at least one row to download PDF!");
    return;
  }

  // Clone table header + selected rows only
  const newTable = document.createElement("table");
  newTable.border = "1";
  newTable.cellPadding = "6";
  newTable.cellSpacing = "0";
  newTable.style.width = "100%";
  
  // Clone header without first column (checkbox)
  const headers = Array.from(table.querySelectorAll("thead th"))
    .slice(1)
    .map(th => `<th>${th.innerText}</th>`)
    .join("");
  newTable.innerHTML = `<thead><tr>${headers}</tr></thead>`;

  // Add only selected rows (excluding first checkbox column)
  const tbody = document.createElement("tbody");
  selectedRows.forEach(row => {
    const cols = Array.from(row.querySelectorAll("td")).slice(1); // skip first col
    const tr = document.createElement("tr");
    cols.forEach(td => {
      const newTd = document.createElement("td");
      newTd.innerText = td.innerText;
      tr.appendChild(newTd);
    });
    tbody.appendChild(tr);
  });
  newTable.appendChild(tbody);

  // Wrapper for PDF
  const selectedTable = new URLSearchParams(window.location.search).get("table") || "purchase";
  const wrapper = document.createElement("div");
  wrapper.innerHTML = `<h2 style="text-align:center;">Selected Rows - ${selectedTable.replace(/_/g, ' ')}</h2>`;
  wrapper.appendChild(newTable);

  const opt = {
    margin: 0.3,
    filename: `${selectedTable}-selected.pdf`,
    image: { type: 'jpeg', quality: 0.98 },
    html2canvas: { scale: 2 },
    jsPDF: { unit: 'in', format: 'A4', orientation: 'landscape' }
  };

  html2pdf().from(wrapper).set(opt).save();
});


// ✅ DOWNLOAD CSV of selected rows
document.getElementById("downloadCSVBtn").addEventListener("click", () => {
  const table = document.getElementById("dataTable");
  const selectedRows = Array.from(table.querySelectorAll(".rowCheckbox:checked"))
    .map(cb => cb.closest("tr"));

  if (selectedRows.length === 0) {
    alert("Please select at least one row to download CSV!");
    return;
  }

  let csv = [];
  // Add header row (without checkbox column)
  const headers = Array.from(table.querySelectorAll("thead th"))
    .slice(1)
    .map(th => `"${th.innerText.replace(/"/g, '""')}"`)
    .join(",");
  csv.push(headers);

  // Add selected row data (skip checkbox column)
  selectedRows.forEach(row => {
    const cols = Array.from(row.querySelectorAll("td")).slice(1);
    const rowData = cols.map(td => `"${td.innerText.replace(/"/g, '""')}"`).join(",");
    csv.push(rowData);
  });

  const blob = new Blob([csv.join("\n")], { type: "text/csv" });
  const url = window.URL.createObjectURL(blob);
  const a = document.createElement("a");

  const selectedTable = new URLSearchParams(window.location.search).get("table") || "purchase";
  a.href = url;
  a.download = `${selectedTable}-selected.csv`;

  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  window.URL.revokeObjectURL(url);
});

</script>
<?php endif; ?>
    


<?php elseif ($mode === 'customers'): ?>
  <input type="text" id="customerSearch" placeholder="Search Department" style="
    width: 15%;
    padding: 8px 10px;
    margin-bottom: 15px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
    text-align:center;
  ">

  <table id="customersTable" style="width: 100%; border-collapse: collapse; background-color: white;">
    <thead>
      <tr style="background-color: #6b3de6; color: white;box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
          z-index: 1000;">
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Designation</th>
        <th>NTN</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $tableData->fetch_assoc()): ?>
      <tr style="border-bottom: 1px solid #ddd;">
        <td><?= $row["id"] ?></td>
        <td><?= htmlspecialchars($row["name"]) ?></td>
        <td><?= htmlspecialchars($row["email"]) ?></td>
        <td><?= htmlspecialchars($row["phone"]) ?></td>
        <td><?= htmlspecialchars($row["designation"]) ?></td>
        <td><?= htmlspecialchars($row["ntn"]) ?></td>
        <td><?= $row["created_at"] ?></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <script>
    const searchInput = document.getElementById('customerSearch');
    const table = document.getElementById('customersTable').getElementsByTagName('tbody')[0];

    searchInput.addEventListener('keyup', function () {
      const filter = this.value.toLowerCase();
      const rows = table.getElementsByTagName('tr');

      for (let i = 0; i < rows.length; i++) {
        const nameCell = rows[i].getElementsByTagName('td')[1]; // Name column is index 1
        if (nameCell) {
          const nameText = nameCell.textContent || nameCell.innerText;
          rows[i].style.display = nameText.toLowerCase().includes(filter) ? '' : 'none';
        }
      }
    });
  </script>
<?php endif; ?>
<?php if ($mode === 'ledger'): ?>
  <!-- Ledger Search Bar -->
  <div style="margin: 30px auto; text-align: center;">
    <input type="text" id="ledgerSearch" placeholder="Search Department"
      style="padding: 10px 14px; border: 1px solid #ccc; border-radius: 8px; width: 280px; text-align:center;"
      onkeyup="searchLedgerTable()" />

    <ul id="ledgerSuggestions" style="
      list-style: none;
      padding: 0;
      margin-top: 10px;
      width: 280px;
      margin-left: auto;
      margin-right: auto;
      border: 1px solid #ddd;
      border-radius: 6px;
      background: white;
      display: none;
      max-height: 200px;
      overflow-y: auto;
    "></ul>

    <div id="ledgerResult" style="margin-top: 30px;"></div>
  </div>

  <script>
    function toTitleCase(str) {
      return str.replace(/_/g, ' ')
                .replace(/\w\S*/g, w => w.charAt(0).toUpperCase() + w.slice(1));
    }

    function searchLedgerTable() {
      const query = document.getElementById('ledgerSearch').value.trim();
      if (query.length < 2) {
        document.getElementById('ledgerSuggestions').style.display = 'none';
        return;
      }

      fetch(`get_common_tables.php?query=${query}`)
        .then(res => res.json())
        .then(data => {
          const ul = document.getElementById('ledgerSuggestions');
          ul.innerHTML = '';
          data.forEach(table => {
            const li = document.createElement('li');
            li.textContent = toTitleCase(table);
            li.setAttribute('data-table', table);
            li.style.padding = '8px 12px';
            li.style.cursor = 'pointer';
            li.onmouseover = () => li.style.background = '#f0f0f0';
            li.onmouseout = () => li.style.background = 'white';
            li.onclick = () => loadLedgerData(table);
            ul.appendChild(li);
          });
          ul.style.display = data.length ? 'block' : 'none';
        });
    }

    function loadLedgerData(table) {
      fetch(`fetch_table_data.php?table=${table}`)
        .then(res => res.text())
        .then(html => {
          document.getElementById('ledgerResult').innerHTML = html;
          document.getElementById('ledgerSuggestions').style.display = 'none';
        });
    }
  </script>
<?php endif; ?>
<?php if ($mode === 'invoices'): ?>
    <div style="padding: 30px;">
        <h2 style="margin-bottom: 20px;">Available Invoices</h2>
        <div style="display: flex; flex-wrap: wrap; gap: 20px;">
            <?php
            $imgFolder = 'Img/';
            $imgExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'pdf', 'PDF', 'PNG', 'JPEG', 'JPG', 'GIF'];
            $imgFiles = glob($imgFolder . '*.{jpg,jpeg,png,gif,bmp,webp,pdf,PDF,PNG,JPEG,JPG,GIF}', GLOB_BRACE);
            if (!empty($imgFiles)):
                foreach ($imgFiles as $img):
                    $fileName = basename($img);
            ?>
                <div style="text-align: center;">
                    <a href="<?= $img ?>" target="_blank" download="<?= $fileName ?>">
                        <img src="<?= $img ?>" alt="<?= $fileName ?>" style="width: 200px; height: auto; border: 1px solid #ccc; border-radius: 8px; padding: 5px;" />
                    </a>
                    <p style="font-size: 14px;"><?= $fileName ?></p>
                </div>
            <?php
                endforeach;
            else:
                echo "<p>No invoice images found in the folder.</p>";
            endif;
            ?>
        </div>
    </div>
<?php endif; ?>

<?php if ($mode === 'form'): ?>
  <div style="padding: 30px;">
    <h2 style="margin-bottom: 20px;">Available PDFs</h2>
    <table style="width: 100%; border-collapse: collapse; background: white;">
      <thead style="background: #6b3de6; color: white;box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
          z-index: 1000;">
        <tr>
          <th style="padding: 12px;">#</th>
          <th style="padding: 12px;">File Name</th>
          <th style="padding: 12px;">Preview</th>
          <th style="padding: 12px;">Download</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $pdfFolder = 'pdf/';
        $pdfFiles = glob($pdfFolder . '*.pdf');
        $counter = 1;
        foreach ($pdfFiles as $file):
          $fileName = basename($file);
        ?>
          <tr style="border-bottom: 1px solid #ddd;">
            <td style="padding: 10px;"><?= $counter++ ?></td>
            <td style="padding: 10px;"><?= htmlspecialchars($fileName) ?></td>
            <td style="padding: 10px;">
              <a href="<?= $file ?>" target="_blank" style="color: #007bff;">Preview</a>
            </td>
            <td style="padding: 10px;">
              <a href="<?= $file ?>" download style="color: #28a745;">Download</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <?php if (empty($pdfFiles)): ?>
      <p>No PDF files found in the folder.</p>
    <?php endif; ?>
  </div>
<?php endif; ?>
