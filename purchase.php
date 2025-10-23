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
  <title>Purchase</title>
  <link rel="icon" type="image/png" sizes="32x32" href="/images/weblogo.png?v=2">
<link rel="icon" type="image/png" sizes="16x16" href="/images/weblogo.png?v=2">
<link rel="shortcut icon" href="/images/weblogo.png?v=2">

        <script src="https://cdn.tailwindcss.com"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<head>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
 

  body {
    margin: 0;
    padding-bottom: 60px; /* space for fixed menu bar */
  }
</style>
<!-- <style>

  /* Form groups for inline inputs */
  .form-group {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
  }

  .form-group > div {
    flex: 1;
    display: flex;
    flex-direction: column;
  }

  label {
    margin-bottom: 6px;
    font-weight: bold;
  }

  input[type="text"], input[type="date"], input[type="number"], select {
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
  }

  /* Table styling */
  table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 10px;
  }

  th, td {
    border: 1px solid #ddd;
    text-align: center;
    padding: 10px;
  }

  th {
    background-color: #6b3de6;
    color: white;
  }

  tfoot tr {
    font-weight: bold;
    background-color: #f0f0f0;
  }

  .add-line-btn {
    background-color: #6b3de6;
    color: white;
    border: none;
    padding: 10px 15px;
    cursor: pointer;
    border-radius: 4px;
  }

  .add-line-btn:hover {
    background-color: #5a2bc3;
  }
   .form-container {
      max-width: 1200px;
      background: white;
      margin: auto;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
</style> -->
</head>
<body>
<div class="menu-bar fixed top-0 left-0 w-full bg-[#6b3de6] shadow z-50">
  <div class="flex justify-between items-center px-6 py-3 text-white font-medium" style="
    padding-bottom: unset;
    padding-top: unset;
">
    
    <!-- Centered Menu Links -->
    <div class="flex-1 flex justify-center space-x-6">
      <a href="/home" class="hover:bg-purple-800 px-4 py-2 rounded" >Dashboard</a>
      <a href="/sales" class="hover:bg-purple-800 px-4 py-2 rounded">Sales</a>
      <a href="/purchase" class="hover:bg-purple-800 px-4 py-2 rounded"style="background-color: #5830be;">Purchases</a>
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
<style>
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f4f7fa;
    margin: 0;
  }

  .form-wrapper {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    align-items: flex-start;
  }

  .left-section {
    flex: 0 0 28%;
    background: #ffffff;
    padding: 20px 15px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    border: 1px solid #e0e0e0;
  }

  .left-section h2 {
    font-size: 20px;
    margin-bottom: 20px;
    text-align: center;
    color: #333;
  }

  .form-group-row {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
  }

  .form-group {
    flex: 1;
  }

  .form-group label {
    font-size: 14px;
    font-weight: 600;
    color: #555;
    margin-bottom: 5px;
    display: block;
  }

  .form-group input {
    width: 90%;
    padding: 8px 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
  }

  input[readonly] {
    background: #f1f1f1;
  }

  .right-section {
    flex: 1;
    background: #ffffff;
    padding: 20px;
    border-radius: 12px;
    border: 1px solid #e0e0e0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  }

  .right-section h2 {
    font-size: 20px;
    margin-bottom: 15px;
    color: #333;
  }

  #purchaseTable {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
  }

  #purchaseTable th,
#purchaseTable td {
  border: 1px solid #ddd;
  padding: 10px;
  text-align: center; /* Ensures text and inputs inside are centered */
}
#purchaseTable td input {
  width: 100%;
  padding: 6px 8px;
  border: none;           /* Removes border */
  outline: none;          /* Removes outline when focused */
  text-align: center;     /* Center text inside input */
  background: transparent;
  font-size: 14px;
}


  #purchaseTable th {
    background: #f9fafc;
    color: #333;
  }

  .add-line-btn {
    background-color: #007bff;
    color: white;
    padding: 10px 16px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    cursor: pointer;
    margin-right: 10px;
  }

  .add-line-btn:hover {
    background-color: #0056b3;
  }

  #newInvoiceBtn {
    margin: 30px auto;
    display: block;
    font-size: 18px;
  }

  @media (max-width: 768px) {
    .form-wrapper {
      flex-direction: column;
    }

    .left-section,
    .right-section {
      flex: 1 0 100%;
    }

    .form-group-row {
      flex-direction: column;
    }
  }
