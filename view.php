<?php
include("dbcon.php");
session_start();

// Fetch data from the database
$sql = "SELECT * FROM `images` ORDER BY ID DESC";
$result = $conn->query($sql);
$students = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Submissions</title>
    <link rel="icon" type="image/jpg" href="sl_z_072523_61700_05.jpg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            border: 1px solid #dee2e6;
            padding: 0.75rem;
        }

        .navbar {
            margin-bottom: 1rem;
        }

        .profile-pic {
            border-radius: 50%;
        }
    </style>
</head>

<body>
    <div class="section bg-dark">
        <nav class="navbar navbar-expand-lg navbar-light p-2 container">
            <a class="navbar-brand text-light" href="#">
            <a href="index.php"><h4>Twitter</h4></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a href="addnew.php" class="btn btn-primary mx-2">Add Submission</a>
                        </li>
                    </ul>
                </div>
        </nav>
    </div>

    <div class="container">
        <div class="alert">
            <?php if (isset($_SESSION['status'])) echo "<p class='text-white bg-success p-2 rounded'>" . $_SESSION['status'] . "</p>";
            unset($_SESSION["status"]); ?>
        </div>
        <h2>All Submissions</h2>
        <table class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th>S.No</th>
                    <th>Name</th>
                    <th>Roll No</th>
                    <th>Relevant Documents</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 0;
                foreach ($students as $student) {
                    $i++;
                ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td>
                            <img class="profile-pic me-2" src="uploads/<?php echo htmlspecialchars($student['profile']); ?>" alt="Profile Picture" width="30" height="30">
                            <?php echo htmlspecialchars($student['name']); ?>
                        </td>
                        <td><?php echo htmlspecialchars($student['rollno']); ?></td>
                        <td>
                            <?php
                            $documents = explode(',', $student['result']);
                            foreach ($documents as $document) {
                                echo '<a href="uploads/' . htmlspecialchars($document) . '" target="_blank" class="d-block">View Online</a>';
                            }
                            ?>
                        </td>
                        <td>
                            <a class="btn btn-primary" href="updatepage.php?id=<?php echo $student['id']; ?>">Edit</a>
                            <a class="btn btn-danger" href="delete.php?id=<?php echo $student['id']; ?>">Delete</a>
                        </td>

                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-s6x5gjEjw4VRTWvP4LwFQXReLk1k5FkA3eOeo5+8M9l/Md4X7zwG2T6dq5g5uB7y" crossorigin="anonymous"></script>
</body>

</html>