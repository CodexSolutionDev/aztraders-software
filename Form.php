<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: /");
    exit;
}
if (basename($_SERVER['PHP_SELF']) !== 'view.php') {
    unset($_SESSION['access_granted_view']);
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Form</title>
  <link rel="icon" type="image/png" sizes="32x32" href="/images/weblogo.png?v=2">
<link rel="icon" type="image/png" sizes="16x16" href="/images/weblogo.png?v=2">
<link rel="shortcut icon" href="/images/weblogo.png?v=2">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<head>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <script src="https://cdn.tailwindcss.com"></script>
<style>
  .watermark {
    position: absolute;
    top: 40%;
    left: 50%;
    transform: translate(-50%, -50%);
    opacity: 0.10;
    z-index: 0;
    pointer-events: none;
  }
  .watermark img {
    max-width: 400px;
  }
</style>

    <style>
  

  body {
    margin: 0;
    padding-bottom: 70px; /* enough space for bottom navbar */
    font-family: sans-serif;
  }
</style>

  
</head>
<body class="bg-gray-100 text-gray-800">
<div class="menu-bar fixed top-0 left-0 w-full bg-[#6b3de6] shadow z-50">
  <div class="flex justify-between items-center px-6 py-3 text-white font-medium" style="
    padding-bottom: unset;
    padding-top: unset;
">
    
    <!-- Centered Menu Links -->
    <div class="flex-1 flex justify-center space-x-6">
      <a href="/home" class="hover:bg-purple-800 px-4 py-2 rounded" >Dashboard</a>
      <a href="/sales" class="hover:bg-purple-800 px-4 py-2 rounded">Sales</a>
      <a href="/purchase" class="hover:bg-purple-800 px-4 py-2 rounded">Purchases</a>
      <a href="/department" class="hover:bg-purple-800 px-4 py-2 rounded">Department</a>
      <a href="/staff" class="hover:bg-purple-800 px-4 py-2 rounded">Staff</a>
      <a href="/Form" class="hover:bg-purple-800 px-4 py-2 rounded"style="background-color: #5830be;">Form</a>
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

<style>
  input.amount {
    width: 100px;
    display: inline-block;
  }
</style>

<style>
  .container {
    max-width: 900px;
    margin: 40px auto;
    background: #ffffff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    font-family: Arial, sans-serif;
  }

  .container h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #333;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
  }

  table th, table td {
    border: 1px solid #ccc;
    padding: 10px;
    text-align: center;
  }

  table th {
    background-color: #f3f3f3;
  }

  input[type="text"],
  input[type="number"] {
    width: 100%;
    padding: 6px;
    box-sizing: border-box;
    border-radius: 4px;
    border: 1px solid #aaa;
  }

  button {
    padding: 10px 20px;
    background-color: #6b3de6;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
    transition: background-color 0.3s;
  }

  button:hover {
    background-color: #5830be;
  }

  h3 {
    text-align: right;
    color: #222;
  }
</style>
<form id="salesForm" style="margin-top: 70px;">
  <div id="pdfContent">
    <!-- B&M Enterprises Section -->
   <div id="bmHeader" style="display: none; width: 100%; padding: 20px 30px; box-sizing: border-box; font-family: sans-serif;">
  <div style="display: flex; justify-content: space-between; align-items: flex-start;">
    <!-- Left: Company Info -->
    <div>
      <h1 style="text-align: center; font-size: 2.25rem; font-weight: 600; line-height: 1.25; margin: 0;">
        B&M <span style="color: #a64d79;">Enterprises</span>
      </h1>
      <p style="color: #4a4a4a; font-size: 20px; line-height: 1.4; margin-top: 5px;">
         General Order Supplier
      </p>
    </div>

    <!-- Right: Invoice Number -->
    <div id="bmInvoiceNumber" style="font-weight: bold; color: black; font-size: 14px; text-align: right; margin-top: 10px;">
      <!-- filled dynamically -->
    </div>
  </div>
  <!-- Ref and Date row -->
  <div style="display: flex; justify-content: space-between; margin-top: 30px; font-size: 14px; color: #4a4a4a; font-weight: 500;">
    <div>
      Ref #: <span style="display: inline-block; width: 200px; border-bottom: 1px solid #aaa;"></span>
    </div>
    <div>
      Date: <span style="display: inline-block; width: 200px; border-bottom: 1px solid #aaa;"></span>
    </div>
  </div>
  <!-- Selected Table Display -->
  <div id="bmselectedTable" class="mt-4 text-black font-semibold">
   <span class="text-purple-700">None</span>
  </div>
