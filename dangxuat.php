<?php
session_start();
session_unset(); // Xóa sạch các biến trong session (id, username, vaitro)
session_destroy(); // Hủy hoàn toàn phiên làm việc
header("Location: index.php"); // Quay lại trang chủ
exit();
?>