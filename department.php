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
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Department</title>
  <link rel="icon" type="image/png" sizes="32x32" href="/images/weblogo.png?v=2">
<link rel="icon" type="image/png" sizes="16x16" href="/images/weblogo.png?v=2">
<link rel="shortcut icon" href="/images/weblogo.png?v=2">

        <script src="https://cdn.tailwindcss.com"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<head>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>




<style>
.container {
            display: flex;
            align-items: center;
            padding: 16px;
            border: none;
            border-radius: 12px;
            width: 200px;
            background-color: transparent;
            font-family: Arial, sans-serif;
            margin-left: 20px;
            margin-top: 35px;
        }

        .new-button {
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
        }

        .label {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-left: 5px;
        }

        .label-text {
            font-weight: bold;
            font-size: 18px;
        }

        .icon {
            font-size: 16px;
            color: #555;
        }

        .new-button:hover {
            background-color: #0056b3;
        }
    
</style>
<style>
  body {
    margin: 0;
    font-family: Arial, sans-serif;
    background-color: #f5f7fa;
  }

  .table-container {
    margin: 20px auto;
    max-width: 95%;
    background-color: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  }

  .filter-btn {
    padding: 8px 16px;
    font-size: 14px;
    cursor: pointer;
    border: 1px solid #ccc;
    background-color: #007bff;
    color: white;
    border-radius: 6px;
    margin-bottom: 12px;
    transition: background-color 0.3s;
  }

  .filter-btn:hover {
    background-color: #0056b3;
  }

  .column-toggle {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 16px;
  }

  .column-toggle label {
    font-size: 14px;
    background-color: #f0f0f0;
    padding: 6px 10px;
    border-radius: 6px;
    cursor: pointer;
    user-select: none;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
  }

  thead {
    background-color: #f9f9f9;
  }

  th, td {
    padding: 8px 10px;
    border: 1px solid #e0e0e0;
    text-align: left;
    white-space: nowrap;
  }

  tr:nth-child(even) {
    background-color: #fafafa;
  }

  th {
    font-weight: bold;
    background-color: #f1f1f1;
    color: #333;
  }

  td {
    color: #555;
  }

  @media screen and (max-width: 768px) {
    .column-toggle {
      flex-direction: column;
    }

    th, td {
      font-size: 12px;
      padding: 6px 8px;
    }
  }
</style>
<style>
  .action-dropdown-wrapper {
    position: relative;
  }

  .action-dropdown {
    position: absolute;
    top: 24px;
    right: 0;
    background-color: #ffffff;
    border: 1px solid #ddd;
    border-radius: 6px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    display: none;
    flex-direction: column;
    min-width: 120px;
    z-index: 1000;
  }

  .action-dropdown button {
    background: none;
    border: none;
    padding: 10px 12px;
    text-align: left;
    width: 100%;
    font-size: 14px;
    cursor: pointer;
  }

  .action-dropdown button:hover {
    background-color: #f2f2f2;
  }
</style>

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
      <a href="/purchase" class="hover:bg-purple-800 px-4 py-2 rounded">Purchases</a>
      <a href="/department" class="hover:bg-purple-800 px-4 py-2 rounded"style="background-color: #5830be;">Department</a>
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

  
<div class="container">
    <a href="/new" class="new-button">NEW</a>
    <div class="label">
        <span class="label-text">Department</span>
    <button onclick="exportSelected()">Export</button>
    <button onclick="editselected()">Edit</button>
  </div>
    </div>
<div class="table-container">
  <button class="filter-btn" onclick="toggleColumnFilter()">Filter Columns</button>
  <div id="columnFilter" style="display: none;" class="column-toggle">
    <?php if (!empty($columns)): ?>
      <?php foreach ($columns as $col): ?>
        <label>
          <input type="checkbox" checked onchange="toggleColumn('<?php echo $col; ?>')"> <?php echo ucfirst($col); ?>
        </label>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
  <table>
  <thead>
    <tr>
      <th><input type="checkbox" onclick="selectAll(this)"></th>
      <?php foreach ($columns as $col): ?>
        <?php if ($col !== 'id'): // skip id column ?>
          <th class="col-<?php echo $col; ?>"><?php echo ucfirst($col); ?></th>
        <?php endif; ?>
      <?php endforeach; ?>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php if (!empty($customers)): ?>
      <?php foreach ($customers as $customer): ?>
        <tr data-id="<?php echo htmlspecialchars($customer['id']); ?>">
          <td><input type="checkbox" class="row-checkbox"></td>
          <?php foreach ($columns as $col): ?>
            <?php if ($col !== 'id'): // skip id column ?>
              <td class="col-<?php echo $col; ?>"><?php echo htmlspecialchars($customer[$col] ?? ''); ?></td>
            <?php endif; ?>
          <?php endforeach; ?>
          <td>
            <button 
              class="delete-btn bg-red-500 text-white px-3 py-1 rounded hover:bg-red-700"
              onclick="openDeleteModal('<?php echo htmlspecialchars($customer['id']); ?>')">
              Delete
            </button>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr>
        <td colspan="<?php echo count($columns); ?>" style="text-align:center; font-style:italic;">
          No Departments data available.
        </td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>

</div>

<!-- JS for toggleColumn etc -->
<script>
  function toggleColumnFilter() {
    const filter = document.getElementById("columnFilter");
    filter.style.display = filter.style.display === "none" ? "flex" : "none";
  }

  function toggleColumn(column) {
    const elements = document.querySelectorAll(".col-" + column);
    elements.forEach(el => {
      el.style.display = el.style.display === "none" ? "" : "none";
    });
  }
