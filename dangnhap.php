<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'includes/header.php';
require_once 'includes/connect.php'; 

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['tendangnhap']);
    $password = trim($_POST['matkhau']);

    if (empty($username) || empty($password)) {
        $error = "Vui lòng nhập đầy đủ thông tin!";
    } else {
        // 1. Tìm tài khoản dựa trên tên đăng nhập bằng MySQLi
        $stmt = $conn->prepare("SELECT * FROM taikhoan WHERE tendangnhap = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        
        // Lấy kết quả trả về
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Chuyển kết quả thành mảng dữ liệu (Associative Array)
            $user = $result->fetch_assoc();
            
            // 2. Xác thực mật khẩu
            if (password_verify($password, $user['matkhau'])) {
                // Đăng nhập thành công -> Lưu dữ liệu vào SESSION
                $_SESSION['id'] = $user['id'];
                $_SESSION['username'] = $user['tendangnhap'];
                $_SESSION['vaitro'] = $user['vaitro']; // 0: Khách hàng, 1: Admin

                // 3. Phân quyền hướng đi
                if ($user['vaitro'] == 1) {
                    header("Location: admin/index.php"); // Chuyển thẳng tới trang Admin
                } else {
                    header("Location: index.php"); // Khách hàng về trang chủ
                }
                exit();
            } else {
                $error = "Mật khẩu không chính xác!";
            }
        } else {
            $error = "Tên đăng nhập không tồn tại!";
        }
        $stmt->close();
    }
}
?>

<h2>ĐĂNG NHẬP</h2>

<?php if ($error): ?> <div class="error"><?php echo $error; ?></div> <?php endif; ?>

<form action="" method="POST" style="max-width: 400px;">
    <div class="form-group">
        <label for="tendangnhap">Tên đăng nhập:</label>
        <input type="text" id="tendangnhap" name="tendangnhap" required>
    </div>
    <div class="form-group">
        <label for="matkhau">Mật khẩu:</label>
        <input type="password" id="matkhau" name="matkhau" required>
    </div>
    <button type="submit" class="btn">Đăng nhập</button>
</form>

<?php include 'includes/footer.php'; ?>