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
  <title>Department</title>
  <link rel="icon" type="image/png" sizes="32x32" href="/images/weblogo.png?v=2">
<link rel="icon" type="image/png" sizes="16x16" href="/images/weblogo.png?v=2">
<link rel="shortcut icon" href="/images/weblogo.png?v=2">

        <script src="https://cdn.tailwindcss.com"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f4f7fa;
    }
    
    .container {
      padding: 40px;
      max-width: 1200px; /* Increased width */
      margin: 40px auto;
      background: white;
      margin-top: 15px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      padding-bottom: 4px;
    }
    .form-header {
      display: flex;
      align-items: center;
      gap: 20px;
      margin-bottom: 30px;
    }
    .img-container {
      position: relative;
    }
    .img {
      width: 90px;
      height: 90px;
      object-fit: cover;
      border-radius: 50%;
      border: 2px solid #d3d3d3;
    }
    .upload-btn {
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      background: #6b3de6;
      color: white;
      border: none;
      border-radius: 12px;
      padding: 4px 10px;
      font-size: 12px;
      cursor: pointer;
      transition: background-color 0.3s;
    }
    .upload-btn:hover {
      background: #5830be;
    }
    .form-header input[type="text"] {
      border: none;
      border-bottom: 2px solid #aaa;
      font-size: 26px;
      font-weight: 600;
      padding: 8px 4px;
      width: 100%;
      background: transparent;
      outline: none;
      color: #333;
    }
    .form-section {
      margin-top: 10px;
    }
    .form-group {
      margin-bottom: 24px;
    }
    .form-group label {
      display: block;
      font-weight: 600;
      font-size: 14px;
      margin-bottom: 6px;
      color: #444;
    }
    .form-group input,
    .form-group select {
      width: 98%;
      padding: 8px; /* Reduced padding for smaller input size */
      border: none;
      border-radius: 8px;
      background: transparent;
      font-size: 14px;
      outline: none;
      color: #333;
      transition: border-color 0.3s;
      border-bottom: 1px solid;
    }
    .form-group input:focus,
    .form-group select:focus {
      border-color: #6b3de6;
    }
    .form-row {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
    }
    .form-row .form-group {
      flex: 1;
      min-width: 220px;
    }
    .form-row .form-group.small {
      flex: 0 0 30%; /* Smaller width for email and phone */
    }
    
  </style>
  <!-- CSS Styling -->
<style>
  .icon-container {
      margin-left: 10px;
      margin-top: 70px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    background-color: rgba(255, 255, 255, 0.3); /* transparent background */
    border: 1px solid #ccc;
    padding: 12px 18px;
    border-radius: 10px;
    width: fit-content;
    backdrop-filter: blur(8px); /* optional blur */
    box-shadow: 0 4px 10px rgba(0,0,0,0.08);
  }

  .label {
    font-weight: 600;
    font-size: 16px;
    color: #333;
    margin-right: 10px;
  }

  .icons {
    display: flex;
    gap: 12px;
  }

  .icons i {
    font-size: 18px;
    color: #666;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .icons i:hover {
    color: #6b3de6;
    transform: scale(1.2);
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
  <form id="customerform" action="your_target_script.php" method="POST" enctype="multipart/form-data">
<div class="icon-container" style="display: flex; align-items: center; gap: 10px; margin-left: 259px;">
  
  <!-- Customer Label -->
  <span class="label" style="font-size: 18px; font-weight: bold;">Customer</span>
  
  <!-- Icon with Save text -->
  <div class="icons" style="text-align: center;">
    <i class="fa fa-cloud-upload fa-fw" style="cursor:pointer; font-size: 28px;" 
       onclick="document.getElementById('customerform').submit();"></i>
    <div style="font-size: 12px; margin-top: 2px;">Save</div>
  </div>
  
</div>


<form>
  <div class="container">
    <div class="form-header">
      <input type="text" id="name" name="name" placeholder="Full Name" required>

      <div class="form-row">
        <div class="form-group">
          <label for="email" style="margin-top: 5px;">Email</label>
          <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
          <label for="phone" style="margin-top: 5px;">Phone</label>
          <input type="tel" id="phone" name="phone" required>
        </div>
      </div>
    </div>

    <div class="form-section">
      <div class="form-group">
        <label for="designation">Designation</label>
        <input type="text" id="designation" name="designation" placeholder="e.g. Sales Director" required>
      </div>

      <div class="form-group">
        <label for="ntn">NTN</label>
        <input type="text" id="ntn" name="ntn" placeholder="not applicable">
      </div>

     
      </div>
    </div>
  </div>
</form>

  

 
  </body>
</html>
&nbsp;
&nbsp;