</div>
    <div id="bmContainer" style="display:none;">
  <h2>Sales Table</h2>
  <table id="salesTable" class="table-auto border-collapse border border-gray-300 w-full">
  <thead>
      <tr>
        <th class="border border-gray-300 px-2 py-1">SR No</th>
        <th class="border border-gray-300 px-2 py-1">Description</th>
        <th class="border border-gray-300 px-2 py-1">QTY</th>
        <th class="border border-gray-300 px-2 py-1">Unit Rate</th>
        <th class="border border-gray-300 px-2 py-1">GST (18%)</th>
        <th class="border border-gray-300 px-2 py-1">Amount</th>
      </tr>
    </thead>
    <tbody id="salesTableBody">
      <tr>
<td class="border border-gray-300 px-2 py-1 deleteRow" style="cursor: pointer; color: red;">1*</td>
        <td class="border border-gray-300 px-2 py-1"><input type="text" name="sales_description[]" /></td>
        <td class="border border-gray-300 px-2 py-1"><input type="number" name="sales_qty[]" class="qty" oninput="calculateRow(this)" /></td>
        <td class="border border-gray-300 px-2 py-1"><input type="number" name="sales_rate[]" class="rate" oninput="calculateRow(this)" /></td>
        <td class="border border-gray-300 px-2 py-1"><input type="text" name="sales_gst[]" class="gst" readonly /></td>
        <td class="border border-gray-300 px-2 py-1"><input type="text" name="sales_amount[]" class="amount" readonly /></td>
      </tr>
    </tbody>
  </table>
  <h3 style="margin-right: 12px;">Total: <span id="salesTotal">0</span></h3>
  <div style="height: 30px;"></div>
  <table id="labourTable" class="table-auto border-collapse border border-gray-300 w-full">
  <thead>
      <tr>
        <th class="border border-gray-300 px-2 py-1">SR No</th>
        <th class="border border-gray-300 px-2 py-1">Services/Labour</th>
        <th class="border border-gray-300 px-2 py-1">QTY</th>
        <th class="border border-gray-300 px-2 py-1">Unit Rate</th>
        <th class="border border-gray-300 px-2 py-1">PST (16%)</th>
        <th class="border border-gray-300 px-2 py-1">Amount</th>
      </tr>
    </thead>
    <tbody id="labourTableBody">
      <tr>
<td class="border border-gray-300 px-2 py-1 deleteRow" style="cursor: pointer; color: red;">1*</td>
        <td class="border border-gray-300 px-2 py-1"><input type="text" name="labour_description[]" /></td>
        <td class="border border-gray-300 px-2 py-1"><input type="number" name="labour_qty[]" class="qty" oninput="calculateRow(this)" /></td>
        <td class="border border-gray-300 px-2 py-1"><input type="number" name="labour_rate[]" class="rate" oninput="calculateRow(this)" /></td>
        <td class="border border-gray-300 px-2 py-1"><input type="text" name="labour_pst[]" class="pst" readonly /></td>
        <td class="border border-gray-300 px-2 py-1"><input type="text" name="labour_amount[]" class="amount" readonly /></td>
      </tr>
    </tbody>
  </table>
<h3 style="margin-right: 12px;">Total: <span id="labourTotal">0</span></h3>  
  
  <h1 style="font-size: 2rem; color: green; font-weight: bold; margin-top: 20px;">
  Grand Total: <span id="grandTotal">0</span>
</h1>

</div>


    <div id="bmFooter" style="display: none; width: 100%; margin: 0 auto;">
  <div style="text-align: center; font-weight: bold; font-size: 0.875rem; color: black; margin-bottom: 8px;">
    <span style="display: inline-block; margin-right: 0px;">Email: bilalali109@gmail.com</span>
    <span style="display: inline-block;">Cell: +92 331 418 5033</span>
  </div>

  <div style="background-color: #a64d79; color: white; font-weight: bold; font-family: serif; font-size: 1rem; padding: 8px 16px; text-align: center;">
        Shop No. 18, Street No. 64, Makhan Pura, Chah Miran, Lahore.
  </div>
