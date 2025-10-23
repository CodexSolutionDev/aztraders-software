-<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: /");
    exit;
}
if (basename($_SERVER['PHP_SELF']) !== 'view.php') {
    unset($_SESSION['access_granted_view']);
}
?>
<?php
// DB Connection
$conn = new mysqli("", "", "", "");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get all table names except 'customers' and 'sales'
$tables = [];
$sql = "SHOW TABLES";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_array()) {
        $tableName = $row[0];
        if ($tableName !== 'sales' && $tableName !== 'customers' && $tableName !== 'vendor_bills') {
            $tables[] = $tableName;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sales</title>
  <link rel="icon" type="image/png" sizes="32x32" href="/images/weblogo.png?v=2">
<link rel="icon" type="image/png" sizes="16x16" href="/images/weblogo.png?v=2">
<link rel="shortcut icon" href="/images/weblogo.png?v=2">

      <script src="https://cdn.tailwindcss.com"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<head>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdn.tailwindcss.com"></script>

    <style>
 
  body {
    margin-top: 10px;
  }
</style>
<style>
    .dropdown {
      display: inline-block;
      position: relative;
    }

    .dropdown-content {
      display: none;
      position: absolute;
      background-color: #7a55e6;
      min-width: 160px;
      z-index: 1;
      left: 0;
    }

    .dropdown-content a {
      color: white;
      padding: 12px 16px;
      text-decoration: none;
      display: block;
      text-align: left;
    }

    .dropdown-content a:hover {
      background-color: #5a2bc3;
    }


    .container {
  display: flex;
  align-items: center;
  gap: 16px;
margin-left: 20px;
    margin-top: 20px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.new-button {
  background-color: #6b3de6;
  color: white;
  padding: 10px 18px;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  font-weight: bold;
  text-decoration: none;
  cursor: pointer;
  transition: background 0.3s ease;
}

.new-button:hover {
  background-color: #532bc0;
}

.label {
  padding: 8px 16px;
  border-radius: 6px;
}

.label-text {
  font-size: 14px;
  font-weight: 600;
  color: #333;
}

  </style>
 <div class="menu-bar fixed top-0 left-0 w-full bg-[#6b3de6] shadow z-50">
  <div class="flex justify-between items-center px-6 py-3 text-white font-medium" style="
    padding-bottom: unset;
    padding-top: unset;
">
    
    <!-- Centered Menu Links -->
    <div class="flex-1 flex justify-center space-x-6">
      <a href="/home" class="hover:bg-purple-800 px-4 py-2 rounded" >Dashboard</a>
      <a href="/sales" class="hover:bg-purple-800 px-4 py-2 rounded"style="background-color: #5830be;">Sales</a>
      <a href="/purchase" class="hover:bg-purple-800 px-4 py-2 rounded">Purchases</a>
      <a href="/department" class="hover:bg-purple-800 px-4 py-2 rounded">Department</a>
      <a href="/staff" class="hover:bg-purple-800 px-4 py-2 rounded">Staff</a>
      <a href="/Form" class="hover:bg-purple-800 px-4 py-2 rounded">Form</a>
      <a href="/ledger" class="hover:bg-purple-800 px-4 py-2 rounded">Ledger</a>           
      <a href="/expense" class="hover:bg-purple-800 px-4 py-2 rounded">Expense</a>
      <a href="/view" class="hover:bg-purple-800 px-4 py-2 rounded">View</a>
    </div>

    <!-- Right Side: Username -->
    <div id="username-display" class="text-sm font-semibold" >
      <?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?>
    </div>

  </div>
</div>


  </head>

<body>

<title>Sales Form</title>
  <style>
  body {
    font-family: Arial, sans-serif;
    background: #f5f5f5;
    margin: 0;
    margin-top: 50px;
    padding-top: 0px;
  }

  .form-container {
    max-width: 98%;
    background: white;
    margin: auto;
    padding: 1rem;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow-x: hidden;
  }

  .inv-number {
    font-size: 24px;
    font-weight: bold;
    border-bottom: 2px solid #333;
    padding-bottom: 8px;
    margin-bottom: 24px;
  }

 
  table {
    width: 100%;
    max-width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
  }

  #salesTable th,
  #salesTable td {
    border: 1px solid #ccc;
    padding: 6px;
    text-align: center;
  }

  #salesTable input,
  #salesTable select {
    width: 100%;
    box-sizing: border-box;
    padding: 4px;
    font-size: 13px;
  }

  th {
    background-color: #f0f0f0;
  }

  .add-line-btn {
    margin-top: 15px;
    padding: 10px 18px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
  }

  .add-line-btn:hover {
    background-color: #0056b3;
  }
</style>

<style>
  .invoice-controls {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
    margin-top: 20px;
  }

  .invoice-controls .buttons,
  .add-line-button,
  .label {
    display: flex;
    align-items: center;
    gap: 6px;
  }

  button {
    padding: 4px 10px;
    font-size: 13px;
    border: 1px solid #ccc;
    color: white;
    font-feature-settings: normal;
    border-radius: 5px;
    background-color: #6b3de6;
    cursor: pointer;
    transition: background-color 0.2s ease;
  }

  button:hover {
    background-color: #e0e0e0;
  }

  .label span {
    font-weight: bold;
    font-size: 14px;
  }
</style>

<div class="invoice-controls"style="
    margin-top: unset;
">
  <div class="add-line-button">
    <button onclick="addRow()">➕ Add Line</button>
  </div>
  <div class="buttons">
    <button onclick="handleNew()">➕ New</button>
    <button onclick="saveToDB()">💾 Save Invoice</button>
  </div>
  <div class="label">
    🧾 <span>SALES</span>
  </div>
</div>

<div class="form-container">
<style>
  body {
    font-family: 'Segoe UI', sans-serif;
    background-color: #f9f9f9;
  }

  .form-row {
    display: flex;
    justify-content: center;
    align-items: flex-end;
    gap: 30px;
    flex-wrap: wrap;
    margin-top: 20px;
  }

  .form-group {
    display: flex;
    flex-direction: column;
    font-size: 14px;
    min-width: 220px;
  }

  .form-group label {
    margin-bottom: 6px;
    font-weight: 500;
    color: #444;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  select {
    padding: 10px 12px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 14px;
    background: #fff;
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    margin-bottom: 7;
  }

  select:focus {
    border-color: #3b82f6;
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
  }

  /* Custom file upload */
  .file-upload input[type="file"] {
    display: none;
  }

  .custom-file-label {
    background-color: #f1f1f1;
    border: 1px solid #ccc;
    padding: 10px 14px;
    border-radius: 8px;
    font-size: 14px;
    cursor: pointer;
    transition: background-color 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
  }

  .custom-file-label:hover {
    background-color: #e2e2e2;
  }
</style>
<!-- Layout -->
<div class="form-row" style="display: flex; align-items: flex-end; gap: 20px; margin-bottom: 20px; flex-wrap: wrap;">
  <!-- Selected Company -->
  <div style="min-width: 200px; flex: 1;">
    <label style="font-size: 14px; font-weight: bold;">Selected Department:</label>
    <div id="selectedCompanyDisplay" style="padding: 10px 14px; background-color: #f0f0f0; border-radius: 6px; font-weight: bold;">None</div>
  </div>

  <!-- Search Company -->
  <div class="form-group" style="flex: 1.2; min-width: 180px; position: relative;">
    <label style="font-size: 14px; font-weight: bold;">Search Department:</label>
    <input type="text" id="companySearch" placeholder="Search..." style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc;" autocomplete="off">
    <ul id="suggestions" style="position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ccc; max-height: 150px; overflow-y: auto; display: none; list-style: none; padding: 0; margin: 0; z-index: 10;"></ul>
    <input type="hidden" name="selected_table" id="selected_table">
  </div>

 <!-- File Upload -->
<div class="form-group" style="flex: 1.2; min-width: 180px;">
  <label for="invoiceFile" style="font-size: 14px; font-weight: bold;">Upload Invoice</label>

  <label id="fileLabel" for="invoiceFile" class="file-badge" style="width: 100%;">
    <span id="fileName">No file chosen</span>
    <span id="viewFile" class="view-icon" style="display: none;">👁</span>
    <span id="deleteFile" class="delete-icon" style="display: none;">🗑</span>
  </label>

  <input type="file" id="invoiceFile" accept="image/*,.pdf" style="display: none;">
</div>

</div>

<!-- Modal for Preview -->
<div id="previewModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); align-items: center; justify-content: center; z-index: 1000;">
  <div style="position: relative; background: #fff; padding: 10px; border-radius: 8px; max-width: 90%; max-height: 90%;">
    <span id="closePreview" style="position: absolute; top: -12px; right: -12px; background: #fff; border: 1px solid #ccc; padding: 4px 8px; border-radius: 50%; cursor: pointer; font-weight: bold;">×</span>
    <iframe id="previewFrame" style="display: none; width: 100%; height: 80vh;" frameborder="0"></iframe>
    <img id="previewImage" src="" alt="Preview" style="max-width: 100%; max-height: 80vh; display: none;">
  </div>
</div>

<style>
  .file-badge {
    padding: 6px 10px;
    border: 1px solid #ccc;
    border-radius: 20px;
    font-size: 13px;
    color: #333;
    background: #f5f5f5;
    display: inline-flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    cursor: pointer;
  }

  .file-badge:hover {
    background-color: #eaeaea;
  }

  .delete-icon, .view-icon {
    font-weight: bold;
    cursor: pointer;
  }

  .delete-icon {
    color: red;
  }

  .view-icon {
    color: #007bff;
  }

  .view-icon:hover {
    text-decoration: underline;
  }
</style>


<script>
  // Company Search Logic
  const companySearch = document.getElementById('companySearch');
  const suggestionsBox = document.getElementById('suggestions');
  const hiddenInput = document.getElementById('selected_table');
  const displayBox = document.getElementById('selectedCompanyDisplay');

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
      item.style.padding = '8px';
      item.style.cursor = 'pointer';
      item.addEventListener('click', () => {
        companySearch.value = match;
        hiddenInput.value = match.replace(/ /g, '_');
        displayBox.textContent = match;
        suggestionsBox.style.display = 'none';
      });
      suggestionsBox.appendChild(item);
    });

    suggestionsBox.style.display = matches.length > 0 ? 'block' : 'none';
  });

  document.addEventListener('click', function (e) {
    if (!companySearch.contains(e.target) && !suggestionsBox.contains(e.target)) {
      suggestionsBox.style.display = 'none';
    }
  });

  // File Upload Logic
  const fileInput = document.getElementById('invoiceFile');
  const fileLabel = document.getElementById('fileLabel');
  const fileNameSpan = document.getElementById('fileName');
  const deleteIcon = document.getElementById('deleteFile');
  const viewIcon = document.getElementById('viewFile');
  const previewModal = document.getElementById('previewModal');
  const previewImage = document.getElementById('previewImage');
  const previewFrame = document.getElementById('previewFrame');
  const closePreview = document.getElementById('closePreview');

  let fileURL = '';

  fileInput.addEventListener('change', function () {
    const file = this.files[0];
    if (file) {
      fileURL = URL.createObjectURL(file);
      fileNameSpan.textContent = file.name;
      deleteIcon.style.display = 'inline-block';
      viewIcon.style.display = 'inline-block';
    }
  });

  // Open preview from label (but not when clicking icons)
  fileLabel.addEventListener('click', function (e) {
    const file = fileInput.files[0];
    if (!file || e.target === deleteIcon || e.target === viewIcon) return;
    openPreview(file);
  });

  // Open preview from view icon
  viewIcon.addEventListener('click', function (e) {
    e.stopPropagation();
    const file = fileInput.files[0];
    if (file) openPreview(file);
  });

  function openPreview(file) {
    if (file.type.includes('pdf')) {
      previewFrame.src = fileURL;
      previewFrame.style.display = 'block';
      previewImage.style.display = 'none';
    } else {
      previewImage.src = fileURL;
      previewImage.style.display = 'block';
      previewFrame.style.display = 'none';
    }
    previewModal.style.display = 'flex';
  }

  // Delete file
  deleteIcon.addEventListener('click', function (e) {
    e.stopPropagation();
    fileInput.value = '';
    fileNameSpan.textContent = 'No file chosen';
    deleteIcon.style.display = 'none';
    viewIcon.style.display = 'none';
    previewModal.style.display = 'none';
  });

  // Close modal
  closePreview.addEventListener('click', () => {
    previewModal.style.display = 'none';
  });
