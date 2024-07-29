<?php
include("dbcon.php");
session_start();
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM `images` WHERE `id` = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $student = $result->fetch_assoc();
        $stmt->close();
    } else {
        $_SESSION['status'] = "Error fetching record: " . $conn->error;
        header("Location: view.php");
        exit();
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = $_POST['name'];
        $rollno = $_POST['rollno'];
        $profile = $_FILES['profile']['name'];

        // Handle file upload
        if (!empty($profile)) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["profile"]["name"]);
            move_uploaded_file($_FILES["profile"]["tmp_name"], $target_file);
        } else {
            $profile = $student['profile']; // Keep the old profile if no new file is uploaded
        }

        // Update record in the database
        $sql = "UPDATE `images` SET `name` = ?, `rollno` = ?, `profile` = ? WHERE `id` = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssi", $name, $rollno, $profile, $id);
            if ($stmt->execute()) {
                $_SESSION['status'] = "Record updated successfully!";
                header("Location: view.php");
                exit();
            } else {
                $_SESSION['status'] = "Error updating record: " . $conn->error;
            }
            $stmt->close();
        } else {
            $_SESSION['status'] = "Error preparing update query: " . $conn->error;
        }
    }
} else {
    $_SESSION['message'] = "No ID specified!";
    header("Location: view.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="icon" type="image/jpg" href="sl_z_072523_61700_05.jpg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .profile-picture {
            max-width: 150px;
            max-height: 150px;
            object-fit: cover;
            border-radius: 50%;
        }

        .form-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .form-container .form-group {
            flex: 1;
        }

        .profile-picture-container {
            margin-left: 3px;
            width: 483px;
        }
    </style>
</head>

<body>
    <div class="section bg-dark">
        <nav class="navbar navbar-expand-lg navbar-light p-2 container">
            <a href="index.php"><h4>Twitter</h4></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item active">
                        <a class="nav-link text-light" href="#"></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-light" href="#"></a>
                    </li>
                    <li class="nav-item">
                        <a href="addnew.php" class="btn btn-primary mx-2">Add Submission</a>
                    </li>
                    <li class="nav-item">
                        <a href="view.php" class="btn btn-primary logoutbtn">View Submission</a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>

    <div class="container mt-1">
        <div class="alert">
            <?php if (isset($_SESSION['status'])) echo "<p class='text-white bg-success p-2 rounded'>" . $_SESSION['status'] . "</p>";
            unset($_SESSION["status"]); ?>
        </div>
        <form action="updatepage.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
            <h2>Update Submission</h2>
            <div class="form-container">
                <div class="">
                    <div class="form-group mb-2">
                        <label class="text-light" for="username"><b>Name</b></label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" required>
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-light" for="rollno"><b>Roll Number</b></label>
                        <input type="text" class="form-control" id="rollno" name="rollno" value="<?php echo htmlspecialchars($student['rollno']); ?>" required>
                    </div>
                    <div class="form-group mb-2 bg-light p-2 mt-4 rounded">
                        <label for="documents[]">Relevent Documents</label>
                        <input type="file" class="form-control-file" id="documents" name="documents[]" multiple required>
                    </div>
                </div>
                <div class="profile-picture-container bg-white rounded p-2">
                    <?php if (!empty($student['profile'])) : ?>
                        <img id="profilePicPreview" class="profile-picture " src="uploads/<?php echo htmlspecialchars($student['profile']); ?>" alt="Current Profile Picture" width="100" class="mt-2">
                    <?php endif; ?>
                    <br>
                    <label for="profilepicture">Profile Picture</label>
                    <input type="file" class="form-control" id="profile" name="profile">

                </div>
            </div>
            <button type="submit" name="submit" class="btn btn-success mt-3">Submit</button>
        </form>
    </div>
    <script>
        const avatar = '9720027.jpg'; // Default image URL

        document.getElementById('profilepicture').addEventListener('change', function(event) {
            const input = event.target;
            const file = input.files[0];

            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    const inputimage = document.getElementById('profilePicPreview');
                    inputimage.src = e.target.result;
                };

                reader.readAsDataURL(file);
            } else {

                const inputimage = document.getElementById('profilePicPreview');
                inputimage.src = avatar;
            }
        });


        document.getElementById('profilepicture').addEventListener('click', function(event) {
            const input = event.target;
            input.value = '';
            const inputimage = document.getElementById('profilePicPreview');
            inputimage.src = avatar;
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>