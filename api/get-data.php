<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Include database connection
require_once __DIR__ . '/../admin/includes/db_connect.php';

// Check if database connection exists before proceeding
if (!isset($pdo)) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$type = $_GET['type'] ?? '';

try {
    switch ($type) {
        case 'gallery':
            $stmt = $pdo->query("SELECT * FROM gallery ORDER BY sort_order ASC, created_at DESC");
            $gallery = ['home' => [], 'portfolio' => [], 'details' => []];
            while ($row = $stmt->fetch()) {
                $item = ['image' => $row['image'], 'title' => $row['title']];
                if (isset($gallery[$row['category']])) {
                    $gallery[$row['category']][] = $item;
                }
            }
            echo json_encode($gallery);
            break;
        
        case 'services':
            $stmt = $pdo->query("SELECT * FROM services WHERE active = 1 ORDER BY sort_order ASC");
            echo json_encode($stmt->fetchAll());
            break;
        
        case 'reviews':
            try {
            $stmt = $pdo->query("SELECT name, message, rating, DATE_FORMAT(date, '%Y-%m-%d') as date FROM reviews WHERE status = 'approved' ORDER BY date DESC");
                $reviews = $stmt->fetchAll();
                echo json_encode($reviews ? $reviews : []);
            } catch (PDOException $e) {
                echo json_encode([]);
            }
            break;
        
        case 'catalogs':
            try {
                $stmt = $pdo->query("SELECT * FROM catalogs WHERE active = 1 ORDER BY sort_order ASC");
                $catalogs = $stmt->fetchAll();
                if ($catalogs) {
                    foreach ($catalogs as &$catalog) {
                        $pStmt = $pdo->prepare("SELECT * FROM catalog_products WHERE catalog_id = :cid AND active = 1 ORDER BY sort_order ASC");
                        $pStmt->execute(['cid' => $catalog['id']]);
                        $catalog['products'] = $pStmt->fetchAll();
                    }
                }
                echo json_encode($catalogs ? $catalogs : []);
            } catch (PDOException $e) {
                echo json_encode([]);
            }
            break;
        
        case 'partners':
            try {
                $stmt = $pdo->query("SELECT * FROM partners WHERE active = 1 ORDER BY sort_order ASC, id DESC");
                $partners = $stmt->fetchAll();
                echo json_encode($partners ? $partners : []);
            } catch (PDOException $e) {
                echo json_encode([]);
            }
            break;
    
        case 'portfolio':
            $stmt = $pdo->query("SELECT id, title, description, image as path, type, DATE_FORMAT(date, '%d.%m.%Y') as date FROM projects WHERE active = 1 ORDER BY date DESC");
            echo json_encode($stmt->fetchAll());
            break;
    
        case 'customization':
            $response = [];

            // 1. HERO SECTION
            $hero = $pdo->query("SELECT * FROM hero_section LIMIT 1")->fetch();
            if ($hero) {
                $response['hero'] = [
                    'title' => $hero['title'],
                    'subtitle' => $hero['subtitle'],
                    'image' => $hero['image'],
                    'mini_text' => $hero['mini_text'] ?? 'PREMIUM QUALITÄT SEIT 2010',
                    'button1_text' => $hero['button1_text'],
                    'button1_link' => $hero['button1_link'],
                    'button2_text' => $hero['button2_text'],
                    'button2_link' => $hero['button2_link'],
                    'stats_bar' => [
                        'stat1_number' => $hero['stat1_number'],
                        'stat1_text' => $hero['stat1_text'],
                        'stat2_number' => $hero['stat2_number'],
                        'stat2_text' => $hero['stat2_text'],
                        'stat3_number' => $hero['stat3_number'],
                        'stat3_text' => $hero['stat3_text']
                    ]
                ];
            }

            // 2. ABOUT SECTION
            $about = $pdo->query("SELECT * FROM about_section LIMIT 1")->fetch();
            if ($about) {
                $response['about'] = [
                    'title' => $about['title'],
                    'description1' => $about['description1'],
                    'description2' => $about['description2'],
                    'shop_title' => $about['shop_title'],
                    'shop_text' => $about['shop_text'],
                    'processing_title' => $about['processing_title'],
                    'processing_text' => $about['processing_text'],
                    'image1' => $about['image1'],
                    'image2' => $about['image2'],
                    'image3' => $about['image3'],
                    
                    // Fields added for update_about_table
                    'page_hero_image' => $about['page_hero_image'] ?? '',
                    'page_hero_title' => $about['page_hero_title'] ?? '',
                    'page_hero_subtitle' => $about['page_hero_subtitle'] ?? '',
                    'full_content' => [
                        'title' => $about['full_title'] ?? '',
                        'description1' => $about['full_desc1'] ?? '',
                        'description2' => $about['full_desc2'] ?? '',
                        'description3' => $about['full_desc3'] ?? ''
                    ],
                    'story_title' => $about['story_title'] ?? '',
                    'story_paragraph1' => $about['story_p1'] ?? '',
                    'story_paragraph2' => $about['story_p2'] ?? '',
                    'story_paragraph3' => $about['story_p3'] ?? '',
                    'card1_title' => $about['card1_title'] ?? '',
                    'card1_text' => $about['card1_text'] ?? '',
                    'card2_title' => $about['card2_title'] ?? '',
                    'card2_text' => $about['card2_text'] ?? '',
                    'card3_title' => $about['card3_title'] ?? '',
                    'card3_text' => $about['card3_text'] ?? '',
                    'stats' => [
                        'stat1_number' => $about['stat1_num'] ?? '',
                        'stat1_text' => $about['stat1_text'] ?? '',
                        'stat2_number' => $about['stat2_num'] ?? '',
                        'stat2_text' => $about['stat2_text'] ?? '',
                        'stat3_number' => $about['stat3_num'] ?? '',
                        'stat3_text' => $about['stat3_text'] ?? ''
                    ],
                    'show_in_index' => (bool)($about['show_in_index'] ?? 1)
                ];
            }

            // 3. SERVICES SECTION
            $servicesSec = $pdo->query("SELECT * FROM services_section LIMIT 1")->fetch();
            if ($servicesSec) {
                $response['services'] = [
                    'hero_image' => $servicesSec['hero_image'],
                    'show_in_index' => (bool)$servicesSec['show_in_index'],
                    'max_cards_index' => (int)$servicesSec['max_cards_index'],
                    'section_subtitle' => $servicesSec['section_subtitle'],
                    'section_title_line1' => $servicesSec['section_title_line1'],
                    'section_title_line2' => $servicesSec['section_title_line2'],
                    'section_description' => $servicesSec['section_description'],
                    'full_title' => $servicesSec['full_title'],
                    'full_description' => $servicesSec['full_description'],
                    'additional_title' => $servicesSec['additional_title'],
                    'additional_cards' => json_decode($servicesSec['additional_cards_json'] ?? '[]', true),
                    'why_title' => $servicesSec['why_title'],
                    'why_description' => $servicesSec['why_description'],
                    'why_cards' => json_decode($servicesSec['why_cards_json'] ?? '[]', true),
                    'process_title' => $servicesSec['process_title'],
                    'process_description' => $servicesSec['process_description'],
                    'process_steps' => json_decode($servicesSec['process_steps_json'] ?? '[]', true)
                ];
            }

            // 4. CONTACT SECTION
            $contact = $pdo->query("SELECT * FROM contact_section LIMIT 1")->fetch();
            if ($contact) {
                $response['contact'] = [
                    'section_title' => $contact['title'],
                    'section_subtitle' => $contact['subtitle'],
                    'address_line1' => $contact['address_line1'],
                    'address_line2' => $contact['address_line2'],
                    'phone1' => $contact['phone1'],
                    'phone2' => $contact['phone2'],
                    'email' => $contact['email'],
                    'facebook_link' => $contact['facebook_link'],
                    'instagram_link' => $contact['instagram_link'],
                    'linkedin_link' => $contact['linkedin_link'],
                    'whatsapp_number' => $contact['whatsapp_number'],
                    'project_manager_title' => $contact['project_manager_title'] ?? '',
                    'project_manager_name' => $contact['project_manager_name'] ?? '',
                    'project_manager_description' => $contact['project_manager_description'] ?? '',
                    'opening_hours_title' => $contact['opening_hours_title'] ?? '',
                    'opening_hours_monday_friday' => $contact['opening_hours_monday_friday'] ?? '',
                    'opening_hours_saturday' => $contact['opening_hours_saturday'] ?? '',
                    'opening_hours_sunday' => $contact['opening_hours_sunday'] ?? '',
                    'form_title' => $contact['form_title'] ?? '',
                    'form_button' => $contact['form_button'] ?? '',
                    'map_embed_code' => $contact['map_embed_code'] ?? ''
                ];
            }
            
            // 5. CATALOGS SECTION
            $catalogsSec = $pdo->query("SELECT * FROM catalogs_section LIMIT 1")->fetch();
            if ($catalogsSec) {
                $response['catalogs'] = [
                    'hero_image' => $catalogsSec['hero_image'],
                    'show_in_index' => (bool)$catalogsSec['show_in_index'],
                    'max_catalogs_index' => (int)$catalogsSec['max_catalogs_index'],
                    'index_title' => $catalogsSec['index_title'],
                    'index_description' => $catalogsSec['index_description'],
                    'full_title' => $catalogsSec['full_title'],
                    'full_description' => $catalogsSec['full_description']
                ];
            }

            // 6. PORTFOLIO SECTION
            try {
                $portfolioSec = $pdo->query("SELECT * FROM portfolio_section LIMIT 1")->fetch();
                if ($portfolioSec) {
                    $response['portfolio'] = [
                        'hero_image' => $portfolioSec['hero_image'] ?? '',
                        'show_in_index' => (bool)($portfolioSec['show_in_index'] ?? 1),
                        'max_items_index' => (int)($portfolioSec['max_items_index'] ?? 6),
                        'index_title' => $portfolioSec['index_title'] ?? '',
                        'index_description' => $portfolioSec['index_description'] ?? '',
                        'full_title' => $portfolioSec['full_title'] ?? '',
                        'full_description' => $portfolioSec['full_description'] ?? ''
                    ];
                } else {
                    // Default values if table doesn't exist or is empty
                    $response['portfolio'] = [
                        'hero_image' => '',
                        'show_in_index' => true,
                        'max_items_index' => 6,
                        'index_title' => 'Unser Baujournal',
                        'index_description' => 'Aktuelle Projekte und Inspirationen.',
                        'full_title' => 'Unsere Projekte',
                        'full_description' => 'Eine Auswahl unserer erfolgreich abgeschlossenen Projekte'
                    ];
                }
            } catch (PDOException $e) {
                // Table doesn't exist - use defaults
                $response['portfolio'] = [
                    'hero_image' => '',
                    'show_in_index' => true,
                    'max_items_index' => 6,
                    'index_title' => 'Unser Baujournal',
                    'index_description' => 'Aktuelle Projekte und Inspirationen.',
                    'full_title' => 'Unsere Projekte',
                    'full_description' => 'Eine Auswahl unserer erfolgreich abgeschlossenen Projekte'
                ];
            }

            // 7. LEGAL (Optional, usually loaded separately or page specific)
            // But we can include it if frontend expects it
            $legal = $pdo->query("SELECT * FROM legal_section LIMIT 1")->fetch();
            if ($legal) {
                $response['legal'] = [
                    'impressum' => $legal['impressum_content'],
                    'privacy' => $legal['privacy_content'],
                    'agb' => $legal['agb_content']
                ];
            }

            echo json_encode($response);
            break;
        
        default:
            echo json_encode(['error' => 'Invalid type']);
            break;
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>