</div>

    <!-- Faizan Traders Section -->
    <div id="faizanHeader" style="display: none; width: 100%; padding: 20px 30px; box-sizing: border-box; font-family: sans-serif;">
  <div style="display: flex; justify-content: space-between; align-items: flex-start;">
    <!-- Left: Company Info -->
    <div>
      <h1 style="text-align: center; font-size: 2.25rem; font-weight: 600; line-height: 1.25; margin: 0;">
        Faizan <span style="color: Blue;">Traders</span>
      </h1>
      <p style="color: #4a4a4a; font-size: 14px; line-height: 1.4; margin-top: 5px;">
        All Kind of Paper Products, Stationery, Printing<br />
        Furniture, Auto Workshop Works, Computer Accessories
      </p>
    </div>

    <!-- Right: Invoice Number -->
    <div id="faizanInvoiceNumber" style="font-weight: bold; color: black; font-size: 14px; text-align: right; margin-top: 10px;">
      <!-- filled dynamically -->
    </div>
  </div>

  <!-- Ref and Date row -->
  <div style="display: flex; justify-content: space-between; margin-top: 30px; font-size: 14px; color: #4a4a4a; font-weight: 500;">
    <div>
      Ref #: <span style="display: inline-block; width: 200px; border-bottom: 1px solid #aaa;"></span>
    </div>
    <div>
      Date: <span style="display: inline-block; width: 200px; border-bottom: 1px solid #aaa;"></span>
    </div>
  </div>
  <!-- Selected Table Display -->
  <div id="faizanselectedTable" class="mt-4 text-black font-semibold">
   <span class="text-purple-700">None</span>
  </div>
</div>

    

    <div id="faizanContainer" style="display:none;">
  <h2>Sales Table</h2>
  <table id="salesTable" class="table-auto border-collapse border border-gray-300 w-full">
  <thead>
      <tr>
        <th class="border border-gray-300 px-2 py-1">SR No</th>
        <th class="border border-gray-300 px-2 py-1">Description</th>
        <th class="border border-gray-300 px-2 py-1">QTY</th>
        <th class="border border-gray-300 px-2 py-1">Unit Rate</th>
        <th class="border border-gray-300 px-2 py-1">GST (18%)</th>
        <th class="border border-gray-300 px-2 py-1">Amount</th>
      </tr>
    </thead>
    <tbody id="salesTableBody">
      <tr>
<td class="border border-gray-300 px-2 py-1 deleteRow" style="cursor: pointer; color: red;">1*</td>
        <td class="border border-gray-300 px-2 py-1"><input type="text" name="sales_description[]" /></td>
        <td class="border border-gray-300 px-2 py-1"><input type="number" name="sales_qty[]" class="qty" oninput="calculateRow(this)" /></td>
        <td class="border border-gray-300 px-2 py-1"><input type="number" name="sales_rate[]" class="rate" oninput="calculateRow(this)" /></td>
        <td class="border border-gray-300 px-2 py-1"><input type="text" name="sales_gst[]" class="gst" readonly /></td>
        <td class="border border-gray-300 px-2 py-1"><input type="text" name="sales_amount[]" class="amount" readonly /></td>
      </tr>
    </tbody>
  </table>
  <h3 style="margin-right: 12px;">Total: <span id="salesTotal">0</span></h3>
  <div style="height: 30px;"></div>
  <table id="labourTable" class="table-auto border-collapse border border-gray-300 w-full">
  <thead>
      <tr>
        <th class="border border-gray-300 px-2 py-1">SR No</th>
        <th class="border border-gray-300 px-2 py-1">Services/Labour</th>
        <th class="border border-gray-300 px-2 py-1">QTY</th>
        <th class="border border-gray-300 px-2 py-1">Unit Rate</th>
        <th class="border border-gray-300 px-2 py-1">PST (16%)</th>
        <th class="border border-gray-300 px-2 py-1">Amount</th>
      </tr>
    </thead>
    <tbody id="labourTableBody">
      <tr>
<td class="border border-gray-300 px-2 py-1 deleteRow" style="cursor: pointer; color: red;">1*</td>
        <td class="border border-gray-300 px-2 py-1"><input type="text" name="labour_descripton[]" /></td>
        <td class="border border-gray-300 px-2 py-1"><input type="number" name="labour_qty[]" class="qty" oninput="calculateRow(this)" /></td>
        <td class="border border-gray-300 px-2 py-1"><input type="number" name="labour_rate[]" class="rate" oninput="calculateRow(this)" /></td>
        <td class="border border-gray-300 px-2 py-1"><input type="text" name="labour_pst[]" class="pst" readonly /></td>
        <td class="border border-gray-300 px-2 py-1"><input type="text" name="labour_amount[]" class="amount" readonly /></td>
      </tr>
    </tbody>
  </table>