</script>





<table id="salesTable">
  <thead>
    <tr>
        <th>Name</th>
      <th>Bill Amount</th>
      <th>Cheque</th>
      <th>Total GST</th>
      <th>GST 1/5</th>
      <th>Remaining GST</th>
      <th><input type="number" step="0.01" id="vendorPercent" placeholder="0">Vendor %</th>
      <th><input type="number" step="0.01" id="cashierPercent" placeholder="0">Cashier %</th>
      <th><input type="number" step="0.01" id="agPercent" placeholder="0">AG %</th>
      <th><input type="number" step="0.01" id="officePercent" placeholder="0">Office %</th>
      <th>Balance</th>
      <th>Firm</th>
    </tr>
  </thead>
  <tbody></tbody>
  <tfoot>
    <tr id="totalsRow">
        <th></th>
      <th id="totalBill">0.00</th>
      <th id="totalCheque">0.00</th>
      <th id="totalGstFooter">0.00</th>
      <th id="totalGst1_5">0.00</th>
      <th id="totalRemGst">0.00</th>
      <th id="totalVendor">0.00</th>
      <th id="totalCashier">0.00</th>
      <th id="totalAg">0.00</th>
      <th id="totalOffice">0.00</th>
      <th id="totalBal">0.00</th>
      <th></th>
    </tr>
  </tfoot>
