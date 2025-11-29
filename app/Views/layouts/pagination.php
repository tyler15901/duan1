<?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
<nav aria-label="Page navigation" class="mt-4">
    <ul class="pagination justify-content-center">
        <?php
            // 1. Giữ lại các tham số URL hiện tại (tìm kiếm, lọc...)
            $queryParams = $_GET;
            unset($queryParams['url']); // Bỏ tham số router của MVC
        ?>

        <li class="page-item <?php echo ($pagination['current_page'] <= 1) ? 'disabled' : ''; ?>">
            <?php 
                $queryParams['page'] = $pagination['current_page'] - 1;
                $prevLink = '?' . http_build_query($queryParams);
            ?>
            <a class="page-link" href="<?php echo $prevLink; ?>">
                <i class="bi bi-chevron-left"></i>
            </a>
        </li>

        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
            <li class="page-item <?php echo ($i == $pagination['current_page']) ? 'active' : ''; ?>">
                <?php 
                    $queryParams['page'] = $i;
                    $pageLink = '?' . http_build_query($queryParams);
                ?>
                <a class="page-link" href="<?php echo $pageLink; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>

        <li class="page-item <?php echo ($pagination['current_page'] >= $pagination['total_pages']) ? 'disabled' : ''; ?>">
            <?php 
                $queryParams['page'] = $pagination['current_page'] + 1;
                $nextLink = '?' . http_build_query($queryParams);
            ?>
            <a class="page-link" href="<?php echo $nextLink; ?>">
                <i class="bi bi-chevron-right"></i>
            </a>
        </li>
    </ul>
</nav>
<?php endif; ?>