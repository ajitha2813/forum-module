<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'forum_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $content = $_POST['content'];
    $image = $_FILES['image']['name'];
    $video = $_FILES['video']['name'];
    $user_id = $_SESSION['user_id'];
    
    $target_dir = "uploads/";
    
    if ($image) {
        $target_file = $target_dir . basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
    }
    
    if ($video) {
        $target_file = $target_dir . basename($video);
        move_uploaded_file($_FILES['video']['tmp_name'], $target_file);
    }
    
    $sql = "INSERT INTO posts (user_id, content, image, video) VALUES ('$user_id', '$content', '$image', '$video')";
    
    if ($conn->query($sql) === TRUE) {
        echo "New post created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$sql = "SELECT * FROM posts ORDER BY created_at DESC";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="dashboard.css">
    <title>Dashboard</title>
</head>
<body>
    <h2>Dashboard</h2>
    <form method="post" enctype="multipart/form-data">
        Content: <textarea name="content" required></textarea><br>
        Image: <input type="file" name="image"><br><br>
        Video: <input type="file" name="video"><br>
        <input type="submit" value="Post">
    </form>
    <h2>Forum Posts</h2>
    <?php while($row = $result->fetch_assoc()): ?>
        <div>
            <p><?php echo $row['content']; ?></p>
            <?php if ($row['image']): ?>
                <img src="uploads/<?php echo $row['image']; ?>" width="200">
            <?php endif; ?>
            <?php if ($row['video']): ?>
                <video width="200" controls>
                    <source src="uploads/<?php echo $row['video']; ?>" type="video/mp4">
                </video>
            <?php endif; ?>
            <small>Posted on: <?php echo $row['created_at']; ?></small>
        </div>
        <hr>
    <?php endwhile; ?>
</body>
</html>
