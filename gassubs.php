
<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

// Include database connection
require_once "database.php";

$user_id = $_SESSION['user']['id']; // Assuming 'id' is the primary key of the users table
$sql = "SELECT full_name, role FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $full_name, $selected_role);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
} else {
    $full_name = "Unknown"; // Default value if unable to fetch full name
    $selected_role = "Unknown"; // Default value if unable to fetch selected role
}

if (!isset($_SESSION['user'])) {
    header("Location: login.php"); 
    exit();
}

$allowedRoles = array('GAS');
if (!in_array($_SESSION['user']['role'], $allowedRoles)) {
    header("Location: index.php");  
    exit();
}


$roleHomePageMap = [
    'ICT' => 'ICT11home.php',
    'ABM' => 'ABM11home.php',
    'Humms' => 'HUMMS11home.php',
    'STEM' => 'STEM11home.php',
    'GAS' => 'GAS11home.php',
    'HUMMS12' => 'HUMMS12home.php',
    'ICT12' => 'ICT12home.php',
    'STEM12' => 'STEMS12home.php',
    'ABM12' => 'ABM12home.php',
    'GAS12' => 'GAS12home.php'

    
];


if (array_key_exists($_SESSION['user']['role'], $roleHomePageMap)) {
    
    $homePageURL = $roleHomePageMap[$_SESSION['user']['role']];
} else {
   
    echo "No home page found for the given role.";
}


?> 
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>GAS 11</title>
    <link
      rel="stylesheet"
      href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css"
    />
    <link
      rel="stylesheet"
      href="https://maxst.icons8.com/vue-static/landings/line-awesome/font-awesome-line-awesome/css/all.min.css"
    />
    <link rel="stylesheet" href="adminhome.css" />
    <link rel="stylesheet" href="handout.css" />
  </head>
  <body>
    <input type="checkbox" id="nav-toggle" />
    <div class="sidebar">
      <div class="sidebar-brand">
        <h2><span class="lab la-accusoft"></span><span>EmpowerEDU</span></h2>
      </div>

      <div class="sidebar-menu">
        <ul>
          <li>
            <a href="" 
              ><span class="las la-igloo"></span> <span>Dashboard</span></a
            >
          </li>
          <li>
            <a href="1and2.php" class="active"
              ><span class="las la-clipboard-list"></span>
              <span>Handout 1 and 2</span></a
            >
          </li>
          <li>
            <a href="3and4.php"
              ><span class="las la-clipboard-list"></span>
              <span>Handout 3 and 4</span></a
            >
          </li>
          <li>
            <a href="5and6.php"
              ><span class="las la-clipboard-list"></span>
              <span>Handout 5 and 6</span></a
            >
          </li>
          <li>
            <a href=""
              ><span class="las la-clipboard-list"></span>
              <span>GAS 11 Subs</span></a
            >
          </li>
           <li>
            <a href="logout.php">
            <span class="material-symbols-outlined">
                Logout
            </span>

            </a>
        </li>
        </ul>
      </div>
    </div>

    <div class="main-content">
      <header>
        <h3>
          <div class="header-title">
            <label for="nav-toggle">
              <span class="las la-bars"></span>
            </label>
            GAS 11 Subs
          </div>
        </h3>

        <div class="search-wrapper">
          <span class="las la-search"></span>
          <input type="search" placeholder="Search here" />
        </div>

        <div class="user-wrapper">
          <img src="img/2.jpg" width="30px" height="30px" alt="" />
          <div>
            <h4><?php echo $full_name; ?></h4>
            <small><?php echo $selected_role; ?></small>
          </div>
        </div>
      </header>

      <main>
             <div class="">
                <div class="image-modal">
                    <span class="close">&times;</span>
                    <img class="modal-content" id="expandedImg">
                </div>
            </div>
            <div class="cards">
                <?php 
                include "handoutdb.php";
                $limit = 15; 
                $sql = "SELECT * FROM handoutgas11 ORDER BY id DESC LIMIT $limit";
                $res = mysqli_query($conn, $sql);

                if (mysqli_num_rows($res) > 0) {
                    while ($handoutgas11 = mysqli_fetch_assoc($res)) { ?>
                        <div class="alb">
                            <?php 
                            $fileExtension = pathinfo($handoutgas11['image_url'], PATHINFO_EXTENSION);
                            if ($fileExtension === 'pdf') {
                                echo '<img src="img/pdficon.png" alt="PDF File">';
                            } elseif ($fileExtension === 'doc' || $fileExtension === 'docx') {
                                echo '<img src="img/wordicon.png" alt="Word File">';
                            } elseif ($fileExtension === 'pptx') {
                                echo '<img src="img/pptxicon.png" alt="PowerPoint File">';
                            } elseif ($fileExtension === 'mp4') {
                                echo '<img src="img/mp4icon.png" alt="MP4 File">';
                            } elseif ($fileExtension === 'mp3') {
                                echo '<img src="img/mp3icon.png" alt="MP3 File">';
                            } elseif ($fileExtension === 'gif') {
                                echo '<img src="img/gificon.png" alt="GIF File">';
                            } else {
                                echo '<img src="uploads/'.$handoutgas11['image_url'].'" alt="Uploaded Image">';
                            }
                            ?>
                            <p><?php echo $handoutgas11['filename']; ?></p>
                            <a class="download-btn" href="uploads/<?=$handoutgas11['image_url']?>" download="<?=$handoutgas11['image_url']?>">
                                <img src="img/download.png" alt="Download" width="20" height="20">
                            </a>
                    </div>
                <?php }
            }
            ?>
</div>
      </main>
    </div>
         <script src="script.js"></script>
         <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
         <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

         <script>
         function goBack() {
            <?php
            
            echo "window.location.href = '$homePageURL';";
            ?>
        }
    document.addEventListener('click', function (e) {
        if (e.target && e.target.matches('.alb img')) {
            if (!e.target.closest('.download-btn')) {
                document.getElementById('expandedImg').src = e.target.src;
                document.querySelector('.image-modal').style.display = 'block';
            }
        }
    });
    document.querySelector('.close').addEventListener('click', function() {
        document.querySelector('.image-modal').style.display = 'none';
    });
</script>
             
  </body>
</html>