<?php
if (isset($_GET['selected_table'])) {
    $selectedTable = $_GET['selected_table'];
    $displayTableName = str_replace('_', ' ', $selectedTable);

    // Fetch existing data from selected table (optional)
    // $query = "SELECT * FROM `$selectedTable`";
    // $result = mysqli_query($conn, $query);
?>
<!-- Conditional Display After Table Selection -->
<div style="margin-top: 40px; display: flex; justify-content: center;">
  <div style="
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-radius: 16px;
    padding: 30px;
    width: 90%;
    max-width: 1000px;
    border: 1px solid rgba(255, 255, 255, 0.18);
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
  ">
    <h2 style="color: #333; text-align: center; margin-bottom: 20px;">
      <?= htmlspecialchars(ucwords($displayTableName)) ?>
    </h2>

    <form method="POST" action="/save_lines">
      <input type="hidden" name="table_name" value="<?= htmlspecialchars($selectedTable) ?>">

      <table id="data-table" style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
        <thead>
          <tr style="background-color: #6b3de6; color: white;">
            <th style="padding: 10px;">Date</th>
            <th style="padding: 10px;">Detail of Purchase</th>
            <th style="padding: 10px;">Quantity</th>
            <th style="padding: 10px;">Rate</th>
            <th style="padding: 10px;">Amount</th>
          </tr>
        </thead>
        <tbody id="table-body">
          <!-- Default row -->
          <tr>
            <td><input type="date" name="date[]" required style="width: 100%; padding: 5px;"></td>
            <td><input type="text" name="detail[]" required style="width: 100%; padding: 5px;"></td>
            <td><input type="number" name="qty[]" required style="width: 100%; padding: 5px;"></td>
            <td><input type="number" name="rate[]" required style="width: 100%; padding: 5px;"></td>
            <td><input type="number" name="amount[]" required style="width: 100%; padding: 5px;" readonly></td>
          </tr>
        </tbody>
      </table>

      <button type="button" onclick="addRow()" style="
        padding: 10px 20px;
        margin-right: 10px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
      ">Add Line</button>

      <button type="submit" style="
        padding: 10px 20px;
        background-color: #2196F3;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
      ">Save</button>
    </form>
  </div>
</div>

<script>
function addRow() {
  const table = document.getElementById('table-body');
  const newRow = document.createElement('tr');
  newRow.innerHTML = `
    <td><input type="date" name="date[]" required style="width: 100%; padding: 5px;"></td>
    <td><input type="text" name="detail[]" required style="width: 100%; padding: 5px;"></td>
    <td><input type="number" name="qty[]" required style="width: 100%; padding: 5px;"></td>
    <td><input type="number" name="rate[]" required style="width: 100%; padding: 5px;"></td>
    <td><input type="number" name="amount[]" required style="width: 100%; padding: 5px;" readonly></td>
  `;
  table.appendChild(newRow);
}
</script>
<?php } ?>