<h3 style="margin-right: 12px;">Total: <span id="labourTotal">0</span></h3> 

  <h1 style="font-size: 2rem; color: green; font-weight: bold; margin-top: 20px;">
  Grand Total: <span id="grandTotal">0</span>
</h1>

</div>

   <div id="faizanFooter" style="display: none; width: 100%; margin: 0 auto;">
  <div style="text-align: center; font-weight: bold; font-size: 0.875rem; color: black; margin-bottom: 8px;">
    <span style="display: inline-block; margin-right: 0px;">tradersa895@gmail.com</span>
    <span style="display: inline-block;">Cell: +92 323 1464657</span>
  </div>

  <div style="background-color: Blue; color: white; font-weight: bold; font-family: serif; font-size: 1rem; padding: 8px 16px; text-align: center;">
    Office # 7, Al Qamar Centre, 26 Kabeer Street, Urdu Bazar Lahore
  </div>
</div>

    
    <div id="azHeader" style="display: none; width: 100%; padding: 20px 30px; box-sizing: border-box; font-family: sans-serif;">
  <div style="display: flex; justify-content: space-between; align-items: flex-start;">
    <!-- Left: Company Info -->
    <div>
      <h1 style="text-align: center; font-size: 2.25rem; font-weight: 600; line-height: 1.25; margin: 0;">
        AZ <span style="color: red;">Traders</span>
      </h1>
      <p style="color: #4a4a4a; font-size: 14px; line-height: 1.4; margin-top: 5px;">
        All Kind of Paper Products, Stationery, Printing<br />
        Furniture, Auto Workshop Works, Computer Accessories
      </p>
    </div>
    <div id="selectedTable" style="display:none;">Selected Table: None</div>

<!-- Selected Table Display -->
  <div id="azselectedTable" class="mt-4 text-black font-semibold">
   <span class="text-purple-700">None</span>
  </div>
    <!-- Right: Invoice Number -->
    <div id="azInvoiceNumber" style="font-weight: bold; color: black; font-size: 14px; text-align: right; margin-top: 10px;">
      <!-- filled dynamically -->
    </div>
  </div>

  <!-- Ref and Date row -->
  <div style="display: flex; justify-content: space-between; margin-top: 30px; font-size: 14px; color: #4a4a4a; font-weight: 500;">
    <div>
      Ref #: <span style="display: inline-block; width: 200px; border-bottom: 1px solid #aaa;"></span>
    </div>
    <div>
      Date: <span style="display: inline-block; width: 200px; border-bottom: 1px solid #aaa;"></span>
    </div>
  </div>
</div>

    <div id="azContainer" style="display:none;">
  <h2>Sales Table</h2>
  <table id="salesTable" class="table-auto border-collapse border border-gray-300 w-full">
  <thead>
      <tr>
        <th class="border border-gray-300 px-2 py-1">SR No</th>
        <th class="border border-gray-300 px-2 py-1">Description</th>
        <th class="border border-gray-300 px-2 py-1">QTY</th>
        <th class="border border-gray-300 px-2 py-1">Unit Rate</th>
        <th class="border border-gray-300 px-2 py-1">GST (18%)</th>
        <th class="border border-gray-300 px-2 py-1">Amount</th>
      </tr>
    </thead>
    <tbody id="salesTableBody">
      <tr>
<td class="border border-gray-300 px-2 py-1 deleteRow" style="cursor: pointer; color: red;">1*</td>
        <td class="border border-gray-300 px-2 py-1"><input type="text" name="sales_description[]" /></td>
        <td class="border border-gray-300 px-2 py-1"><input type="number" name="sales_qty[]" class="qty" oninput="calculateRow(this)" /></td>
        <td class="border border-gray-300 px-2 py-1"><input type="number" name="sales_rate[]" class="rate" oninput="calculateRow(this)" /></td>
        <td class="border border-gray-300 px-2 py-1"><input type="text" name="sales_gst[]" class="gst" readonly /></td>
        <td class="border border-gray-300 px-2 py-1"><input type="text" name="sales_amount[]" class="amount" readonly /></td>
      </tr>
    </tbody>
  </table>
  <h3 style="margin-right: 12px;">Total: <span id="salesTotal">0</span></h3>
  <div style="height: 30px;"></div>
  <table id="labourTable" class="table-auto border-collapse border border-gray-300 w-full">
  <thead>
      <tr>
        <th class="border border-gray-300 px-2 py-1">SR No</th>
        <th class="border border-gray-300 px-2 py-1">Servics/Labour</th>
        <th class="border border-gray-300 px-2 py-1">QTY</th>
        <th class="border border-gray-300 px-2 py-1">Unit Rate</th>
        <th class="border border-gray-300 px-2 py-1">PST (16%)</th>
        <th class="border border-gray-300 px-2 py-1">Amount</th>
      </tr>
    </thead>
    <tbody id="labourTableBody">
      <tr>
