<!--
Authors: Alexandra, Jose, Brinley, Ben, Kyle
Date: 03/28/2025
Revisions:
     03/38/2025 -- Alexandra Stratton -- created manager_header.php
     4/26/2025 - Brinley, fix bug with null content id
 Purpose: the header for the manager side.

 -->
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
body {
  font-family: "Lato", sans-serif;
  transition: margin-left 0.5s;
}

.sidenav {
  height: 100%;
  width: 0;
  position: fixed;
  z-index: 100;
  top: 0;
  left: 0;
  background-color: #111;
  overflow-x: hidden;
  transition: 0.5s;
  padding-top: 60px;
}
.sidenav a, .dropdown-btn {
  padding: 6px 8px 6px 16px;
  text-decoration: none;
  font-size: 20px;
  color: white;
  display: block;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
  cursor: pointer;
  outline: none;
}

.sidenav p {
  padding: 6px 8px 6px 16px;
  text-decoration: none;
  font-size: 20px;
  color: white;
  display: block;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
  cursor: pointer;
  outline: none;
}

.sidenav a:hover, .dropdown-btn:hover {
  background-color: red;
}

.active {
  background-color: red;
  color: white;
}

.dropdown-container {
  display: none;
  background-color: #262626;
  padding-left: 8px;
}

.fa-caret-down {
  float: right;
  padding-right: 8px;
}

.closebtn {
  position: absolute;
  top: 0;
  right: 25px;
  font-size: 36px;
  margin-left: 50px;
  background-color: #111;
  color: white;
  cursor: pointer;
  border: none;
}

.openbtn {
  position: fixed !important;
  top: 10px !important;
  left: 10px !important;
  width: 40px !important;
  height: 40px !important;
  background-color: #333 !important;
  color: #fff !important;
  border: none !important;
  border-radius: 5px !important;
  cursor: pointer !important;
  z-index: 10 !important;
  font-size: 20px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
}

.openbtn:hover {
  background-color: #444;
}
#content {
  transition: margin-left 0.5s;
  padding: 20px;
}


/* Some media queries for responsiveness */
@media screen and (max-height: 450px) {
  .sidenav {padding-top: 15px;}
  .sidenav a {font-size: 18px;}
}
</style>
</head>
<body>
<div class="barber" id="barber">
    <div class="sidenav" id="sideNav">
        <button class="closebtn" id="sidebar-deactive" onclick="closeNav()">&times;</button>
        <a href="dashboard.php">Dashboard</a>
        <a href="appointments.php">Appointments</a>
        <a href="checkouts.php">Checkout History</a>
        <a href="barber_profile.php">Profile</a>
        <a href="product.php">Products</a>
        <a href="orders.php">Orders</a>
        <button class="dropdown-btn">Clients 
            <i class="fa fa-caret-down"></i>
        </button>
        <div class="dropdown-container">
            <a href="client.php">Client List</a>
            <a href="see_feedback.php">Feedback</a>
            <a href="testimonies.php">Reviews & Testimonies</a>

        </div>
        <button class="dropdown-btn">Employees 
            <i class="fa fa-caret-down"></i>
        </button>
        <div class="dropdown-container">
            <a href="employees.php">Employee List</a>
            <a href="services_manager.php">Employee Services</a>
            <a href="set_hours_manager.php">Employee Hours</a> 
        </div>
        <a href="register.php">Create New Account</a> 
        <a href="logout.php"><i class="fa fa-sign-out"></i> Log Out</a>
    </div>
</div>
<div id="main">
  <button id="sidebarToggle" class="openbtn" onclick="openNav()">☰</button>  
</div>

    <script>
var dropdown = document.getElementsByClassName("dropdown-btn");
for (let i = 0; i < dropdown.length; i++) {
  dropdown[i].addEventListener("click", function () {
    this.classList.toggle("active");
    var dropdownContent = this.nextElementSibling;
    dropdownContent.style.display = 
      dropdownContent.style.display === "block" ? "none" : "block";
  });
}

function openNav() {
  document.getElementById("sideNav").style.width = "250px";
  if (document.getElementById("content")) {
    document.getElementById("content").style.marginLeft = "250px";
  }
}

function closeNav() {
  document.getElementById("sideNav").style.width = "0";
  if (document.getElementById("content")) {
    document.getElementById("content").style.marginLeft = "0";
  }
}
document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById("sidebarToggle");
    const body = document.body;

    toggleBtn.addEventListener("click", function () {
        body.classList.add("sidebar-active");
        body.classList.remove("sidebar-deactive");

    });
});

document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn2 = document.getElementById("sidebar-deactive");
    const body = document.body;

    toggleBtn2.addEventListener("click", function () {
        body.classList.add("sidebar-deactive");
        body.classList.remove("sidebar-active");

    });
});
</script>
</body>
</html>