<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['vaitro']) || $_SESSION['vaitro'] != 1) { header("Location: ../dangnhap.php"); exit(); }

require_once '../includes/connect.php';

// Lấy danh sách Loài và Danh mục để hiển thị ra Form chọn
$loai_result = $conn->query("SELECT * FROM loai_thucung");
$danhmuc_result = $conn->query("SELECT * FROM danhmuc");

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tensanpham = trim($_POST['tensanpham']);
    $giaban = intval($_POST['giaban']);
    $soluong = intval($_POST['soluong']);
    $mota = trim($_POST['mota']);
    $loai_id = intval($_POST['loai_thucung_id']);
    $danhmuc_id = intval($_POST['danhmuc_id']);
    
    // Xử lý Upload Ảnh
    $hinhanh = $_FILES['hinhanh']['name'];
    $target_dir = "../images/";
    $target_file = $target_dir . basename($hinhanh);

    if (empty($tensanpham) || $giaban <= 0 || empty($hinhanh)) {
        $error = "Vui lòng nhập đầy đủ thông tin hợp lệ và chọn hình ảnh!";
    } else {
        // Tiến hành di chuyển file ảnh vừa upload vào thư mục images/
        if (move_uploaded_file($_FILES['hinhanh']['tmp_name'], $target_file)) {
            // Chèn dữ liệu mới vào bảng sanpham theo đúng thiết kế CSDL của bạn
            $stmt = $conn->prepare("INSERT INTO sanpham (tensanpham, giaban, hinhanh, soluong, mota, loai_thucung_id, danhmuc_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sisisii", $tensanpham, $giaban, $hinhanh, $soluong, $mota, $loai_id, $danhmuc_id);
            
            if ($stmt->execute()) {
                $success = "Thêm sản phẩm mới thành công! <a href='index.php'>Quay lại danh sách</a>";
            } else {
                $error = "Lỗi hệ thống không thể lưu sản phẩm!";
            }
            $stmt->close();
        } else {
            $error = "Không thể tải hình ảnh lên máy chủ!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm sản phẩm mới</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f4f4f4; }
        .form-container { max-width: 600px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); margin: auto; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        .btn-submit { background: #4CAF50; color: white; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; width: 100%; }
        .error { color: red; margin-bottom: 15px; }
        .success { color: green; margin-bottom: 15px; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>THÊM SẢN PHẨM MỚI</h2>
    
    <?php if ($error): ?> <div class="error"><?php echo $error; ?></div> <?php endif; ?>
    <?php if ($success): ?> <div class="success"><?php echo $success; ?></div> <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Tên sản phẩm:</label>
            <input type="text" name="tensanpham" required>
        </div>
        <div class="form-group">
            <label>Giá bán (đ):</label>
            <input type="number" name="giaban" required>
        </div>
        <div class="form-group">
            <label>Số lượng kho:</label>
            <input type="number" name="soluong" value="0">
        </div>
        <div class="form-group">
            <label>Hình ảnh sản phẩm:</label>
            <input type="file" name="hinhanh" accept="image/*" required>
        </div>
        <div class="form-group">
            <label>Loài thú cưng:</label>
            <select name="loai_thucung_id">
                <?php while($loai = $loai_result->fetch_assoc()): ?>
                    <option value="<?php echo $loai['id']; ?>"><?php echo htmlspecialchars($loai['ten_loai']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Danh mục sản phẩm:</label>
            <select name="danhmuc_id">
                <?php while($dm = $danhmuc_result->fetch_assoc()): ?>
                    <option value="<?php echo $dm['id']; ?>"><?php echo htmlspecialchars($dm['ten_danhmuc']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Mô tả chi tiết:</label>
            <textarea name="mota" rows="4"></textarea>
        </div>
        
        <button type="submit" class="btn-submit">Lưu sản phẩm</button>
        <p style="text-align: center;"><a href="index.php">Quay lại trang quản lý</a></p>
    </form>
</div>

</body>
</html>