</script>
<script>
  function toggleActionMenu() {
    const menu = document.getElementById('actionDropdown');
    menu.style.display = menu.style.display === 'flex' ? 'none' : 'flex';
  }

  function getSelectedRows() {
    return Array.from(document.querySelectorAll('input.row-checkbox:checked'))
      .map(cb => cb.closest('tr'));
  }

  function exportSelected() {
    const rows = getSelectedRows();
    if (rows.length === 0) {
      alert('No rows selected');
      return;
    }

    let csv = '';
    rows.forEach(row => {
      const cells = Array.from(row.querySelectorAll('td'));
      csv += cells.map(td => `"${td.innerText}"`).join(',') + '\n';
    });

    const blob = new Blob([csv], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);

    const a = document.createElement('a');
    a.href = url;
    a.download = 'selected_customers.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
  }

</script>
<script>
  let deleteId = null;

  function openDeleteModal(id) {
    deleteId = id;
    document.getElementById('deleteModal').classList.remove('hidden');
  }

  function closeDeleteModal() {
    deleteId = null;
    document.getElementById('deleteModal').classList.add('hidden');
    document.getElementById('deletePassword').value = '';
  }

  function confirmDelete() {
  const password = document.getElementById('deletePassword').value;

  fetch('delete_department.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id: deleteId, password })
  })
  .then(res => res.json())
  .then(response => {
    console.log("Server Response:", response);
    if (response.success) {
      alert(response.message);
      document.querySelector(`tr[data-id="${deleteId}"]`).remove();
    } else {
      alert("Failed: " + (response.error || "Unknown error"));
    }
    closeDeleteModal();
  })
  .catch(err => {
    console.error("Fetch Error:", err);
    alert("Error deleting.");
    closeDeleteModal();
  });
}




  function getSelectedRows() {
    return Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.closest('tr'));
  }

  function deleteSelected() {
    const rows = getSelectedRows();
    if (rows.length === 0) {
      alert('No rows selected');
      return;
    }

    if (!confirm('Are you sure you want to delete selected rows?')) return;

    // Collect IDs
    const ids = rows.map(row => row.getAttribute('data-id'));

    // Send to server
    fetch('delete_customers.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ ids })
    })
    .then(res => res.json())
    .then(response => {
      if (response.success) {
        rows.forEach(row => row.remove());
      } else {
        alert('Failed to delete. Please try again.');
      }
    })
    .catch(err => {
      console.error(err);
      alert('Error occurred while deleting.');
    });
  }
</script>
<script>
function editselected() {
    const rows = getSelectedRows();
    if (rows.length !== 1) {
        alert('Please select exactly one row to edit.');
        return;
    }

    const row = rows[0];
    const id = row.getAttribute('data-id');

    // Har cell ko editable banaye except "name"
    row.querySelectorAll('td').forEach((cell, index) => {
        const header = document.querySelectorAll('thead th')[index];
        if (header && header.innerText.toLowerCase() === 'name') {
            // name column ko readonly rakho
            return;
        }
        if (!cell.querySelector('input') && index !== 0) { // index 0 is checkbox column
            const oldValue = cell.innerText;
            cell.innerHTML = `<input type="text" value="${oldValue}" style="width:100%;">`;
        }
    });

    // Save button add karo
    if (!row.querySelector('.save-btn')) {
        const saveBtn = document.createElement('button');
        saveBtn.innerText = 'Save';
        saveBtn.classList.add('save-btn');
        saveBtn.style.marginLeft = '10px';
        saveBtn.onclick = function() { saveSelected(id, row); };
        row.lastElementChild.appendChild(saveBtn);
    }
}

function saveSelected(id, row) {
    const updatedData = {};
    row.querySelectorAll('td').forEach((cell, index) => {
        const header = document.querySelectorAll('thead th')[index];
        if (header && index !== 0) { // skip checkbox column
            const colName = header.innerText.toLowerCase();
            if (cell.querySelector('input')) {
                updatedData[colName] = cell.querySelector('input').value;
            } else {
                updatedData[colName] = cell.innerText;
            }
        }
    });

    fetch('update_customer.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id, ...updatedData })
    })
    .then(res => res.json())
    .then(response => {
        if (response.success) {
            // Update table with new values
            row.querySelectorAll('td').forEach((cell, index) => {
                if (cell.querySelector('input')) {
                    cell.innerText = cell.querySelector('input').value;
                }
            });
            alert('Row updated successfully.');
        } else {
            alert('Failed to update.');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Error occurred while updating.');
    });
}
</script>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
  <div class="bg-white rounded-lg p-6 w-96 shadow-lg">
    <h2 class="text-lg font-bold text-red-600">⚠ Caution</h2>
    <p class="mt-2 text-gray-700">This department’s data will be permanently deleted!</p>
    
    <div class="mt-4">
      <label class="block text-sm font-medium text-gray-700">Enter Password:</label>
      <input type="password" id="deletePassword" class="w-full border rounded px-3 py-2 mt-1" placeholder="Enter password">
    </div>
    
    <div class="mt-6 flex justify-end space-x-3">
      <button onclick="closeDeleteModal()" class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">Cancel</button>
      <button onclick="confirmDelete()" class="px-4 py-2 rounded bg-red-500 text-white hover:bg-red-700">Delete</button>
    </div>
  </div>
</div>

</body>
</html>

