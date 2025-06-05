<?php
require_once "../includes/db.php";
require_once "../includes/functions.php";
require_once "../includes/config.php";
require_once "../includes/header.php";
require __DIR__ . '/../vendor/autoload.php';
use Smalot\PdfParser\Parser;
use GuzzleHttp\Client;
redirect_if_not_logged_in();
if (!is_candidate()) { header("Location: /recruiter/dashboard.php"); exit; }

$error = $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['cv_file'])) {
    $file = $_FILES['cv_file'];
    if ($file['error'] == 0 && strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) === 'pdf') {
        $target = "../uploads/" . uniqid() . "_" . basename($file['name']);
        if (move_uploaded_file($file['tmp_name'], $target)) {
            // Extract text
            $parser = new Parser();
            $pdf = $parser->parseFile($target);
            $cv_text = $pdf->getText();

            // Gọi OpenAI API
            $prompt = "Đây là CV:\n$cv_text\n\nHãy đánh giá (1) điểm mạnh, (2) điểm yếu, (3) điểm số CV (0-100), (4) gợi ý cải thiện, (5) gợi ý vị trí phù hợp, trình bày từng mục rõ ràng.";
            $client = new Client();
            $response = $client->post('https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . OPENAI_API_KEY,
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'max_tokens' => 1000,
                    'temperature' => 0.5
                ]
            ]);
            $result = json_decode($response->getBody(), true);
            $ai_feedback = $result['choices'][0]['message']['content'];

            // Lưu xuống DB
            $stmt = $pdo->prepare("INSERT INTO cvs (user_id, file_path, content, ai_feedback) VALUES (?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $target, $cv_text, $ai_feedback]);

            $success = "Đã phân tích CV và lưu kết quả.";
        } else {
            $error = "Lỗi lưu file!";
        }
    } else {
        $error = "Chỉ nhận file PDF.";
    }
}
?>
<h3>Đánh giá CV cá nhân</h3>
<?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
<?php if($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
<form method="post" enctype="multipart/form-data">
    <div class="mb-3">
        <input type="file" name="cv_file" accept=".pdf" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Phân tích CV</button>
</form>
<?php require_once "../includes/footer.php"; ?>