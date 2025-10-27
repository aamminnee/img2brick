<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

class CropImageController {
    private string $uploadDir;

    public function __construct() {
        $this->uploadDir = __DIR__ . '/../uploads/';
    }

    public function handleCropUpload(): void {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['cropped_image'])) {
            $this->respond('error', 'No image received.');
            return;
        }

        $originalName = $_POST['original_name'] ?? 'image.png';
        $newName = 'cropped_' . basename($originalName);
        $targetPath = $this->uploadDir . $newName;

        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }

        if (move_uploaded_file($_FILES['cropped_image']['tmp_name'], $targetPath)) {
            $this->respond('success', 'Cropped image saved successfully.', $newName);
        } else {
            $this->respond('error', 'Failed to save cropped image.');
        }
    }

    private function respond(string $status, string $message, string $file = ''): void {
        echo json_encode([
            'status' => $status,
            'message' => $message,
            'file' => $file
        ]);
        exit;
    }
}

$controller = new CropImageController();
$controller->handleCropUpload();
?>  