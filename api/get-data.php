<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

function getDataPath($file) {
    return dirname(__DIR__) . '/data/' . $file;
}

function readJson($file) {
    $path = getDataPath($file);
    if (!file_exists($path)) {
        return [];
    }
    $content = file_get_contents($path);
    return json_decode($content, true) ?: [];
}

$type = $_GET['type'] ?? '';

switch ($type) {
    case 'gallery':
        $gallery = readJson('gallery.json');
        echo json_encode($gallery);
        break;
    
    case 'services':
        $services = readJson('services.json');
        // Filter only active services
        $activeServices = array_filter($services, function($service) {
            return isset($service['active']) && $service['active'] === true;
        });
        echo json_encode(array_values($activeServices));
        break;
    
    case 'reviews':
        $reviews = readJson('reviews.json');
        // Return only approved reviews
        echo json_encode($reviews['approved'] ?? []);
        break;
    
    case 'activities':
        $activities = readJson('activities.json');
        // Return only active activities with their active services
        $activeActivities = [];
        foreach ($activities as $key => $activity) {
            if (isset($activity['active']) && $activity['active'] === true) {
                $activeServices = array_filter($activity['services'] ?? [], function($service) {
                    return isset($service['active']) && $service['active'] === true;
                });
                $activeActivities[$key] = $activity;
                $activeActivities[$key]['services'] = array_values($activeServices);
            }
        }
        echo json_encode($activeActivities);
        break;
    
    case 'catalogs':
        $catalogs = readJson('catalogs.json');
        // Return only active catalogs with active products
        $activeCatalogs = array_map(function($catalog) {
            if (isset($catalog['products']) && is_array($catalog['products'])) {
                // Filter only active products
                $catalog['products'] = array_values(array_filter($catalog['products'], function($product) {
                    return isset($product['active']) ? $product['active'] === true : true;
                }));
            }
            return $catalog;
        }, array_filter($catalogs['catalogs'] ?? [], function($catalog) {
            return isset($catalog['active']) && $catalog['active'] === true;
        }));
        echo json_encode(array_values($activeCatalogs));
        break;

    case 'portfolio':
        // Use dedicated projects.json (preferred)
        $projects = readJson('projects.json');
        $activeProjects = array_filter($projects['projects'] ?? [], function($p) {
            return isset($p['active']) ? $p['active'] === true : true;
        });
        echo json_encode(array_values($activeProjects));
        break;

    case 'customization':
        $customization = readJson('customization.json');
        echo json_encode($customization);
        break;
    
    default:
        echo json_encode(['error' => 'Invalid type']);
        break;
}