<?php
require_once 'functions.php';
requireLogin();

$message = '';
$messageType = '';
$pageTitle = 'Bewertungen verwalten';

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $messageType = $_SESSION['message_type'];
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    
    if ($action === 'approve') {
        $stmt = $pdo->prepare("UPDATE reviews SET status = 'approved' WHERE id = :id");
        if ($stmt->execute(['id' => $id])) {
            $_SESSION['message'] = 'Bewertung erfolgreich genehmigt!';
            $_SESSION['message_type'] = 'success';
        }
    } elseif ($action === 'reject') {
        // Rejecting usually means deleting or setting status to rejected. Let's delete for now or soft delete.
        // Based on previous logic, "reject" was removing from pending. So delete is fine or status='rejected'.
        // Let's delete to keep it clean, or use 'rejected' if we want history. 
        // User asked to "fix", standard is usually Approve vs Delete.
        $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = :id");
        if ($stmt->execute(['id' => $id])) {
            $_SESSION['message'] = 'Bewertung abgelehnt (gelöscht)!';
            $_SESSION['message_type'] = 'success'; // or error style for delete
        }
    } elseif ($action === 'delete_approved') {
        $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = :id");
        if ($stmt->execute(['id' => $id])) {
            $_SESSION['message'] = 'Bewertung erfolgreich gelöscht!';
            $_SESSION['message_type'] = 'success';
        }
    }
    
    header("Location: reviews.php");
    exit;
}

// Fetch Reviews
$pendingReviews = $pdo->query("SELECT * FROM reviews WHERE status = 'pending' ORDER BY date DESC")->fetchAll();
$approvedReviews = $pdo->query("SELECT * FROM reviews WHERE status = 'approved' ORDER BY date DESC")->fetchAll();

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Admin Panel</title>
    <link rel="stylesheet" href="../dist/css/output.css">
    <link rel="stylesheet" href="../assets/fontawesome/all.min.css">
    <link rel="icon" type="image/x-icon" href="../favicon.ico" />
    <link rel="icon" type="image/png" sizes="16x16" href="../favicon-16x16.png" />
    <link rel="icon" type="image/png" sizes="32x32" href="../favicon-32x32.png" />
    <link rel="apple-touch-icon" sizes="180x180" href="../apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="Ab-Bau-Fliesen" />
    <link rel="manifest" href="../site.webmanifest" />
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div class="w-64 flex-shrink-0 bg-white border-r border-gray-200">
            <?php include 'includes/sidebar.php'; ?>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm z-10 h-16 flex items-center justify-between px-6 border-b border-gray-200">
                <h1 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-star mr-2 text-yellow-500"></i>Bewertungen verwalten
                </h1>
            </header>
            
            <!-- Scrollable Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-6 md:p-8">
                
                <?php if ($message): ?>
                    <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'success' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200'; ?> flex items-center shadow-sm">
                        <i class="fas <?php echo $messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> mr-3 text-xl"></i>
                        <span class="font-medium"><?php echo $message; ?></span>
                    </div>
                <?php endif; ?>

                <!-- Pending Reviews -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
                    <h2 class="text-xl font-bold mb-4 flex items-center text-orange-600">
                        <i class="fas fa-clock mr-2"></i>
                        Bewertungen ausstehend (<?php echo count($pendingReviews); ?>)
                    </h2>
                    <?php if (empty($pendingReviews)): ?>
                        <div class="text-center py-8 bg-orange-50 rounded-lg border border-orange-100 text-orange-500">
                            Keine neuen Bewertungen ausstehend.
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($pendingReviews as $review): ?>
                                <div class="border rounded-xl p-5 bg-white shadow-sm hover:shadow-md transition-shadow border-l-4 border-l-orange-400">
                                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-3">
                                        <div>
                                            <h3 class="font-bold text-lg text-gray-900"><?php echo htmlspecialchars($review['name']); ?></h3>
                                            <div class="flex items-center mt-1 space-x-2">
                                                <div class="flex text-yellow-400 text-sm">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <i class="fas fa-star <?php echo $i <= $review['rating'] ? '' : 'text-gray-300'; ?>"></i>
                                                    <?php endfor; ?>
                                                </div>
                                                <span class="text-sm text-gray-500">• <?php echo date('d.m.Y', strtotime($review['date'])); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-gray-700 mb-4 bg-gray-50 p-3 rounded-lg text-sm"><?php echo nl2br(htmlspecialchars($review['message'])); ?></p>
                                    <div class="flex space-x-3 pt-2 border-t border-gray-100">
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="action" value="approve">
                                            <input type="hidden" name="id" value="<?php echo $review['id']; ?>">
                                            <button type="submit" class="bg-green-100 text-green-700 px-4 py-2 rounded-lg hover:bg-green-200 font-medium transition-colors flex items-center text-sm">
                                                <i class="fas fa-check mr-2"></i>Genehmigen
                                            </button>
                                        </form>
                                        <form method="POST" class="inline" onsubmit="return confirm('Sind Sie sicher, dass Sie diese Bewertung ablehnen möchten?');">
                                            <input type="hidden" name="action" value="reject">
                                            <input type="hidden" name="id" value="<?php echo $review['id']; ?>">
                                            <button type="submit" class="bg-red-100 text-red-700 px-4 py-2 rounded-lg hover:bg-red-200 font-medium transition-colors flex items-center text-sm">
                                                <i class="fas fa-times mr-2"></i>Ablehnen
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Approved Reviews -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold mb-4 flex items-center text-green-600">
                        <i class="fas fa-check-circle mr-2"></i>
                        Genehmigte Bewertungen (<?php echo count($approvedReviews); ?>)
                    </h2>
                    <?php if (empty($approvedReviews)): ?>
                        <div class="text-center py-8 bg-gray-50 rounded-lg text-gray-500">
                            Noch keine Bewertungen genehmigt.
                        </div>
                    <?php else: ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php foreach ($approvedReviews as $review): ?>
                                <div class="border rounded-xl p-5 bg-white relative group hover:border-green-200 transition-colors">
                                    <div class="flex justify-between items-start mb-2">
                                        <h3 class="font-bold text-gray-900"><?php echo htmlspecialchars($review['name']); ?></h3>
                                        <div class="flex text-yellow-400 text-xs">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star <?php echo $i <= $review['rating'] ? '' : 'text-gray-300'; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <p class="text-gray-600 text-sm mb-3 line-clamp-3"><?php echo htmlspecialchars($review['message']); ?></p>
                                    <div class="flex justify-between items-center text-xs text-gray-400 mt-auto pt-3 border-t border-gray-50">
                                        <span><?php echo date('d.m.Y', strtotime($review['date'])); ?></span>
                                        <form method="POST" class="inline" onsubmit="return confirm('Sind Sie sicher?');">
                                            <input type="hidden" name="action" value="delete_approved">
                                            <input type="hidden" name="id" value="<?php echo $review['id']; ?>">
                                            <button type="submit" class="text-red-400 hover:text-red-600 p-1 rounded transition-colors" title="Löschen">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
            </main>
        </div>
    </div>
</body>
</html>