</table>

<script>
let lastRow = null;

function addRow() {
  const tbody = document.querySelector("#salesTable tbody");
  const row = document.createElement("tr");

  row.innerHTML = `
  <td><input type="text" class="username" value="${loggedInUser}" readonly /></td>
    <td><input type="number" class="bill" step="0.01" /></td>
    <td><input type="number" class="cheque" step="0.01" /></td>
    <td><input type="number" class="totalGst" step="0.01" /></td>
    <td><input type="number" class="gst1_5" readonly /></td>
    <td><input type="number" class="remGst" readonly /></td>
    <td><input type="number" class="vendor" readonly /></td>
    <td><input type="number" class="cashier" readonly /></td>
    <td><input type="number" class="ag" readonly /></td>
    <td><input type="number" class="office" readonly /></td>
    <td><input type="number" class="balance" readonly /></td>
    <td>
      <select class="firm">
        <option value="AZ TRADERS">AZ TRADERS</option>
        <option value="FAIZAN TRADERS">FAIZAN TRADERS</option>
        <option value="B&M ENTERPRISES">B&M ENTERPRISES</option>
      </select>
    </td>
  `;

  tbody.appendChild(row);
  lastRow = row; // Save reference to latest row
}

function calculateRow(row) {
  const bill = parseFloat(row.querySelector(".bill")?.value) || 0;
  const cheque = parseFloat(row.querySelector(".cheque")?.value) || 0;
  const totalGst = parseFloat(row.querySelector(".totalGst")?.value) || 0;

  const gst1_5 = totalGst * 0.20;
  const remGst = totalGst - gst1_5;

  const vendorPercent = parseFloat(document.getElementById("vendorPercent")?.value) || 0;
  const cashierPercent = parseFloat(document.getElementById("cashierPercent")?.value) || 0;
  const agPercent = parseFloat(document.getElementById("agPercent")?.value) || 0;
  const officePercent = parseFloat(document.getElementById("officePercent")?.value) || 0;

  const vendor = cheque * vendorPercent / 100;
  const cashier = cheque * cashierPercent / 100;
  const ag = cheque * agPercent / 100;
  const office = cheque * officePercent / 100;

  const balance = cheque - (remGst + vendor + cashier + ag + office);

  row.querySelector(".gst1_5").value = gst1_5.toFixed(2);
  row.querySelector(".remGst").value = remGst.toFixed(2);
  row.querySelector(".vendor").value = vendor.toFixed(2);
  row.querySelector(".cashier").value = cashier.toFixed(2);
  row.querySelector(".ag").value = ag.toFixed(2);
  row.querySelector(".office").value = office.toFixed(2);
  row.querySelector(".balance").value = balance.toFixed(2);
}

