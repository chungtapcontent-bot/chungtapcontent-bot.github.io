<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['vaitro']) || $_SESSION['vaitro'] != 1) { header("Location: ../dangnhap.php"); exit(); }

require_once '../includes/connect.php';

// Kiểm tra xem có truyền ID sản phẩm cần sửa không
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}
$id = intval($_GET['id']);

// Lấy thông tin hiện tại của sản phẩm đó
$stmt_get = $conn->prepare("SELECT * FROM sanpham WHERE id = ?");
$stmt_get->bind_param("i", $id);
$stmt_get->execute();
$product = $stmt_get->get_result()->fetch_assoc();
$stmt_get->close();

if (!$product) {
    die("Sản phẩm không tồn tại!");
}

// Lấy danh sách Loài và Danh mục cho select-box
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
    
    // Mặc định lấy lại tên ảnh cũ
    $hinhanh = $product['hinhanh']; 
    
    // Nếu Admin chọn tải lên một file ảnh mới
    if (!empty($_FILES['hinhanh']['name'])) {
        $hinhanh = $_FILES['hinhanh']['name'];
        $target_file = "../images/" . basename($hinhanh);
        move_uploaded_file($_FILES['hinhanh']['tmp_name'], $target_file);
    }

    if (empty($tensanpham) || $giaban <= 0) {
        $error = "Tên sản phẩm và Giá bán không được để trống!";
    } else {
        // Tiến hành cập nhật bằng câu lệnh UPDATE
        $stmt_update = $conn->prepare("UPDATE sanpham SET tensanpham=?, giaban=?, hinhanh=?, soluong=?, mota=?, loai_thucung_id=?, danhmuc_id=? WHERE id=?");
        $stmt_update->bind_param("sisisiii", $tensanpham, $giaban, $hinhanh, $soluong, $mota, $loai_id, $danhmuc_id, $id);
        
        if ($stmt_update->execute()) {
            $success = "Cập nhật sản phẩm thành công! <a href='index.php'>Quay lại danh sách</a>";
            // Làm mới lại dữ liệu hiển thị trên form
            $product['hinhanh'] = $hinhanh; 
        } else {
            $error = "Có lỗi xảy ra, không thể cập nhật dữ liệu!";
        }
        $stmt_update->close();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa sản phẩm</title>
    <link rel="stylesheet" href="style-giong-them-php.css"> <!-- Bạn dùng chung style với file them.php nhé -->
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f4f4f4; }
        .form-container { max-width: 600px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); margin: auto; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        .btn-submit { background: #2196F3; color: white; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; width: 100%; }
        .error { color: red; margin-bottom: 15px; }
        .success { color: green; margin-bottom: 15px; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>SỬA SẢN PHẨM (ID: <?php echo $product['id']; ?>)</h2>
    
    <?php if ($error): ?> <div class="error"><?php echo $error; ?></div> <?php endif; ?>
    <?php if ($success): ?> <div class="success"><?php echo $success; ?></div> <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Tên sản phẩm:</label>
            <input type="text" name="tensanpham" value="<?php echo htmlspecialchars($product['tensanpham']); ?>" required>
        </div>
        <div class="form-group">
            <label>Giá bán (đ):</label>
            <input type="number" name="giaban" value="<?php echo $product['giaban']; ?>" required>
        </div>
        <div class="form-group">
            <label>Số lượng kho:</label>
            <input type="number" name="soluong" value="<?php echo $product['soluong']; ?>">
        </div>
        <div class="form-group">
            <label>Hình ảnh hiện tại:</label><br>
            <img src="../images/<?php echo htmlspecialchars($product['hinhanh']); ?>" style="width: 100px; margin-bottom: 10px; border-radius: 4px;"><br>
            <label>Thay ảnh mới (để trống nếu muốn giữ nguyên ảnh cũ):</label>
            <input type="file" name="hinhanh" accept="image/*">
        </div>
        <div class="form-group">
            <label>Loài thú cưng:</label>
            <select name="loai_thucung_id">
                <?php while($loai = $loai_result->fetch_assoc()): ?>
                    <option value="<?php echo $loai['id']; ?>" <?php if($loai['id'] == $product['loai_thucung_id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($loai['ten_loai']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Danh mục sản phẩm:</label>
            <select name="danhmuc_id">
                <?php while($dm = $danhmuc_result->fetch_assoc()): ?>
                    <option value="<?php echo $dm['id']; ?>" <?php if($dm['id'] == $product['danhmuc_id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($dm['ten_danhmuc']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Mô tả chi tiết:</label>
            <textarea name="mota" rows="4"><?php echo htmlspecialchars($product['mota']); ?></textarea>
        </div>
        
        <button type="submit" class="btn-submit">Cập nhật thay đổi</button>
        <p style="text-align: center;"><a href="index.php">Quay lại trang quản lý</a></p>
    </form>
</div>

</body>
</html>