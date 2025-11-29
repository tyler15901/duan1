<?php
// 1. LOGIC: Tính toán tổng trước để hiển thị Widget ở trên đầu
$total_revenue = 0;
$total_expense = 0;
$total_profit = 0;

if (!empty($profit_list)) {
    foreach ($profit_list as $p) {
        $total_revenue += $p['DoanhThu'];
        $total_expense += $p['ChiPhi'];
        $total_profit += $p['LoiNhuan'];
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
        <h2 class="text-primary fw-bold border-start border-4 border-primary ps-3 mb-0">Báo cáo Tài chính</h2>
        <p class="text-muted small ps-3 mb-0 mt-1">Phân tích hiệu quả kinh doanh theo thời gian thực.</p>
    </div>
    <button onclick="window.print()" class="btn btn-secondary shadow-sm">
        <i class="bi bi-printer"></i> In Báo cáo
    </button>
</div>

<div class="card shadow-sm border-0 mb-4 d-print-none">
    <div class="card-body py-3">
        <form class="row g-2 align-items-center justify-content-end" method="GET">
            <div class="col-auto">
                <span class="fw-bold text-muted small me-2"><i class="bi bi-funnel"></i> Kỳ báo cáo:</span>
            </div>
            <div class="col-auto">
                <select name="month" class="form-select form-select-sm fw-bold border-secondary text-secondary">
                    <?php for ($i = 1; $i <= 12; $i++): ?>
                        <option value="<?php echo $i; ?>" <?php echo ($i == $current_month) ? 'selected' : ''; ?>>Tháng
                            <?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-auto">
                <select name="year" class="form-select form-select-sm fw-bold border-secondary text-secondary">
                    <option value="2024" <?php echo ($current_year == 2024) ? 'selected' : ''; ?>>Năm 2024</option>
                    <option value="2025" <?php echo ($current_year == 2025) ? 'selected' : ''; ?>>Năm 2025</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary px-3 fw-bold">Xem dữ liệu</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 bg-success bg-opacity-10 h-100">
            <div class="card-body">
                <div class="text-success text-uppercase small fw-bold mb-1">
                    Doanh thu (Tháng <?php echo $current_month; ?>)
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="text-success fw-bold mb-0"><?php echo number_format($total_revenue); ?></h3>
                    <i class="bi bi-graph-up-arrow fs-2 text-success opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 bg-danger bg-opacity-10 h-100">
            <div class="card-body">
                <div class="text-danger text-uppercase small fw-bold mb-1">Tổng Chi phí</div>
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="text-danger fw-bold mb-0"><?php echo number_format($total_expense); ?></h3>
                    <i class="bi bi-cart-x fs-2 text-danger opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 bg-primary text-white h-100">
            <div class="card-body">
                <div class="text-white-50 text-uppercase small fw-bold mb-1">Lợi nhuận ròng</div>
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="text-white fw-bold mb-0"><?php echo number_format($total_profit); ?></h3>
                    <i class="bi bi-wallet2 fs-2 text-white opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-12">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-dark"><i class="bi bi-table"></i> Chi tiết từng đoàn khởi hành</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary">
                        <tr class="small fw-bold text-uppercase">
                            <th class="ps-3">Mã Lịch</th>
                            <th>Thông tin Tour</th>
                            <th class="text-end">Doanh thu</th>
                            <th class="text-end">Chi phí</th>
                            <th class="text-end">Lợi nhuận</th>
                            <th class="text-center">Tỷ suất</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($profit_list)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">Không có dữ liệu trong kỳ này.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($profit_list as $row): ?>
                                <?php
                                // Tính margin
                                $margin = ($row['DoanhThu'] > 0) ? round(($row['LoiNhuan'] / $row['DoanhThu']) * 100, 1) : 0;
                                $marginClass = ($margin >= 20) ? 'success' : (($margin > 0) ? 'warning text-dark' : 'danger');
                                ?>
                                <tr>
                                    <td class="ps-3 fw-bold text-primary font-monospace"><?php echo $row['LichCode']; ?></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo $row['TenTour']; ?></div>
                                        <small class="text-muted"><i class="bi bi-calendar"></i>
                                            <?php echo date('d/m/Y', strtotime($row['NgayKhoiHanh'])); ?></small>
                                    </td>
                                    <td class="text-end"><?php echo number_format($row['DoanhThu']); ?></td>
                                    <td class="text-end text-danger"><?php echo number_format($row['ChiPhi']); ?></td>
                                    <td class="text-end fw-bold text-success"><?php echo number_format($row['LoiNhuan']); ?>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge bg-<?php echo $marginClass; ?> bg-opacity-25 border border-<?php echo $marginClass; ?> text-<?php echo str_replace(' text-dark', '', $marginClass); ?>">
                                            <?php echo $margin; ?>%
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="bg-light fw-bold border-top">
                        <tr>
                            <td colspan="2" class="text-end text-uppercase text-muted ps-3">Tổng cộng:</td>
                            <td class="text-end"><?php echo number_format($total_revenue); ?></td>
                            <td class="text-end text-danger"><?php echo number_format($total_expense); ?></td>
                            <td class="text-end text-primary"><?php echo number_format($total_profit); ?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-12 d-print-none">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-dark"><i class="bi bi-bar-chart-line"></i> Biểu đồ lợi nhuận năm
                    <?php echo $current_year; ?></h6>
            </div>
            <div class="card-body">
                <div style="height: 300px;">
                    <canvas id="reportChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .d-print-none {
            display: none !important;
        }

        .card {
            border: 1px solid #ddd !important;
            box-shadow: none !important;
        }

        .badge {
            border: 1px solid #000;
            color: #000 !important;
        }
    }
</style>
<?php if (!empty(json_decode($chart_labels))): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const ctxRep = document.getElementById('reportChart').getContext('2d');

            // Tạo gradient vàng cam
            let gradient = ctxRep.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(255, 193, 7, 0.8)');
            gradient.addColorStop(1, 'rgba(255, 193, 7, 0.1)');

            new Chart(ctxRep, {
                type: 'bar',
                data: {
                    labels: <?php echo $chart_labels; ?>, // Đảm bảo biến này là JSON string từ Controller
                    datasets: [{
                        label: 'Lợi nhuận (VNĐ)',
                        data: <?php echo $chart_profit; ?>, // Đảm bảo biến này là JSON string
                        backgroundColor: gradient,
                        borderColor: '#ffc107',
                        borderWidth: 1,
                        borderRadius: 5, // Bo tròn cột
                        barPercentage: 0.6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    let label = context.dataset.label || '';
                                    if (label) { label += ': '; }
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value) { return value.toLocaleString('vi-VN') + ' đ'; }
                            }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        });
    </script>
<?php else: ?>
    <div class="text-center py-5 text-muted">
        Chưa có dữ liệu biểu đồ cho năm này.
    </div>
<?php endif; ?>