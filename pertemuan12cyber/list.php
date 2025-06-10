<?php
// Direktori tempat file disimpan
$target_dir = "uploads/";

// Pesan status untuk ditampilkan kepada pengguna
$status_msg = '';

// Logika untuk menangani penghapusan file
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_file'])) {
    $file_to_delete = basename($_POST['delete_file']);
    $file_path = $target_dir . $file_to_delete;

    if (file_exists($file_path) && strpos(realpath($file_path), realpath($target_dir)) === 0) {
        if (unlink($file_path)) {
            $status_msg = "<div class='message success'>File '" . htmlspecialchars($file_to_delete) . "' telah berhasil dihapus.</div>";
        } else {
            $status_msg = "<div class='message error'>Gagal menghapus file.</div>";
        }
    } else {
        $status_msg = "<div class='message error'>File tidak ditemukan atau tidak valid.</div>";
    }
}

// Fungsi untuk mendapatkan ikon generik
function getFileIcon($extension) {
    $icon_map = [
        'pdf' => 'https://img.icons8.com/color/48/000000/pdf.png',
        'doc' => 'https://img.icons8.com/color/48/000000/ms-word.png',
        'docx' => 'https://img.icons8.com/color/48/000000/microsoft-word-2019--v1.png',
        'zip' => 'https://img.icons8.com/color/48/000000/zip.png',
        'default' => 'https://img.icons8.com/color/48/000000/document.png'
    ];
    $ext = strtolower($extension);
    return isset($icon_map[$ext]) ? $icon_map[$ext] : $icon_map['default'];
}

// Fungsi untuk memformat ukuran file
function formatBytes($bytes, $precision = 2) { 
    $units = ['B', 'KB', 'MB', 'GB', 'TB']; 
    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 
    $bytes /= (1 << (10 * $pow)); 
    return round($bytes, $precision) . ' ' . $units[$pow]; 
} 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar File Terunggah</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <nav>
            <a href="index.html">Unggah File</a>
            <a href="list.php" class="active">Daftar File</a>
        </nav>
        <div class="card">
            <h2>Daftar File</h2>
            <?php echo $status_msg; ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Pratinjau</th>
                            <th>Nama File (Klik untuk Lihat)</th>
                            <th>Ukuran</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!is_dir($target_dir)) {
                            mkdir($target_dir, 0777, true);
                        }
                        
                        $files = array_diff(scandir($target_dir), array('.', '..'));

                        if (count($files) > 0) {
                            foreach($files as $file) {
                                $file_path = $target_dir . $file;
                                $file_ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
                                $file_size = filesize($file_path);
                                
                                // --- PERUBAHAN MULAI DI SINI ---
                                $icon_html = '';
                                // Daftar ekstensi file gambar yang ingin ditampilkan thumbnailnya
                                $image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];

                                if (in_array($file_ext, $image_extensions) && file_exists($file_path)) {
                                    // Jika file adalah gambar, tampilkan thumbnail
                                    $icon_html = "<img src='" . htmlspecialchars($file_path) . "' alt='Thumbnail' class='file-thumbnail'>";
                                } else {
                                    // Jika bukan gambar, tampilkan ikon generik
                                    $icon_src = getFileIcon($file_ext);
                                    $icon_html = "<img src='" . $icon_src . "' alt='" . $file_ext . "' class='file-icon'>";
                                }
                                // --- PERUBAHAN SELESAI DI SINI ---
                                
                                echo "<tr>";
                                echo "<td>" . $icon_html . "</td>"; // Menggunakan variabel hasil logika di atas
                                echo "<td><a href='" . htmlspecialchars($file_path) . "' target='_blank' class='file-view-link'>" . htmlspecialchars($file) . "</a></td>";
                                echo "<td>" . formatBytes($file_size) . "</td>";
                                echo "<td class='action-buttons'>";
                                echo "<a href='" . htmlspecialchars($file_path) . "' class='button download' download>Unduh</a>";
                                echo "<form method='post' action='list.php' style='display:inline;' onsubmit='return confirm(\"Apakah Anda yakin ingin menghapus file ini?\");'>";
                                echo "<input type='hidden' name='delete_file' value='" . htmlspecialchars($file) . "'>";
                                echo "<button type='submit' class='button delete'>Hapus</button>";
                                echo "</form>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>Belum ada file yang diunggah.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>