<td class="border border-gray-300 px-2 py-1 deleteRow" style="cursor: pointer; color: red;">1*</td>
        <td class="border border-gray-300 px-2 py-1"><input type="text" name="labour_description[]" /></td>
        <td class="border border-gray-300 px-2 py-1"><input type="number" name="labour_qty[]" class="qty" oninput="calculateRow(this)" /></td>
        <td class="border border-gray-300 px-2 py-1"><input type="number" name="labour_rate[]" class="rate" oninput="calculateRow(this)" /></td>
        <td class="border border-gray-300 px-2 py-1"><input type="text" name="labour_pst[]" class="pst" readonly /></td>
        <td class="border border-gray-300 px-2 py-1"><input type="text" name="labour_amount[]" class="amount" readonly /></td>
      </tr>
    </tbody>
  </table>
<h3 style="margin-right: 12px;">Total: <span id="labourTotal">0</span></h3>  
  
  <h1 style="font-size: 2rem; color: green; font-weight: bold; margin-top: 20px;">
  Grand Total: <span id="grandTotal">0</span>
</h1>

</div>

   <div id="azFooter" style="display: none; width: 100%; margin: 0 auto;">
  <div style="text-align: center; font-weight: bold; font-size: 0.875rem; color: black; margin-bottom: 8px;">
    <span style="display: inline-block; margin-right: 0px;">tradersa895@gmail.com</span>
    <span style="display: inline-block;">Cell: +92 310 1427935</span>
  </div>

  <div style="background-color: red; color: white; font-style: bold; font-family: serif; font-size: 1rem; padding: 8px 16px; text-align: center;">
    Office # 23, 1st Floor Khawaja Arcade Wahdat Road Lahore
  </div>
</div>


  </div>
<?php
include 'connect.php'; // Database connection

// Fetch tables except 'customers' and 'sales'
$exclude = ['customers', 'sales'];
$tables = [];
$result = $conn->query("SHOW TABLES");
if ($result) {
    while ($row = $result->fetch_array()) {
        $tableName = $row[0];
        if (!in_array($tableName, $exclude)) {
            $tables[] = $tableName;
        }
    }
}
?>
<!-- Glass Container -->
<div class="glass-container mx-auto mt-10 max-w-3xl p-6 rounded-xl shadow-lg backdrop-blur-md bg-white/10 border border-white/30">

  <!-- Trader Dropdown -->
  <div class="mb-6">
    <label for="traderDropdown" class="font-semibold block mb-2 text-black">Select Trader:</label>
    <select id="traderDropdown" name="trader"
      class="w-full border border-gray-300 p-2 rounded bg-white/80 text-black backdrop-blur-md">
      <option value="">-- Select Trader --</option>
      <option value="faizan">Faizan Traders</option>
      <option value="az">Az Traders</option>
      <option value="bm">B&amp;M Enterprises</option>
    </select>
  </div>

  <!-- Table Search Bar -->
  <div class="mb-6 relative">
    <label for="tableSearchInput" class="font-semibold block mb-2 text-black">Search & Select Table:</label>
    <input type="text" id="tableSearchInput" placeholder="Type table name..."
      class="w-full border border-gray-300 p-2 rounded bg-white/80 text-black backdrop-blur-md"
      onkeyup="filterTableList()" onfocus="showTableList()" onclick="showTableList()">

    <div id="tableListDropdown" class="absolute w-full bg-white/90 border border-gray-300 rounded mt-1 max-h-60 overflow-y-auto hidden z-50">
     <?php foreach ($tables as $table): ?>
  <div class="table-option px-3 py-2 cursor-pointer hover:bg-gray-200"
       onclick="selectTable('<?= htmlspecialchars($table) ?>')">
    <?= ucwords(str_replace('_', ' ', $table)) ?>
  </div>
<?php endforeach; ?>
    </div>

  


<script>
const tableInput = document.getElementById('tableSearchInput');
const tableDropdown = document.getElementById('tableListDropdown');
const selectedDisplay = document.getElementById('selectedTable');

function filterTableList() {
  const filter = tableInput.value.toLowerCase();
  const options = document.querySelectorAll('.table-option');
  let anyVisible = false;
  options.forEach(opt => {
    const text = opt.textContent.toLowerCase();
    if (text.includes(filter)) {
      opt.style.display = 'block';
      anyVisible = true;
    } else {
      opt.style.display = 'none';
    }
  });
  tableDropdown.style.display = anyVisible ? 'block' : 'none';
}