document.querySelectorAll("#vendorPercent, #cashierPercent, #agPercent, #officePercent").forEach(input => {
  input.addEventListener("input", () => {
    if (lastRow) {
      calculateRow(lastRow);
      calculateTotals();
    }
  });
});

document.addEventListener("input", function (e) {
  if (e.target.closest("#salesTable tbody")) {
    if (lastRow) {
      calculateRow(lastRow);
      calculateTotals();
    }
  }
});

function calculateTotals() {
  const rows = document.querySelectorAll("#salesTable tbody tr");

  let totals = {
    bill: 0, cheque: 0, totalGst: 0, gst1_5: 0, remGst: 0,
    vendor: 0, cashier: 0, ag: 0, office: 0, balance: 0
  };

  rows.forEach(row => {
    const getVal = cls => parseFloat(row.querySelector(cls)?.value) || 0;

    totals.bill += getVal(".bill");
    totals.cheque += getVal(".cheque");
    totals.totalGst += getVal(".totalGst");
    totals.gst1_5 += getVal(".gst1_5");
    totals.remGst += getVal(".remGst");
    totals.vendor += getVal(".vendor");
    totals.cashier += getVal(".cashier");
    totals.ag += getVal(".ag");
    totals.office += getVal(".office");
    totals.balance += getVal(".balance");
  });

const format = num => Number(num.toFixed(2)).toLocaleString('en-US');

  document.getElementById("totalBill").innerText = format(totals.bill);
  document.getElementById("totalCheque").innerText = format(totals.cheque);
  document.getElementById("totalGstFooter").innerText = format(totals.totalGst);
  document.getElementById("totalGst1_5").innerText = format(totals.gst1_5);
  document.getElementById("totalRemGst").innerText = format(totals.remGst);
  document.getElementById("totalVendor").innerText = format(totals.vendor);
  document.getElementById("totalCashier").innerText = format(totals.cashier);
  document.getElementById("totalAg").innerText = format(totals.ag);
  document.getElementById("totalOffice").innerText = format(totals.office);
  document.getElementById("totalBal").innerText = format(totals.balance);
}
</script>



