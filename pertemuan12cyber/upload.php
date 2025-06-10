<?php
$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

$message = '';
$message_type = 'success'; // 'success' atau 'error'

// Pastikan direktori "uploads" ada, jika tidak, buatkan.
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// Cek jika request adalah POST
if(isset($_POST["submit"])) {
    
    // Cek apakah file sudah ada
    if (file_exists($target_file)) {
        $message = "Maaf, file dengan nama tersebut sudah ada.";
        $uploadOk = 0;
    }

    // Cek ukuran file (misal, maks 5MB)
    if ($_FILES["fileToUpload"]["size"] > 5000000) {
        $message = "Maaf, ukuran file Anda terlalu besar. Maksimal 5MB.";
        $uploadOk = 0;
    }

    // Izinkan format file tertentu (opsional, bisa diaktifkan jika perlu)
    /*
    $allowed_types = array("jpg", "png", "jpeg", "gif", "pdf", "doc", "docx");
    if (!in_array($fileType, $allowed_types)) {
        $message = "Maaf, hanya file JPG, JPEG, PNG, GIF, PDF, DOC & DOCX yang diperbolehkan.";
        $uploadOk = 0;
    }
    */

    // Cek jika $uploadOk bernilai 0 karena ada error
    if ($uploadOk == 0) {
        if (empty($message)) {
           $message = "Maaf, file Anda tidak dapat diunggah karena kesalahan yang tidak diketahui.";
        }
        $message_type = 'error';
    // Jika semua oke, coba unggah file
    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            $message = "File ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " telah berhasil diunggah.";
            $message_type = 'success';
        } else {
            $message = "Maaf, terjadi kesalahan saat mengunggah file Anda.";
            $message_type = 'error';
        }
    }
} else {
    // Jika halaman diakses langsung tanpa POST
    header("Location: index.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Unggahan</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <h2>Status Unggahan</h2>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
            <div class="nav-links">
                <a href="index.html" class="button">Unggah File Lain</a>
                <a href="list.php" class="button">Lihat Daftar File</a>
            </div>
        </div>
    </div>
</body>
</html>