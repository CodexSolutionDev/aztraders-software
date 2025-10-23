<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: /");
    exit;
}
if (basename($_SERVER['PHP_SELF']) !== 'view.php') {
    unset($_SESSION['access_granted_view']);
}
include 'fetch_customers.php'; // This pulls data into $customers array
?>
<?php
// Database config
$db = new mysqli("", "", "", ""); // ← Replace with your DB

$tables = [];
$result = $db->query("SHOW TABLES");
if ($result) {
    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Expense</title>
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
    z-index: 1000;
    position: fixed;
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
  <a href="/department">Department</a>
  <a href="/staff">Staff</a>
    <a href="/Form">Form</a>
    <a href="/expense"style="background-color: #5830be;">Expense</a>
    <a href="/ledger">Ledger</a>
<a href="/view">View</a>
</div>
<!-- 🔍 Centered Search Section -->
<div id="searchSection" style="
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 80px 20px;
  width: 100%;
  background: #f8f8ff;
">

  <!-- Search Container -->
  <div style="
    background: rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border-radius: 20px;
    padding: 40px 30px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 500px;
    border: 1px solid rgba(255, 255, 255, 0.3);
  ">
    <form method="GET" action="" autocomplete="off">
      <div style="position: relative;">
        <input type="text" id="companySearch" placeholder="Type to search..." style="
          width: 100%;
          padding: 16px;
          border-radius: 12px;
          border: none;
          font-size: 18px;
          outline: none;
          margin-bottom: 15px;
          box-shadow: inset 0 2px 6px rgba(0, 0, 0, 0.05);
        ">

        <ul id="suggestions" style="
          position: absolute;
          top: 100%;
          left: 0;
          right: 0;
          background: white;
          border: 1px solid #ccc;
          max-height: 150px;
          overflow-y: auto;
          display: none;
          list-style: none;
          padding: 0;
          margin: 0;
          z-index: 10;
          border-radius: 0 0 10px 10px;
        "></ul>
      </div>

      <input type="hidden" name="selected_table" id="selected_table">

      <button type="submit" style="
        width: 100%;
        padding: 14px;
        background-color: #6b3de6;
        color: white;
        font-size: 18px;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        transition: background-color 0.3s;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
      ">
        Select
      </button>
    </form>
  </div>
</div>

<script>
  const companySearch = document.getElementById('companySearch');
  const suggestionsBox = document.getElementById('suggestions');
  const hiddenInput = document.getElementById('selected_table');

  const companies = [
    <?php foreach ($tables as $table): ?>
      "<?= str_replace('_', ' ', htmlspecialchars($table)) ?>",
    <?php endforeach; ?>
  ];

  companySearch.addEventListener('input', function () {
    const query = this.value.toLowerCase().trim();
    suggestionsBox.innerHTML = '';
    if (!query) return suggestionsBox.style.display = 'none';

    const matches = companies.filter(c => c.toLowerCase().includes(query));
    matches.forEach(match => {
      const item = document.createElement('li');
      item.textContent = match;
      item.style.padding = '10px';
      item.style.cursor = 'pointer';
      item.addEventListener('click', () => {
        companySearch.value = match;
        hiddenInput.value = match.replace(/ /g, '_');
        suggestionsBox.style.display = 'none';
      });
      suggestionsBox.appendChild(item);
    });

    suggestionsBox.style.display = matches.length ? 'block' : 'none';
  });

  document.addEventListener('click', function (e) {
    if (!companySearch.contains(e.target) && !suggestionsBox.contains(e.target)) {
      suggestionsBox.style.display = 'none';
    }
  });
</script>


</div>
<div id="tableSection" style="display: none; margin-top: 40px;">
<?php if (isset($_GET['selected_table']) && in_array($_GET['selected_table'], $tables)): ?>
  <?php $selectedTable = $_GET['selected_table']; ?>
  
  <!-- Data Entry Container -->
  <div style="
    margin-top: 40px;
    display: flex;
    justify-content: center;
  ">
    <div style="
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border-radius: 16px;
      padding: 30px;
      box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
      width: 95%;
      border: 1px solid rgba(255, 255, 255, 0.18);
      margin-top: 30px;
    ">
        <div style="text-align: right;">
  <button onclick="toggleSections()" style="
    background: none;
    border: none;
    font-size: 24px;
    color: #6b3de6;
    cursor: pointer;
  ">✖</button>
</div>

      <h2 style="text-align: center; color: #333; margin-bottom: 20px;">
        <?= htmlspecialchars(str_replace('_', ' ', $selectedTable)) ?>
      </h2>

      <form method="POST" action="/save_to_table">
        <input type="hidden" name="table_name" value="<?= htmlspecialchars($selectedTable) ?>">
  <input type="hidden" name="username" value="<?= htmlspecialchars($_SESSION['username'] ?? '') ?>">

        <table id="dataTable" style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
  <thead>
    <tr style="background-color: #eee; text-align: left;">
      <th style="padding: 10px;">Sr No</th>
      <th style="padding: 10px;">Date</th>
      <th style="padding: 10px;">Detail of Purchase</th>
      <th style="padding: 10px;">Quantity</th>
      <th style="padding: 10px;">Rate</th>
      <th style="padding: 10px;">Amount</th>
      <th style="padding: 10px;"></th>
    </tr>
  </thead>
  <tbody>
  <tr>
    <td class="sr-no">1</td>
    <td><input type="date" name="data[0][date]" required class="styled-input"></td>
    <td><input type="text" name="data[0][detail]" required class="styled-input"></td>
    <td><input type="number" name="data[0][qty]" required class="styled-input"></td>
    <td><input type="number" name="data[0][rate]" required class="styled-input"></td>
    <td>
      <input type="number" name="data[0][amount]" readonly class="styled-input">
      <div class="formatted-amount" style="display: none;font-size: 12px; color: gray;"></div>
    </td>
    <td><button type="button" onclick="removeRow(this)">❌</button></td>
  </tr>
</tbody>

  <tfoot>
  <tr style="background-color: #f9f9f9;">
    <td colspan="5" style="text-align: right; padding: 10px; font-weight: bold;">Grand Total:</td>
    <td id="grandTotal" style="padding: 10px; font-weight: bold;">0.00</td>
    <td></td>
  </tr>
</tfoot>
</table>
<style>
  .styled-input {
    width: 100%;
    padding: 8px 10px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 14px;
    outline: none;
    box-sizing: border-box;
  }

  .styled-input:focus {
    border-color: #6b3de6;
    box-shadow: 0 0 5px rgba(107, 61, 230, 0.3);
  }
</style>


        <div style="display: flex; justify-content: space-between;">
          <button type="button" onclick="addRow()" style="padding: 10px 20px; background-color: #4caf50; color: white; border: none; border-radius: 8px; cursor: pointer;">
            + Add Line
          </button>

          <button type="submit" style="padding: 10px 20px; background-color: #6b3de6; color: white; border: none; border-radius: 8px; cursor: pointer;">
            💾 Save
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
  <!-- JavaScript for Dynamic Rows -->
    <script>
let rowIndex = 1;

// ➕ Add Row
function addRow() {
  const tableBody = document.querySelector("#dataTable tbody");
  const newRow = document.createElement("tr");

  newRow.innerHTML = `
    <td class="sr-no"></td>
    <td><input type="date" name="data[${rowIndex}][date]" required class="styled-input"></td>
    <td><input type="text" name="data[${rowIndex}][detail]" required class="styled-input"></td>
    <td><input type="number" name="data[${rowIndex}][qty]" required class="styled-input"></td>
    <td><input type="number" name="data[${rowIndex}][rate]" required class="styled-input"></td>
    <td>
      <input type="number" name="data[${rowIndex}][amount]" readonly class="styled-input">
      <div class="formatted-amount" style="display: none; font-size: 12px; color: gray;"></div>
    </td>
    <td><button type="button" onclick="removeRow(this)">❌</button></td>
  `;

  tableBody.appendChild(newRow);
  updateSrNo();
  rowIndex++;
}

// ❌ Remove Row
function removeRow(btn) {
  btn.closest("tr").remove();
  updateSrNo();
  updateGrandTotal();
}

// 🔢 Update Serial Number
function updateSrNo() {
  document.querySelectorAll("#dataTable .sr-no").forEach((cell, index) => {
    cell.textContent = index + 1;
  });
}

// 💰 Update Grand Total
function updateGrandTotal() {
  let total = 0;
  document.querySelectorAll("[name*='[amount]']").forEach(input => {
    const val = parseFloat(input.value || 0);
    if (!isNaN(val)) total += val;
  });
  document.getElementById("grandTotal").textContent = total.toLocaleString('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });
}

// 🧮 Auto-calculate Amount + Total
document.addEventListener("input", e => {
  const row = e.target.closest("tr");
  if (!row) return;

  const qty = parseFloat(row.querySelector("[name*='[qty]']")?.value) || 0;
  const rate = parseFloat(row.querySelector("[name*='[rate]']")?.value) || 0;
  const amountInput = row.querySelector("[name*='[amount]']");
  const formatted = row.querySelector(".formatted-amount");

  const amount = qty * rate;
  if (amount > 0) {
    amountInput.value = amount.toFixed(2);
    if (formatted) formatted.textContent = amount.toLocaleString('en-US', { minimumFractionDigits: 2 });
  } else {
    amountInput.value = "";
    if (formatted) formatted.textContent = "";
  }

  updateGrandTotal();
});
</script>

<script>
  // Show table, hide search section
  window.onload = function () {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('selected_table')) {
      document.getElementById('searchSection').style.display = 'none';
      document.getElementById('tableSection').style.display = 'block';
    }
  }

  // Close button handler
  function toggleSections() {
    document.getElementById('searchSection').style.display = 'flex';
    document.getElementById('tableSection').style.display = 'none';

    // Clear query string
    const baseUrl = window.location.href.split('?')[0];
    window.history.replaceState({}, document.title, baseUrl);
  }
</script>

<?php endif; ?>