<script>
    function handleNew() {
  fetch('get_new_invoice.php')
    .then(response => response.json())
    .then(data => {
      if (data.invoice_number) {
        // Update the invoice number on the page
        const invNumberElem = document.querySelector('.inv-number');
        if (invNumberElem) {
          invNumberElem.textContent = data.invoice_number;
        }
      } else {
        alert("Failed to generate new invoice number.");
      }
    })
    .catch(err => {
      console.error(err);
      alert("Error generating new invoice number.");
    });
}

</script>
<script>
function saveToDB() {
  // Get percent values from table header
  const vendorPercentVal  = parseFloat(document.getElementById("vendorPercent")?.value) || 10;
  const cashierPercentVal = parseFloat(document.getElementById("cashierPercent")?.value) || 0;
  const agPercentVal      = parseFloat(document.getElementById("agPercent")?.value) || 8;
  const officePercentVal  = parseFloat(document.getElementById("officePercent")?.value) || 7;

  const rows = document.querySelectorAll("#salesTable tbody tr");
  const data = [];

  rows.forEach(row => {
    const rowData = [
      row.querySelector(".username")?.value?.trim() || "",
      parseFloat(row.querySelector(".bill")?.value) || 0,
      parseFloat(row.querySelector(".cheque")?.value) || 0,
      parseFloat(row.querySelector(".totalGst")?.value) || 0,
      parseFloat(row.querySelector(".gst1_5")?.value) || 0,
      parseFloat(row.querySelector(".remGst")?.value) || 0,
      vendorPercentVal,   // From header input
      cashierPercentVal,  // From header input
      agPercentVal,       // From header input
      officePercentVal,   // From header input
      parseFloat(row.querySelector(".balance")?.value) || 0,
      row.querySelector(".firm")?.value?.trim() || ""
    ];

    // Push only if bill or cheque has value
    if (rowData[1] > 0 || rowData[2] > 0) {
      data.push(rowData);
    }
  });

  if (data.length === 0) {
    alert("No valid rows to save.");
    return;
  }

  const selectedTableEl = document.querySelector('select[name="selected_table"], input[name="selected_table"]');
  const selectedTable = selectedTableEl ? selectedTableEl.value.trim() : '';

  if (!selectedTable) {
    alert("Please select a table/company.");
    return;
  }

const fileInput = document.getElementById('invoiceFile');
  const file = fileInput?.files?.[0] || null;

  const formData = new FormData();
  formData.append('data', JSON.stringify({
    rows: data,
    selected_table: selectedTable
  }));

  if (file) {
    formData.append('invoiceFile', file);
  }

  fetch('save_invoice.php', {
    method: 'POST',
    body: formData
})
.then(res => res.json())
.then(response => {
    if (response.success) {
        alert("Saved successfully!");
        setTimeout(() => {
            window.location.href = "/sales"; // redirect after short delay
        }, 200); // 0.2 sec delay to ensure save completes
    } else {
        alert("Save failed: " + (response.message || "Unknown error"));
    }
})
.catch(err => {
    console.error(err);
    alert("An error occurred while saving.");
});
}
</script>



<?php
// Connect to your database
require 'db_connection.php';

// Function to generate unique invoice number
function generateUniqueInvoiceNumber($conn) {
    for ($i = 0; $i < 1000; $i++) {
        $number = rand(1000, 9999);
        $invoice = 'INV/' . $number;

        // Check if the number already exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM invoices WHERE invoice_number = ?");
        $stmt->bind_param("s", $invoice);
        $stmt->execute();
        $stmt->bind_result($exists);
        $stmt->fetch();
        $stmt->close();

        if ($exists == 0) {
            // Save new invoice number to the database
            $stmt = $conn->prepare("INSERT INTO invoices (invoice_number) VALUES (?)");
            $stmt->bind_param("s", $invoice);
            $stmt->execute();
            $stmt->close();

            return $invoice;
        }
    }

    return "INV/XXXX"; // fallback if no unique number found
}

// Call the function
$invoiceNumber = generateUniqueInvoiceNumber($conn);
?>
<?php
session_start();
$userName = isset($_SESSION['username']) ? $_SESSION['username'] : 'Unknown User';
?>
<script>
    const loggedInUser = "<?php echo htmlspecialchars($userName); ?>";
</script>

</body>
</html>