function showTableList() {
  tableDropdown.style.display = 'block';
}

function selectTable(name) {
  // Format name for display
  const formatted = name.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
  
  tableInput.value = formatted; // show nicely in input
  selectedDisplay.innerHTML = `Selected Table: <span class="text-purple-700">${formatted}</span>`;
  tableDropdown.style.display = 'none';
}


// Click outside to close dropdown
document.addEventListener('click', function(e) {
  if (!tableDropdown.contains(e.target) && e.target !== tableInput) {
    tableDropdown.style.display = 'none';
  }
});
</script>



  <!-- Buttons -->
  <div class="flex flex-wrap justify-center gap-4">
    <button type="button" onclick="clearTables()" class="btn-glass">Clear Table</button>
    <button type="button" onclick="addSalesRow()" class="btn-glass">Add Sales</button>
    <button type="button" onclick="addLabourRow()" class="btn-glass">Add Labour</button>
    <button type="button" onclick="saveAndDownloadPDF()" class="btn-glass">
      <i class="fas fa-download mr-1"></i> Download PDF
    </button>
  </div>
</div>

<!-- Styling -->
<style>
  .glass-container {
    background: rgba(255, 255, 255, 0.05);
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.2);
  }

  .btn-glass {
    background: rgba(107, 61, 230, 0.8); /* purple tone */
    color: white;
    padding: 8px 16px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    backdrop-filter: blur(6px);
    transition: background 0.3s ease;
  }

  .btn-glass:hover {
    background: rgba(107, 61, 230, 1);
  }
</style>


</form>
<script>
function saveAndDownloadPDF() {
  const form = document.getElementById("salesForm");
  const formData = new FormData(form);

  fetch('save_table.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.text())
  .then(data => {
    const match = data.trim().match(/Invoice saved in table:\s*(\S+)/i);
    const invoiceNumber = match ? match[1] : "N/A";

    // Update invoice numbers
    ["bmInvoiceNumber", "faizanInvoiceNumber", "azInvoiceNumber"].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.innerText = "Invoice #: " + invoiceNumber;
    });

    // Show confirmation message
    let resultDiv = document.getElementById("invoiceResult");
    if (!resultDiv) {
      resultDiv = document.createElement("div");
      resultDiv.id = "invoiceResult";
      document.body.appendChild(resultDiv);
    }
    resultDiv.innerHTML = `
      <div style="margin-top: 20px; background: #dff0d8; color: #3c763d; padding: 15px; border-radius: 5px;">
        ${data}<br>Page will refresh in seconds...
      </div>
    `;

    setTimeout(() => {
      window.location.reload();
    }, 10000);

    const selectedTrader = document.getElementById("traderDropdown").value;
    const traders = ["bm", "faizan", "az"];

    // Update Selected Table text for PDF
    const tableInput = document.getElementById("tableSearchInput");
    let selectedTableEl;
    if (selectedTrader === "bm") selectedTableEl = document.getElementById("bmSelectedTable");
    else if (selectedTrader === "faizan") selectedTableEl = document.getElementById("faizanSelectedTable");
    else if (selectedTrader === "az") selectedTableEl = document.getElementById("azSelectedTable");

    if (selectedTableEl) {
      selectedTableEl.innerText = `Selected Table: ${tableInput.value || "None"}`;
    }

    // Show only selected trader sections
    traders.forEach(trader => {
      ["Header", "Footer", "Container"].forEach(part => {
        const el = document.getElementById(`${trader}${part}`);
        if (el) el.style.display = trader === selectedTrader ? "block" : "none";
      });
    });

    // Hide UI controls
    const uiControls = document.querySelector(".ui-controls");
    if (uiControls) uiControls.style.display = "none";

    // Spacer before footer
    const footerEl = document.getElementById(`${selectedTrader}Footer`);
    let spacer;
    if (footerEl) {
      spacer = document.createElement("div");
      spacer.className = "pdf-footer-spacer";
      spacer.style.height = "100px";
      footerEl.parentNode.insertBefore(spacer, footerEl);
    }

    const element = document.getElementById("pdfContent");
    if (!element) {
      alert("PDF content element not found.");
      return;
    }

    const options = {
      margin: 0.2,
      filename: `invoice-${invoiceNumber}.pdf`,
      image: { type: 'jpeg', quality: 0.98 },
      html2canvas: { scale: 2 },
      jsPDF: { unit: 'in', format: 'A4', orientation: 'portrait' }
    };

    // Add watermark
    const watermarkDiv = document.createElement("div");
    watermarkDiv.className = "watermark";
    const img = document.createElement("img");
    if (selectedTrader === "bm") img.src = "bmlogo.png";
    else if (selectedTrader === "faizan") img.src = "ftlogo.png";
    else if (selectedTrader === "az") img.src = "azlogo.png";
    watermarkDiv.appendChild(img);
    element.appendChild(watermarkDiv);

    // 🧩 Wait for DOM to render properly before PDF
    setTimeout(() => {
      html2pdf().from(element).set(options).save().then(() => {
        // Restore UI
        if (uiControls) uiControls.style.display = "block";
        if (spacer && spacer.parentNode) spacer.parentNode.removeChild(spacer);
        if (watermarkDiv && watermarkDiv.parentNode) watermarkDiv.parentNode.removeChild(watermarkDiv);

        // Hide all trader sections again
        traders.forEach(trader => {
          ["Header", "Footer", "Container"].forEach(part => {
            const el = document.getElementById(`${trader}${part}`);
            if (el) el.style.display = "none";
          });
        });

        // Cleanup
        if (selectedTableEl) selectedTableEl.innerText = "";
      });

      // Send PDF to server
      html2pdf().from(element).set(options).outputPdf("blob").then(function (pdfBlob) {
        const formData = new FormData();
        formData.append("pdf", pdfBlob, `invoice-${invoiceNumber}.pdf`);
        fetch("save_pdf.php", {
          method: "POST",
          body: formData,
        })
        .then(response => response.text())
        .then(result => console.log("Server response:", result))
        .catch(error => console.error("Error saving PDF on server:", error));
      });
    }, 800); // wait for DOM to finish painting

  })
  .catch(err => {
    alert("Error saving or generating PDF: " + err.message);
  });
}
</script>

