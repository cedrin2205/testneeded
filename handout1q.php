<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php"); 
    exit();
}

$allowedRoles = array('ICTteacher', 'ABMteacher', 'HUMMSteacher','STEMteacher','GASteacher');
if (!in_array($_SESSION['user']['role'], $allowedRoles)) {
    header("Location: index.php");  
    exit();
}


$roleHomePageMap = [
    'ICTteacher' => 'ICTTEACHER11home.php',
    'ABMteacher' => 'ABMTEACHER11home.php',
    'HUMMSteacher' => 'HUMMSteacher11home.php',
    'STEMteacher' => 'STEMTEACHER11home.php',
    'GASteacher' => 'GASTEACHER11home.php',
    
];


if (array_key_exists($_SESSION['user']['role'], $roleHomePageMap)) {
    
    $homePageURL = $roleHomePageMap[$_SESSION['user']['role']];
} else {
   
    echo "No home page found for the given role.";
}

if (isset($_GET['delete_image'])) {
    $imageToDelete = $_GET['delete_image'];
  
    include "handoutdb.php";
   
    $deleteQuery = "DELETE FROM handout1q WHERE image_url = '$imageToDelete'";
    if (mysqli_query($conn, $deleteQuery)) {
        if (unlink("uploads/$imageToDelete")) {
            header("Location: handout1q.php");
            exit();
        } else {
            echo "Failed to delete the file from the server.";
        }
    } else {
        echo "Failed to delete the image from the database.";
    }
}

?> 
<!DOCTYPE html>
<html lang="en">
  <head>
      <meta charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Handout1</title>
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
      <link rel="stylesheet" href="upload.css">
      <link rel="stylesheet" href="students.css">
      <link rel="stylesheet" href="handout.css">
      <link rel="stylesheet" href="studentshome.css">

  </head>
  <body>
    
        <header>
           <div class="profile-strand">
                <img class="profile-img" src="img/blob.jpg">
                <p class="strand">Teachers Upload 1-2</p>
            </div>

            <a><button onclick="goBack()" class="btn">Back</button></a>
        </header>

        <section>
           
            <div class="modules-wrap">
                <div class="image-modal">
                    <span class="close">&times;</span>
                    <img class="modal-content" id="expandedImg">
                </div>
            </div>

            <div class="contain">
                <form action="handoutupload1q.php" method="post" enctype="multipart/form-data">
                    <input type="file" name="my_image">
                    <input type="submit" name="submit" value="Upload">
                </form>
            </div>
            <div class="gallery">
                <?php 
                include "handoutdb.php";
                $limit = 15; 
                $sql = "SELECT * FROM handout1q ORDER BY id DESC LIMIT $limit";
                $res = mysqli_query($conn, $sql);

                if (mysqli_num_rows($res) > 0) {
                    while ($handout1q = mysqli_fetch_assoc($res)) { ?>
                        <div class="alb">
                            <?php 
                            $fileExtension = pathinfo($handout1q['image_url'], PATHINFO_EXTENSION);
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
                                echo '<img src="uploads/'.$handout1q['image_url'].'" alt="Uploaded Image">';
                            }
                            ?>
                            <p><?php echo $handout1q['filename']; ?></p>
                            <a class="delete-btn" href="handout1q.php?delete_image=<?=$handout1q['image_url']?>">
                                <img src="img/remove.png" alt="Delete" width="1" height="1">
                            </a>
                            <a class="download-btn" href="uploads/<?=$handout1q['image_url']?>" download="<?=$handout1q['image_url']?>">
                                <img src="img/download.png" alt="Download" width="20" height="20">
                            </a>
                    </div>
                <?php }
            }
            ?>
</div>
         </section>

         <script src="script.js"></script>
         <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
         <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
             
  </body>
</html>
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
    document.querySelectorAll('.delete-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            var confirmDelete = confirm("Are you sure you want to delete this image?");
            if (confirmDelete) {
            }
        });
    });

    document.querySelector('.close').addEventListener('click', function() {
        document.querySelector('.image-modal').style.display = 'none';
    });
</script>
</body>
</html>
