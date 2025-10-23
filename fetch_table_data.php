<?php
$table = $_GET['table'];

$db1 = new mysqli("", "", "", "");
$db2 = new mysqli("", "", "", "");

// Expense DB → amount total
$res1 = $db1->query("SELECT SUM(amount) as total_amount FROM `$table`");
$row1 = $res1->fetch_assoc();
$total_amount = floatval($row1['total_amount']);

// AZ DB → balance total
$res2 = $db2->query("SELECT SUM(balance) as total_balance FROM `$table`");
$row2 = $res2->fetch_assoc();
$total_balance = floatval($row2['total_balance']);

// Final calculation
$diff = $total_amount - $total_balance;

function format_currency($value) {
    return number_format(abs($value), 0, '.', ',');
}

// Display
function formatTableName($name) {
    return ucwords(str_replace("_", " ", $name));
}

echo "<h3 class='text-center text-2xl font-bold text-purple-700 mb-6'>
        Table: " . formatTableName($table) . "
      </h3>";

echo "<div class='flex justify-center gap-10 w-full max-w-5xl mx-auto'> 
        <!-- Credit Box -->
        <div class='flex-1 bg-white border border-gray-300 rounded-lg shadow-md px-10 py-10 text-center'>
            <h4 class='bg-purple-600 text-white py-4 rounded-t-lg text-2xl font-semibold'>Credit</h4>";
if ($diff >= 0) {
    echo "<p class='text-green-600 font-bold text-3xl mt-6'>" . format_currency($diff) . "</p>";
} else {
    echo "<p class='text-gray-500 text-3xl mt-6'>-</p>";
}
echo "  </div>

        <!-- Debit Box -->
        <div class='flex-1 bg-white border border-gray-300 rounded-lg shadow-md px-10 py-10 text-center'>
            <h4 class='bg-purple-600 text-white py-4 rounded-t-lg text-2xl font-semibold'>Debit</h4>";
if ($diff < 0) {
    echo "<p class='text-red-600 font-bold text-3xl mt-6'>" . format_currency($diff) . "</p>";
} else {
    echo "<p class='text-gray-500 text-3xl mt-6'>-</p>";
}
echo "  </div>
      </div>";
