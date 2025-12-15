<?php
require_once 'functions.php';
requireLogin();

$reviews = readJson('reviews.json');
$message = '';
$messageType = '';
$pageTitle = 'Menaxho Reviews';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'approve') {
        $id = $_POST['id'] ?? '';
        foreach ($reviews['pending'] as $key => $review) {
            if ($review['id'] === $id) {
                $reviews['approved'][] = $review;
                unset($reviews['pending'][$key]);
                $reviews['pending'] = array_values($reviews['pending']);
                writeJson('reviews.json', $reviews);
                $message = 'Review wurde erfolgreich genehmigt! Änderungen werden in index.html übernommen.';
                $messageType = 'success';
                break;
            }
        }
        $reviews = readJson('reviews.json');
    } elseif ($action === 'reject') {
        $id = $_POST['id'] ?? '';
        foreach ($reviews['pending'] as $key => $review) {
            if ($review['id'] === $id) {
                unset($reviews['pending'][$key]);
                $reviews['pending'] = array_values($reviews['pending']);
                writeJson('reviews.json', $reviews);
                $message = 'Review wurde abgelehnt!';
                $messageType = 'success';
                break;
            }
        }
        $reviews = readJson('reviews.json');
    } elseif ($action === 'delete_approved') {
        $id = $_POST['id'] ?? '';
        foreach ($reviews['approved'] as $key => $review) {
            if ($review['id'] === $id) {
                unset($reviews['approved'][$key]);
                $reviews['approved'] = array_values($reviews['approved']);
                writeJson('reviews.json', $reviews);
                $message = 'Review wurde erfolgreich gelöscht! Änderungen werden in index.html übernommen.';
                $messageType = 'success';
                break;
            }
        }
        $reviews = readJson('reviews.json');
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Admin Panel</title>
    <link rel="stylesheet" href="../dist/css/output.css">
    <link rel="stylesheet" href="../assets/fontawesome/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include 'includes/sidebar.php'; ?>
    <?php include 'includes/header.php'; ?>

    <div class="ml-64 pt-16 p-6">
        <?php if ($message): ?>
            <div class="bg-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-100 border border-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-400 text-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-700 px-4 py-3 rounded mb-4 flex items-center justify-between">
                <span>
                    <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?> mr-2"></i>
                    <?php echo htmlspecialchars($message); ?>
                </span>
                <a href="../index.html" target="_blank" class="text-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-700 hover:underline font-semibold">
                    <i class="fas fa-external-link-alt mr-1"></i>Shiko Faqen
                </a>
            </div>
        <?php endif; ?>

        <!-- Pending Reviews -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <i class="fas fa-clock text-orange-500 mr-2"></i>
                Reviews në Pritje (<?php echo count($reviews['pending'] ?? []); ?>)
            </h2>
            <?php if (empty($reviews['pending'])): ?>
                <p class="text-gray-500 text-center py-8">Nuk ka reviews në pritje.</p>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($reviews['pending'] ?? [] as $review): ?>
                        <div class="border rounded-lg p-4 bg-orange-50">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h3 class="font-bold text-lg"><?php echo htmlspecialchars($review['name'] ?? 'Anonim'); ?></h3>
                                    <div class="flex items-center mt-1">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?php echo $i <= ($review['rating'] ?? 0) ? 'text-yellow-400' : 'text-gray-300'; ?>"></i>
                                        <?php endfor; ?>
                                        <span class="ml-2 text-sm text-gray-600"><?php echo $review['rating'] ?? 0; ?>/5</span>
                                    </div>
                                </div>
                                <span class="text-sm text-gray-500"><?php echo htmlspecialchars($review['date'] ?? ''); ?></span>
                            </div>
                            <p class="text-gray-700 mb-4"><?php echo nl2br(htmlspecialchars($review['message'] ?? '')); ?></p>
                            <div class="flex space-x-2">
                                <form method="POST" class="inline">
                                    <input type="hidden" name="action" value="approve">
                                    <input type="hidden" name="id" value="<?php echo $review['id']; ?>">
                                    <button type="submit" class="bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 font-semibold shadow-lg hover:shadow-xl transition-all">
                                        <i class="fas fa-save mr-2"></i>Speichern & Genehmigen
                                    </button>
                                </form>
                                <form method="POST" class="inline" onsubmit="return confirm('A jeni të sigurt që dëshironi ta refuzoni?');">
                                    <input type="hidden" name="action" value="reject">
                                    <input type="hidden" name="id" value="<?php echo $review['id']; ?>">
                                    <button type="submit" class="bg-red-500 text-white px-6 py-3 rounded-lg hover:bg-red-600 font-semibold shadow-lg hover:shadow-xl transition-all">
                                        <i class="fas fa-times mr-2"></i>Refuzo
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Approved Reviews -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                Reviews të Aprovuara (<?php echo count($reviews['approved'] ?? []); ?>)
            </h2>
            <?php if (empty($reviews['approved'])): ?>
                <p class="text-gray-500 text-center py-8">Nuk ka reviews të aprovuara.</p>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($reviews['approved'] ?? [] as $review): ?>
                        <div class="border rounded-lg p-4 bg-green-50">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h3 class="font-bold text-lg"><?php echo htmlspecialchars($review['name'] ?? 'Anonim'); ?></h3>
                                    <div class="flex items-center mt-1">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?php echo $i <= ($review['rating'] ?? 0) ? 'text-yellow-400' : 'text-gray-300'; ?>"></i>
                                        <?php endfor; ?>
                                        <span class="ml-2 text-sm text-gray-600"><?php echo $review['rating'] ?? 0; ?>/5</span>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm text-gray-500"><?php echo htmlspecialchars($review['date'] ?? ''); ?></span>
                                    <form method="POST" class="inline" onsubmit="return confirm('A jeni të sigurt?');">
                                        <input type="hidden" name="action" value="delete_approved">
                                        <input type="hidden" name="id" value="<?php echo $review['id']; ?>">
                                        <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded text-sm hover:bg-red-600">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($review['message'] ?? '')); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Info Box -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-500 text-2xl mr-4 mt-1"></i>
                <div>
                    <h3 class="text-lg font-bold text-blue-900 mb-2">Informacion</h3>
                    <p class="text-blue-800 mb-3">
                        Reviews, die hier genehmigt werden, werden automatisch in <strong>index.html</strong> über die API übernommen. 
                        Vetëm reviews të aprovuara shfaqen në faqen publike.
                    </p>
                    <a href="../index.html" target="_blank" class="inline-flex items-center bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                        <i class="fas fa-external-link-alt mr-2"></i>Shiko Faqen Publike
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
