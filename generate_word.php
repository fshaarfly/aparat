<?php
require 'phpoffice/vendor/autoload.php'; // Autoload untuk PHPWord

use PhpOffice\PhpWord\TemplateProcessor;

// Pastikan ID dikirim melalui GET
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    // Koneksi ke database dan ambil data berdasarkan ID
    include 'database.php';

    $query = "SELECT * FROM surat WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $formattedDate = date("d/m/Y", strtotime($data['tanggal_buat']));
        $jenisSurat = $data['jenis_surat'];

        // Path ke template Word
        $outputPath = $jenisSurat . '_' . $data['nama'] . '_' . $data['tanggal_buat'] . '.docx';

        switch ($jenisSurat) {
            case 'SKM':
                $templatePath = 'templates/SKM.docx';
                break;
            case 'SCA':
                $templatePath = 'templates/SCA.docx';
                break;
            case 'TAS':
                $templatePath = 'templates/TAS.docx';
                break;
            case 'LKA':
                $templatePath = 'templates/LKA.docx';
                break;
            default:
                echo "Jenis surat tidak dikenali!";
                exit;
        }

        // Pastikan template file ada
        if (!file_exists($templatePath)) {
            echo "Template surat tidak ditemukan!";
            exit;
        }

        // Proses template Word
        $templateProcessor = new TemplateProcessor($templatePath);

        // Ganti placeholder dengan data dari database
        $templateProcessor->setValue('nama', htmlspecialchars($data['nama']));
        $templateProcessor->setValue('nim', htmlspecialchars($data['nim']));
        $templateProcessor->setValue('jurusan', htmlspecialchars($data['jurusan']));
        $templateProcessor->setValue('prodi', htmlspecialchars($data['prodi']));
        $templateProcessor->setValue('semester', htmlspecialchars($data['semester']));
        $templateProcessor->setValue('tahun_ajaran', htmlspecialchars($data['tahun_ajaran']));
        $templateProcessor->setValue('jenis_surat', htmlspecialchars($data['jenis_surat']));
        $templateProcessor->setValue('tanggal_buat', htmlspecialchars($formattedDate));

        // Simpan dokumen yang dihasilkan
        $templateProcessor->saveAs($outputPath);

        // Kirim file untuk diunduh
        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment; filename="' . basename($outputPath) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($outputPath));

        // Baca file dan kirim ke browser
        readfile($outputPath);

        // Hapus file setelah diunduh
        unlink($outputPath);
        exit;
    } else {
        echo "Surat dengan ID tersebut tidak ditemukan!";
        exit;
    }
} else {
    echo "ID surat tidak valid!";
    exit;
}
