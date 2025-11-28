<h2 class="mb-4">Báo cáo Doanh thu & Lợi nhuận</h2>

<div class="card mb-4 shadow-sm">
    <div class="card-header bg-white fw-bold">
        <i class="bi bi-graph-up"></i> Biểu đồ tăng trưởng (12 tháng gần nhất)
    </div>
    <div class="card-body">
        <canvas id="revenueChart" style="max-height: 400px;"></canvas>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-bold"><i class="bi bi-table"></i> Hiệu quả kinh doanh chi tiết</span>
        
        <form class="d-flex gap-2" method="GET">
            <select name="month" class="form-select form-select-sm" style="width: 100px;">
                <?php for($i=1; $i<=12; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php echo ($i==$current_month)?'selected':''; ?>>Tháng <?php echo $i; ?></option>
                <?php endfor; ?>
            </select>
            <select name="year" class="form-select form-select-sm" style="width: 100px;">
                <option value="2024" <?php echo ($current_year==2024)?'selected':''; ?>>2024</option>
                <option value="2025" <?php echo ($current_year==2025)?'selected':''; ?>>2025</option>
            </select>
            <button type="submit" class="btn btn-sm btn-primary">Xem</button>
        </form>
    </div>
    <div class="card-body p-0">
        <table class="table table-bordered table-hover mb-0">
            <thead class="table-light text-center">
                <tr>
                    <th>Lịch Code</th>
                    <th>Tên Tour</th>
                    <th>Ngày đi</th>
                    <th>Doanh thu</th>
                    <th>Chi phí</th>
                    <th>Lợi nhuận</th>
                    <th>Hiệu quả</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($profit_list)): ?>
                    <tr><td colspan="7" class="text-center py-4">Không có dữ liệu trong tháng này.</td></tr>
                <?php else: ?>
                    <?php foreach($profit_list as $row): ?>
                    <tr>
                        <td class="fw-bold"><?php echo $row['LichCode']; ?></td>
                        <td><?php echo $row['TenTour']; ?></td>
                        <td class="text-center"><?php echo date('d/m', strtotime($row['NgayKhoiHanh'])); ?></td>
                        <td class="text-end text-success"><?php echo number_format($row['DoanhThu']); ?></td>
                        <td class="text-end text-danger"><?php echo number_format($row['ChiPhi']); ?></td>
                        <td class="text-end fw-bold <?php echo ($row['LoiNhuan']>=0)?'text-primary':'text-danger'; ?>">
                            <?php echo number_format($row['LoiNhuan']); ?>
                        </td>
                        <td class="text-center">
                            <?php 
                                // Tính tỷ suất lợi nhuận = Lợi nhuận / Doanh thu
                                $rate = ($row['DoanhThu'] > 0) ? round(($row['LoiNhuan'] / $row['DoanhThu']) * 100, 1) : 0;
                            ?>
                            <span class="badge <?php echo ($rate >= 20) ? 'bg-success' : (($rate > 0) ? 'bg-warning text-dark' : 'bg-danger'); ?>">
                                <?php echo $rate; ?>%
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo $chart_labels; ?>, // Dữ liệu từ PHP
            datasets: [
                {
                    label: 'Doanh thu',
                    data: <?php echo $chart_revenue; ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Lợi nhuận',
                    data: <?php echo $chart_profit; ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                    type: 'line', // Vẽ đường lợi nhuận đè lên cột doanh thu
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>