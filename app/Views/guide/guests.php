<div class="pagetitle">
    <h1>Danh sách Đoàn khách</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/guide/index">Dashboard</a></li>
            <li class="breadcrumb-item active"><?php echo $schedule['LichCode']; ?></li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            
            <div class="card mb-3 shadow-sm border-start border-4 border-info">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-primary fw-bold mb-1"><?php echo $schedule['TenTour']; ?></h6>
                        <div class="small text-muted">
                            <i class="bi bi-calendar3 me-1"></i> <?php echo date('d/m/Y', strtotime($schedule['NgayKhoiHanh'])); ?> 
                            - <?php echo date('d/m/Y', strtotime($schedule['NgayKetThuc'])); ?>
                        </div>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/guide/detail/<?php echo $scheduleId; ?>" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-map me-1"></i> Xem Lịch trình
                    </a>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title m-0">Thành viên đoàn <span>| <?php echo count($guests); ?> pax</span></h5>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-striped">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th scope="col" class="ps-3">#</th>
                                    <th scope="col">Họ và tên</th>
                                    <th scope="col">Liên hệ</th>
                                    <th scope="col">Giấy tờ (CCCD/PP)</th>
                                    <th scope="col">Ghi chú</th>
                                    <th scope="col" class="text-end">Tác vụ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($guests)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="bi bi-people fs-1 opacity-25"></i>
                                            <p class="mt-2">Chưa có dữ liệu khách hàng.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($guests as $index => $g): ?>
                                        <tr>
                                            <td class="ps-3 fw-bold text-secondary"><?php echo $index + 1; ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center me-3 flex-shrink-0" 
                                                         style="width: 40px; height: 40px;">
                                                        <?php echo substr($g['HoTen'], 0, 1); ?>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark"><?php echo $g['HoTen']; ?></div>
                                                        <div class="small text-muted">
                                                            <span class="badge bg-light text-secondary border"><?php echo $g['GioiTinh'] ?? 'Khách'; ?></span>
                                                            <span class="ms-1" style="font-size: 11px;">Vé: <?php echo $g['MaBookingCode']; ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if(!empty($g['SoDienThoai'])): ?>
                                                    <a href="tel:<?php echo $g['SoDienThoai']; ?>" class="text-decoration-none fw-bold text-dark">
                                                        <i class="bi bi-telephone-fill text-success me-1"></i> <?php echo $g['SoDienThoai']; ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted small">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="font-monospace text-secondary">
                                                    <?php echo !empty($g['SoGiayTo']) ? $g['SoGiayTo'] : '<i class="text-muted small">Chưa cập nhật</i>'; ?>
                                                </span>
                                            </td>
                                            
                                            <td>
                                                <div class="d-flex flex-column gap-1">
                                                    <?php if (!empty($g['GhiChu'])): ?>
                                                        <div class="small text-dark">
                                                            <i class="bi bi-pencil-fill text-muted me-1" style="font-size: 10px;"></i> 
                                                            <?php echo $g['GhiChu']; ?>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if (!empty($g['YeuCauDacBiet'])): ?>
                                                        <div>
                                                            <span class="badge bg-warning text-dark border border-warning text-wrap text-start">
                                                                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                                                <?php echo $g['YeuCauDacBiet']; ?>
                                                            </span>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if (empty($g['GhiChu']) && empty($g['YeuCauDacBiet'])): ?>
                                                        <span class="text-muted small">-</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>

                                            <td class="text-end">
                                                <button class="btn btn-sm btn-light text-primary border"
                                                    onclick="openNoteModal(<?php echo $g['MaChiTiet']; ?>, '<?php echo $g['HoTen']; ?>', '<?php echo htmlspecialchars($g['YeuCauDacBiet'] ?? '', ENT_QUOTES); ?>')">
                                                    <i class="bi bi-journal-plus me-1"></i> Note
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="noteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?php echo BASE_URL; ?>/guide/update_request" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Cập nhật lưu ý: <span id="guestName" class="text-primary fw-bold"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="schedule_id" value="<?php echo $scheduleId; ?>">
                    <input type="hidden" name="guest_id" id="guestIdInput">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small">GHI CHÚ PHÁT SINH / SỨC KHỎE</label>
                        <textarea name="content" id="noteContent" class="form-control" rows="4" placeholder="VD: Khách bị say xe, cần đổi phòng tầng thấp..."></textarea>
                        <div class="form-text small">Ghi chú này sẽ được lưu vào phần "Yêu cầu đặc biệt".</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu lại</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openNoteModal(id, name, content) {
        document.getElementById('guestIdInput').value = id;
        document.getElementById('guestName').innerText = name;
        // Xử lý ký tự đặc biệt
        const txt = document.createElement("textarea");
        txt.innerHTML = content;
        document.getElementById('noteContent').value = txt.value || '';
        
        new bootstrap.Modal(document.getElementById('noteModal')).show();
    }
</script>