<script>
  document.addEventListener("click", function (e) {
    if (e.target.classList.contains("deleteRow")) {
      const row = e.target.closest("tr");
      const srNo = parseInt(e.target.textContent);

      if (srNo === 1) {
        alert("SR No 1 wali row delete nahi ki ja sakti.");
        return;
      }

      const tbody = row.closest("tbody"); // Check which table
      row.remove();

      updateSrNumbers(tbody); // Pass only relevant tbody
    }
  });

  function updateSrNumbers(tbody) {
    const rows = tbody.querySelectorAll("tr");
    rows.forEach((row, index) => {
      const srCell = row.querySelector("td");
      if (srCell) {
        srCell.textContent = index + 1;
      }
    });
  }
</script>



<script>
function addSalesRow() {
  const tableBody = document.querySelectorAll('#salesTableBody');
  tableBody.forEach((body) => {
    const rowCount = body.rows.length + 1;
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
<td class="border border-gray-300 px-2 py-1 deleteRow" style="cursor: pointer; color:red;">${rowCount}*</td>
      <td class="border border-gray-300 px-2 py-1"><input type="text" name="sales_description[]" /></td>
      <td class="border border-gray-300 px-2 py-1"><input type="number" name="sales_qty[]" class="qty" oninput="calculateRow(this)" /></td>
      <td class="border border-gray-300 px-2 py-1"><input type="number" name="sales_rate[]" class="rate" oninput="calculateRow(this)" /></td>
      <td class="border border-gray-300 px-2 py-1"><input type="text" name="sales_gst[]" class="gst" readonly /></td>
      <td class="border border-gray-300 px-2 py-1"><input type="text" name="sales_amount[]" class="amount" readonly /></td>
    `;
    body.appendChild(newRow);
  });
}

function addLabourRow() {
  const tableBody = document.querySelectorAll('#labourTableBody');
  tableBody.forEach((body) => {
    const rowCount = body.rows.length + 1;
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
<td class="border border-gray-300 px-2 py-1 deleteRow" style="cursor: pointer; color:red;">${rowCount}*</td>
      <td class="border border-gray-300 px-2 py-1"><input type="text" name="labour_description[]" /></td>
      <td class="border border-gray-300 px-2 py-1"><input type="number" name="labour_qty[]" class="qty" oninput="calculateRow(this)" /></td>
      <td class="border border-gray-300 px-2 py-1"><input type="number" name="labour_rate[]" class="rate" oninput="calculateRow(this)" /></td>
      <td class="border border-gray-300 px-2 py-1"><input type="text" name="labour_pst[]" class="pst" readonly /></td>
      <td class="border border-gray-300 px-2 py-1"><input type="text" name="labour_amount[]" class="amount" readonly /></td>
    `;
    body.appendChild(newRow);
  });
}
</script>

