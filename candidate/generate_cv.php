<?php
require_once "../includes/db.php";
require_once "../includes/functions.php";
require_once "../includes/config.php";
require_once "../includes/header.php";
require 'vendor/autoload.php';
use Dompdf\Dompdf;
use GuzzleHttp\Client;
redirect_if_not_logged_in();
if (!is_candidate()) { header("Location: /recruiter/dashboard.php"); exit; }

$cv_content = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $info = "Họ tên: {$_POST['name']}\nEmail: {$_POST['email']}\nHọc vấn: {$_POST['education']}\nKỹ năng: {$_POST['skills']}\nKinh nghiệm: {$_POST['experience']}";
    $prompt = "Dựa trên thông tin sau, hãy tạo một bản CV chuyên nghiệp, trình bày rõ ràng:\n$info";
    $client = new Client();
    $response = $client->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=' . GEMINI_API_KEY, [
    'headers' => [
        'Content-Type'  => 'application/json',
    ],
    'json' => [
        'contents' => [
            ['parts' => [['text' => $prompt]]]
        ]
    ]
]);
$result = json_decode($response->getBody(), true);
$cv_content = $result['candidates'][0]['content']['parts'][0]['text'];

    // Tạo PDF với dompdf
    $dompdf = new Dompdf();
    $dompdf->loadHtml(nl2br(htmlspecialchars($cv_content)));
    $dompdf->render();
    $pdfOutput = $dompdf->output();
    file_put_contents("../uploads/generated_cv_{$_SESSION['user_id']}.pdf", $pdfOutput);
}
?>
<h3>Tạo CV tự động bằng AI</h3>
<form method="post">
    <div class="mb-3"><input type="text" name="name" class="form-control" placeholder="Họ tên" required></div>
    <div class="mb-3"><input type="email" name="email" class="form-control" placeholder="Email" required></div>
    <div class="mb-3"><textarea name="education" class="form-control" placeholder="Học vấn" required></textarea></div>
    <div class="mb-3"><textarea name="skills" class="form-control" placeholder="Kỹ năng" required></textarea></div>
    <div class="mb-3"><textarea name="experience" class="form-control" placeholder="Kinh nghiệm" required></textarea></div>
    <button type="submit" class="btn btn-success">Tạo CV</button>
</form>
<?php if($cv_content): ?>
    <h4 class="mt-4">Bản CV AI:</h4>
    <pre><?= htmlspecialchars($cv_content) ?></pre>
    <a href="../uploads/generated_cv_<?= $_SESSION['user_id'] ?>.pdf" class="btn btn-primary" target="_blank">Tải PDF</a>
<?php endif; ?>
<?php require_once "../includes/footer.php"; ?>