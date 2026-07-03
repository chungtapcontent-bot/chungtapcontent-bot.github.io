<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'includes/header.php';
require_once 'includes/connect.php'; // Đảm bảo file này khởi tạo kết nối MySQLi thông qua biến $conn

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['tendangnhap']);
    $password = trim($_POST['matkhau']);

    if (empty($username) || empty($password)) {
        $error = "Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu!";
    } else {
        // 1. Kiểm tra xem tên đăng nhập đã bị trùng chưa
        $stmt = $conn->prepare("SELECT id FROM taikhoan WHERE tendangnhap = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result(); // Lưu kết quả tạm thời để đếm số dòng
        
        if ($stmt->num_rows > 0) {
            $error = "Tên đăng nhập này đã tồn tại!";
            $stmt->close();
        } else {
            $stmt->close(); // Đóng câu lệnh kiểm tra trùng
            
            // 2. Mã hóa mật khẩu bảo mật (băm bcrypt)
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // 3. Chèn vào CSDL (vaitro mặc định là 0 theo thiết kế của bạn)
            $stmt_insert = $conn->prepare("INSERT INTO taikhoan (tendangnhap, matkhau, vaitro) VALUES (?, ?, 0)");
            $stmt_insert->bind_param("ss", $username, $hashed_password);
            
            if ($stmt_insert->execute()) {
                $success = "Đăng ký tài khoản thành công! <a href='dangnhap.php'>Đăng nhập ngay</a>";
            } else {
                $error = "Đã xảy ra lỗi hệ thống, vui lòng thử lại!";
            }
            $stmt_insert->close();
        }
    }
}
?>

<h2>ĐĂNG KÝ TÀI KHOẢN</h2>

<?php if ($error): ?> <div class="error"><?php echo $error; ?></div> <?php endif; ?>
<?php if ($success): ?> <div class="success"><?php echo $success; ?></div> <?php endif; ?>

<form action="" method="POST" style="max-width: 400px;">
    <div class="form-group">
        <label for="tendangnhap">Tên đăng nhập:</label>
        <input type="text" id="tendangnhap" name="tendangnhap" required>
    </div>
    <div class="form-group">
        <label for="matkhau">Mật khẩu:</label>
        <input type="password" id="matkhau" name="matkhau" required>
    </div>
    <button type="submit" class="btn">Đăng ký</button>
</form>

<?php include 'includes/footer.php'; ?>