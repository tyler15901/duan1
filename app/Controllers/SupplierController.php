<?php
class SupplierController extends Controller {

    // Danh sách NCC
    public function index() {
        $model = $this->model('SupplierModel');
        $suppliers = $model->getAllSuppliers();
        $this->view('admin/suppliers/index', ['suppliers' => $suppliers]);
    }

    // Form thêm mới
    public function create() {
        $this->view('admin/suppliers/create');
    }

    // Xử lý lưu NCC
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $model = $this->model('SupplierModel');
            $data = [
                'ten' => $_POST['ten_ncc'],
                'loai' => $_POST['loai_cc'],
                'diachi' => $_POST['dia_chi'],
                'sdt' => $_POST['sdt']
            ];
            if ($model->createSupplier($data)) {
                header("Location: " . BASE_URL . "/supplier/index");
            }
        }
    }

    // Xem chi tiết & Quản lý tài nguyên
    public function show($id) {
        $model = $this->model('SupplierModel');
        $data = [
            'supplier' => $model->getSupplierById($id),
            'resources' => $model->getResourcesBySupplier($id)
        ];
        $this->view('admin/suppliers/show', $data);
    }

    // Form sửa
    public function edit($id) {
        $model = $this->model('SupplierModel');
        $data = ['supplier' => $model->getSupplierById($id)];
        $this->view('admin/suppliers/edit', $data);
    }

    // Xử lý cập nhật
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $model = $this->model('SupplierModel');
            $data = [
                'ten' => $_POST['ten_ncc'],
                'loai' => $_POST['loai_cc'],
                'diachi' => $_POST['dia_chi'],
                'sdt' => $_POST['sdt'],
                'trangthai' => $_POST['trang_thai']
            ];
            if ($model->updateSupplier($id, $data)) {
                header("Location: " . BASE_URL . "/supplier/show/" . $id);
            }
        }
    }

    // Xóa NCC
    public function delete($id) {
        $model = $this->model('SupplierModel');
        $model->deleteSupplier($id);
        header("Location: " . BASE_URL . "/supplier/index");
    }

    // --- ACTION PHỤ: THÊM TÀI NGUYÊN NHANH ---
    public function store_resource($supplierId) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $model = $this->model('SupplierModel');
            $name = $_POST['ten_tai_nguyen'];
            $capacity = $_POST['so_luong_cho']; // VD: 45 chỗ
            $note = $_POST['ghi_chu'];
            
            $model->addResource($supplierId, $name, $capacity, $note);
            // Quay lại trang chi tiết
            header("Location: " . BASE_URL . "/supplier/show/" . $supplierId);
        }
    }

    public function delete_resource($id, $supplierId) {
        $model = $this->model('SupplierModel');
        $model->deleteResource($id);
        header("Location: " . BASE_URL . "/supplier/show/" . $supplierId);
    }
}
?>