</style>

<!-- Invoice Button -->
<div style="text-align: center;">
  
</div>

<!-- Form Start -->
<form id="vendorBillForm" method="POST" action="submit_vendor_bill.php">
  <div class="form-wrapper" style="
    margin-top: 4%;
">

    <!-- LEFT SIDE -->
    <div class="left-section">
      <h2>Vendor Bill</h2>

      <div class="form-group-row">
        <div class="form-group">
          <label>Invoice No</label>
          <input type="text" name="invoice_number" value="INV<?php echo rand(10000,99999); ?>" readonly>
        </div>
        <div class="form-group">
          <label>Vendor</label>
          <input type="text" name="vendor" required>
        </div>
      </div>

      <div class="form-group-row">
        <div class="form-group">
          <label>Bill Ref</label>
          <input type="text" name="bill_reference">
        </div>
        <div class="form-group">
          <label>Tax ID</label>
          <input type="text" name="tax_id" required>
        </div>
      </div>

      <div class="form-group-row">
        <div class="form-group">
          <label>Bill Date</label>
          <input type="date" name="bill_date" value="<?php echo date('Y-m-d'); ?>" required>
        </div>
        <div class="form-group">
          <label>Accounting Date</label>
          <input type="date" name="accounting_date">
        </div>
      </div>

      <div class="form-group">
        <label>Due Date</label>
        <input type="date" name="due_date">
      </div>
      <button id="newInvoiceBtn" class="add-line-btn" style="background-color: #6b3de6;">
    ➕ New Invoice
  </button>
    </div>

    <!-- RIGHT SIDE -->
    <div class="right-section">
      <h2>Items Table</h2>

      <table id="purchaseTable">
        <thead>
          <tr>
            <th>Item Name</th>
            <th>Quantity</th>
            <th>Rate</th>
            <th>Amount</th>
          </tr>
        </thead>
        <tbody id="purchaseBody">
          <tr>
            <td><input type="text" name="item_name[]" required></td>
            <td><input type="number" name="quantity[]" min="0" step="1" onchange="updateAmount(this)" required></td>
            <td><input type="number" name="rate[]" min="0" step="0.01" onchange="updateAmount(this)" required></td>
            <td><input type="text" name="amount[]" readonly></td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <th colspan="3" style="text-align:right">Grand Total:</th>
            <th id="grandTotal">0.00</th>
          </tr>
        </tfoot>
      </table>

      <button type="button" class="add-line-btn" onclick="addRow()"style="background-color: #6b3de6;">➕ Add Line</button>
      <button type="submit" class="add-line-btn" style="background-color: #6b3de6;">✔️ Submit Bill</button>
    </div>

  </div>
</form>
<script>
  function updateAmount(input) {
    const row = input.closest('tr');
    const qty = parseFloat(row.querySelector('[name="quantity[]"]').value) || 0;
    const rate = parseFloat(row.querySelector('[name="rate[]"]').value) || 0;
    const amountField = row.querySelector('[name="amount[]"]');
    const amount = qty * rate;
    amountField.value = amount.toFixed(2);
    updateGrandTotal();
  }

  function updateGrandTotal() {
    let total = 0;
    document.querySelectorAll('[name="amount[]"]').forEach(input => {
      total += parseFloat(input.value) || 0;
    });
    document.getElementById('grandTotal').textContent = total.toFixed(2);
  }

  function addRow() {
    const row = document.createElement('tr');
    row.innerHTML = `
      <td><input type="text" name="item_name[]" required></td>
      <td><input type="number" name="quantity[]" min="0" step="1" onchange="updateAmount(this)" required></td>
      <td><input type="number" name="rate[]" min="0" step="0.01" onchange="updateAmount(this)" required></td>
      <td><input type="text" name="amount[]" readonly></td>
    `;
    document.getElementById('purchaseBody').appendChild(row);
  }
</script>

</body>
</html>