<script>

function clearTables() {
  // Sare containers lo aur check karo kaun sa visible hai
  const containers = document.querySelectorAll("#azContainer, #faizanContainer, #bmContainer");
  const activeContainer = Array.from(containers).find(container => getComputedStyle(container).display !== 'none');

  if (!activeContainer) return;

  const salesBody = activeContainer.querySelector("#salesTableBody");
  const labourBody = activeContainer.querySelector("#labourTableBody");

  // Extra rows delete karo (except first one)
  while (salesBody && salesBody.rows.length > 1) {
    salesBody.deleteRow(1);
  }

  while (labourBody && labourBody.rows.length > 1) {
    labourBody.deleteRow(1);
  }

  // First row ke inputs clear karo
  if (salesBody) {
    salesBody.querySelectorAll("input").forEach(input => input.value = "");
  }

  if (labourBody) {
    labourBody.querySelectorAll("input").forEach(input => input.value = "");
  }

  // Totals reset karo
  const salesTotal = activeContainer.querySelector("#salesTotal");
  const labourTotal = activeContainer.querySelector("#labourTotal");
  const grandTotal = activeContainer.querySelector("#grandTotal");

  if (salesTotal) salesTotal.innerText = "0";
  if (labourTotal) labourTotal.innerText = "0";
  if (grandTotal) grandTotal.innerText = "0";
}


</script>

<script>
function calculateRow(input) {
  const row = input.closest('tr');
  const container =
    input.closest('#azContainer') ||
    input.closest('#bmContainer') ||
    input.closest('#faizanContainer');

  if (!container) return;

  const qty = parseFloat(row.querySelector('.qty')?.value || 0);
  const rate = parseFloat(row.querySelector('.rate')?.value || 0);
  const isSales = row.querySelector('.gst') !== null;
  const taxCell = isSales ? row.querySelector('.gst') : row.querySelector('.pst');
  const amountCell = row.querySelector('.amount');

  const taxRate = isSales ? 0.18 : 0.16;
  const baseAmount = qty * rate;
  const tax = baseAmount * taxRate;
  const total = baseAmount + tax;

  if (taxCell) taxCell.value = tax.toFixed(2);
  if (amountCell) amountCell.value = total.toFixed(2);

  calculateTotals(container);
}
function calculateTotals(container) {
  let salesTotal = 0;
  let labourTotal = 0;

  // Use ID selectors instead of class
  container.querySelectorAll('#salesTableBody input.amount').forEach(input => {
    salesTotal += parseFloat(input.value) || 0;
  });

  container.querySelectorAll('#labourTableBody input.amount').forEach(input => {
    labourTotal += parseFloat(input.value) || 0;
  });

  const grandTotal = salesTotal + labourTotal;

  // Also use ID selectors for total fields
  const salesTotalEl = container.querySelector('#salesTotal');
  const labourTotalEl = container.querySelector('#labourTotal');
  const grandTotalEl = container.querySelector('#grandTotal');

  if (salesTotalEl) salesTotalEl.innerText = salesTotal.toFixed(2);
  if (labourTotalEl) labourTotalEl.innerText = labourTotal.toFixed(2);
  if (grandTotalEl) grandTotalEl.innerText = grandTotal.toFixed(2);
}



</script>

<script>
  document.getElementById('traderDropdown').addEventListener('change', function() {
    const val = this.value;

    // Hide all containers
    document.getElementById('bmContainer').style.display = 'none';
    document.getElementById('faizanContainer').style.display = 'none';
    document.getElementById('azContainer').style.display = 'none';

    // Hide all headers/footers (in case they were visible from PDF render)
    document.getElementById('bmHeader').style.display = 'none';
    document.getElementById('bmFooter').style.display = 'none';
    document.getElementById('faizanHeader').style.display = 'none';
    document.getElementById('faizanFooter').style.display = 'none';
    document.getElementById('azHeader').style.display = 'none';
    document.getElementById('azFooter').style.display = 'none';

    // Show only selected container
    if (val === 'bm') {
      document.getElementById('bmContainer').style.display = 'block';
    } else if (val === 'faizan') {
      document.getElementById('faizanContainer').style.display = 'block';
    } else if (val === 'az') {
      document.getElementById('azContainer').style.display = 'block';
    }
  });
</script>

</body>
