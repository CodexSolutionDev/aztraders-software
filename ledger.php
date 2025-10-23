<?php
// Password
$correct_password = "";

// Agar form submit hua
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['password'] === $correct_password) {
        // Access allow karo
        $access_granted = true;
    } else {
        $error = "Wrong password!";
    }
}
if (basename($_SERVER['PHP_SELF']) !== 'view.php') {
    unset($_SESSION['access_granted_view']);
}
// Agar password correct nahi dala to form show karo
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
        border-color: #a64d79;
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
$host = '';
$dbname = '';
$username = '';
$password = '';

// DB Connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$tables = [];
$sql = "SHOW TABLES";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_array()) {
        $tableName = $row[0];
        if ($tableName !== 'sales' && $tableName !== 'customers' && $tableName !== 'vendor_bills') {
            $displayName = str_replace("_", " ", $tableName); // remove underscores
            $tables[] = [
                'name' => $tableName,
                'display' => ucwords($displayName)
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <!-- Tailwind CSS CDN -->
<script src="https://cdn.tailwindcss.com"></script>
  <title>Ledger</title>
  <link rel="icon" type="image/png" sizes="32x32" href="/images/weblogo.png?v=2">
<link rel="icon" type="image/png" sizes="16x16" href="/images/weblogo.png?v=2">
<link rel="shortcut icon" href="/images/weblogo.png?v=2">

  <link rel="icon" type="image/png" href="/weblogo.png">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<head>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
  

 body {
    margin: 0;
    font-family: Arial, sans-serif;
    background-color: #f5f7fa;
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
      <a href="/department" class="hover:bg-purple-800 px-4 py-2 rounded">Department</a>
      <a href="/staff" class="hover:bg-purple-800 px-4 py-2 rounded">Staff</a>
      <a href="/Form" class="hover:bg-purple-800 px-4 py-2 rounded">Form</a>
      <a href="/ledger" class="hover:bg-purple-800 px-4 py-2 rounded"style="background-color: #5830be;">Ledger</a>           
      <a href="/expense" class="hover:bg-purple-800 px-4 py-2 rounded">Expense</a>
      <a href="/view" class="hover:bg-purple-800 px-4 py-2 rounded">View</a>
    </div>

    <!-- Right Side: Username -->
    <div id="username-display" class="text-sm font-semibold" >
      <?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?>
    </div>

  </div>
</div>

<div class="mt-24 px-4" style="
    margin-top: 60px;
">

  <!-- 🔍 Centered Search Bar Container -->
  <div class="max-w-md mx-auto mb-8"style="
    margin-bottom: 10px;
">
    <div class="bg-white border border-gray-200 rounded-lg shadow p-2">
     

      <input
        type="text"
        id="searchInput"
        placeholder="Search Department"
        onkeyup="searchTable()"
        class="w-full px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent placeholder-gray-400" style="
    text-align: center;
"
      />

      <ul
        id="suggestions"
        class="mt-2 max-h-60 overflow-y-auto bg-white border border-gray-200 rounded-md shadow divide-y divide-gray-100 hidden"
      ></ul>
    </div>
  </div>

  <!-- 📋 Table Section (Also Centered) -->
  <div class="max-w-5xl mx-auto">
    <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6">
      <h2 id="selectMessage" class="text-xl font-semibold mb-4 text-center text-gray-700" style="text-align: center;">
    Please select a Department
</h2>

      <div id="tableData" class="w-full overflow-x-auto text-left"></div>
    </div>
  </div>

</div>



<script>
function toTitleCase(str) {
  return str
    .replace(/_/g, ' ')
    .replace(/\w\S*/g, w => w.charAt(0).toUpperCase() + w.slice(1));
}

function searchTable() {
  let query = document.getElementById('searchInput').value;
  let suggestions = document.getElementById('suggestions');

  if (query.length < 2) {
    suggestions.innerHTML = '';
    suggestions.classList.add('hidden');
    return;
  }

  fetch(`get_common_tables.php?query=${query}`)
    .then(response => response.json())
    .then(data => {
      suggestions.innerHTML = '';
      if (data.length === 0) {
        suggestions.classList.add('hidden');
        return;
      }

      data.forEach(table => {
        let li = document.createElement('li');
        li.textContent = toTitleCase(table);
        li.className = "px-4 py-2 text-left hover:bg-purple-100 cursor-pointer text-sm";
        li.onclick = () => loadTableData(table);
        suggestions.appendChild(li);
      });

      suggestions.classList.remove('hidden');
    });
}
function loadTableData(table) {
  fetch(`fetch_table_data.php?table=${table}`)
    .then(response => response.text())
    .then(html => {
      document.getElementById('tableData').innerHTML = html;
      document.getElementById('suggestions').innerHTML = '';
      document.getElementById('suggestions').classList.add('hidden');
      document.getElementById('searchInput').value = ''; // 🟢 Clear search bar
    });
}
function loadTableData(table) {
  fetch(`fetch_table_data.php?table=${table}`)
    .then(response => response.text())
    .then(html => {
      document.getElementById('tableData').innerHTML = html;
      document.getElementById('suggestions').innerHTML = '';
      document.getElementById('suggestions').classList.add('hidden');
      document.getElementById('searchInput').value = ''; // search bar clear kare
      document.getElementById('selectMessage').style.display = 'none'; // 🟢 message hide kare
    });
}


document.addEventListener('click', function (e) {
  const input = document.getElementById('searchInput');
  const suggestions = document.getElementById('suggestions');
  if (!suggestions.contains(e.target) && e.target !== input) {
    suggestions.classList.add('hidden');
  }
});


</script>
