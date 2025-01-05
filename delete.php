<?php
require_once 'database.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $deleteId = intval($_GET['id']);
    $deleteQuery = "DELETE FROM surat WHERE id = ?";
    $stmt = $db->prepare($deleteQuery);

    if ($stmt) {
        $stmt->bind_param("i", $deleteId);
        if ($stmt->execute()) {
            $hapus_script = "
            <script>
                Swal.fire({
                    title: 'Deleted!',
                    text: 'Data berhasil dihapus.',
                    icon: 'success',
                    confirmButtonColor: '#003289'
                }).then(() => {
                    window.location.href = 'riwayat.php';
                });
            </script>
            ";
        } 
    }
}
?>
