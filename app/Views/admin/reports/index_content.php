<?php
// 1. LOGIC: Tính toán tổng (Giữ nguyên logic của bạn)
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

<div class="pagetitle">
    <h1>Báo cáo Tài chính</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item active">Báo cáo</li>
        </ol>
    </nav>
</div>

<section class="section dashboard">
    <div class="card mb-4 d-print-none">
        <div class="card-body py-3 d-flex justify-content-between align-items-center">
            <form class="d-flex align-items-center gap-2" method="GET">
                <span class="fw-bold text-muted small"><i class="bi bi-funnel-fill"></i> Kỳ báo cáo:</span>
                
                <select name="month" class="form-select form-select-sm" style="width: 120px;">
                    <?php for ($i = 1; $i <= 12; $i++): ?>
                        <option value="<?php echo $i; ?>" <?php echo ($i == $current_month) ? 'selected' : ''; ?>>
                            Tháng <?php echo $i; ?>
                        </option>
                    <?php endfor; ?>
                </select>
                
                <select name="year" class="form-select form-select-sm" style="width: 100px;">
                    <option value="2024" <?php echo ($current_year == 2024) ? 'selected' : ''; ?>>2024</option>
                    <option value="2025" <?php echo ($current_year == 2025) ? 'selected' : ''; ?>>2025</option>
                </select>
                
                <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search"></i> Xem</button>
            </form>

            <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-printer-fill me-1"></i> In Báo cáo
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-xxl-4 col-md-4">
            <div class="card info-card revenue-card">
                <div class="card-body">
                    <h5 class="card-title">Doanh Thu <span>| Tháng <?php echo $current_month; ?></span></h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                        <div class="ps-3">
                            <h6><?php echo number_format($total_revenue); ?></h6>
                            <span class="text-success small pt-1 fw-bold">VNĐ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-4 col-md-4">
            <div class="card info-card customers-card"> 
                <style>.expense-icon { background: #ffe0e3 !important; color: #dc3545 !important; }</style>
                <div class="card-body">
                    <h5 class="card-title">Chi Phí <span>| Vận hành</span></h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon expense-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-cart-dash"></i>
                        </div>
                        <div class="ps-3">
                            <h6><?php echo number_format($total_expense); ?></h6>
                            <span class="text-danger small pt-1 fw-bold">VNĐ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-4 col-md-4">
            <div class="card info-card sales-card">
                <div class="card-body">
                    <h5 class="card-title">Lợi Nhuận Ròng <span>| Thực tế</span></h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <div class="ps-3">
                            <h6><?php echo number_format($total_profit); ?></h6>
                            <span class="text-primary small pt-1 fw-bold">VNĐ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 d-print-none">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Biểu đồ Lợi nhuận <span>/ Năm <?php echo $current_year; ?></span></h5>
                    <div id="profitChart"></div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Chi tiết từng đoàn khởi hành</h5>
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="ps-3">Mã Lịch</th>
                                    <th scope="col">Thông tin Tour</th>
                                    <th scope="col" class="text-end">Doanh thu</th>
                                    <th scope="col" class="text-end">Chi phí</th>
                                    <th scope="col" class="text-end">Lợi nhuận</th>
                                    <th scope="col" class="text-center">Tỷ suất</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($profit_list)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="bi bi-folder2-open fs-1 opacity-25"></i>
                                            <p class="mt-2 mb-0">Không có dữ liệu trong kỳ báo cáo này.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($profit_list as $row): ?>
                                        <?php
                                        // Tính margin
                                        $margin = ($row['DoanhThu'] > 0) ? round(($row['LoiNhuan'] / $row['DoanhThu']) * 100, 1) : 0;
                                        // Badge color logic
                                        $badgeClass = 'bg-secondary';
                                        if ($margin >= 20) $badgeClass = 'bg-success';
                                        elseif ($margin > 0) $badgeClass = 'bg-warning text-dark';
                                        elseif ($margin <= 0) $badgeClass = 'bg-danger';
                                        ?>
                                        <tr>
                                            <td class="ps-3 fw-bold text-primary font-monospace"><?php echo $row['LichCode']; ?></td>
                                            <td>
                                                <div class="fw-bold text-dark"><?php echo $row['TenTour']; ?></div>
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar-event me-1"></i> <?php echo date('d/m/Y', strtotime($row['NgayKhoiHanh'])); ?>
                                                </small>
                                            </td>
                                            <td class="text-end"><?php echo number_format($row['DoanhThu']); ?></td>
                                            <td class="text-end text-danger"><?php echo number_format($row['ChiPhi']); ?></td>
                                            <td class="text-end fw-bold text-success"><?php echo number_format($row['LoiNhuan']); ?></td>
                                            <td class="text-center">
                                                <span class="badge <?php echo $badgeClass; ?>">
                                                    <?php echo $margin; ?>%
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <?php if (!empty($profit_list)): ?>
                            <tfoot class="bg-light fw-bold">
                                <tr>
                                    <td colspan="2" class="text-end text-uppercase text-muted ps-3">Tổng cộng:</td>
                                    <td class="text-end text-primary"><?php echo number_format($total_revenue); ?></td>
                                    <td class="text-end text-danger"><?php echo number_format($total_expense); ?></td>
                                    <td class="text-end text-success"><?php echo number_format($total_profit); ?></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    @media print {
        .d-print-none, .header, .sidebar, .pagetitle nav { display: none !important; }
        .pagetitle h1 { font-size: 24pt; text-align: center; margin-bottom: 20px; }
        .card { border: 1px solid #ddd !important; box-shadow: none !important; margin-bottom: 20px; }
        .card-title { color: #000 !important; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .main { margin: 0 !important; padding: 0 !important; }
        body { background: #fff !important; }
    }
</style>

<?php if (!empty(json_decode($chart_labels))): ?>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const profitData = <?php echo $chart_profit; ?>;
        const labels = <?php echo $chart_labels; ?>;

        new ApexCharts(document.querySelector("#profitChart"), {
            series: [{
                name: 'Lợi nhuận',
                data: profitData
            }],
            chart: {
                height: 350,
                type: 'bar',
                toolbar: { show: false }
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    columnWidth: '45%',
                    distributed: true // Mỗi cột 1 màu cho đẹp
                }
            },
            colors: ['#4154f1', '#2eca6a', '#ff771d', '#e0f8e9', '#ffecdf', '#4154f1', '#2eca6a', '#ff771d'],
            dataLabels: { enabled: false },
            legend: { show: false },
            xaxis: {
                categories: labels,
                labels: { style: { fontSize: '12px' } }
            },
            yaxis: {
                labels: {
                    formatter: function (value) {
                        return new Intl.NumberFormat('vi-VN').format(value) + " đ";
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val);
                    }
                }
            }
        }).render();
    });
</script>
<?php endif; ?>