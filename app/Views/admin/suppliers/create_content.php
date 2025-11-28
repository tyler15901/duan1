<h2>Thêm Nhà Cung Cấp Mới</h2>
<form action="<?php echo BASE_URL; ?>/supplier/store" method="POST" class="card p-4">
    <div class="mb-3">
        <label>Tên Nhà cung cấp (*)</label>
        <input type="text" name="ten_ncc" class="form-control" required placeholder="VD: Nhà xe Thành Bưởi">
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label>Loại hình</label>
            <select name="loai_cc" class="form-select">
                <option value="Vận chuyển">Vận chuyển (Xe)</option>
                <option value="Lưu trú">Lưu trú (Khách sạn)</option>
                <option value="Ăn uống">Ăn uống (Nhà hàng)</option>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label>Số điện thoại</label>
            <input type="text" name="sdt" class="form-control">
        </div>
    </div>
    <div class="mb-3">
        <label>Địa chỉ</label>
        <input type="text" name="dia_chi" class="form-control">
    </div>
    <button type="submit" class="btn btn-success">Lưu thông tin</button>
</form>