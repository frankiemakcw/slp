<?php
require_once 'vendor/autoload.php';
require_once 'dbconnect.php';

session_start();

$client = new Google_Client([
    'client_id' => '1025312216370-1ba1liogt0rbgunrf7985rqutesratsv.apps.googleusercontent.com'
]);

// Get the credential from POST data
$json = file_get_contents('php://input');
$data = json_decode($json, true);
$idToken = $data['credential'] ?? null;

if (!$idToken) {
    die(json_encode(['success' => false, 'message' => 'No credential provided.']));
}

try {
    $payload = $client->verifyIdToken($idToken);
    if ($payload) {
        $email = $payload['email'];
        $name = $payload['name'];
        $sub = $payload['sub']; // Unique Google ID
        $hd = $payload['hd'] ?? null;

        // Domain restriction
        if ($hd !== 'ywgs.edu.hk') {
            die(json_encode(['success' => false, 'message' => 'Only ywgs.edu.hk users allowed.']));
        }

        // Record login in login table
        $stmt = $pdo->prepare("INSERT INTO login (email, name, login_time) VALUES (:email, :name, NOW())");
        $stmt->execute([
            ':email' => $email,
            ':name' => $name
        ]);

        // Regenerate session ID for security
        session_regenerate_id(true);
        
        $_SESSION['user'] = [
            'sub' => $sub,
            'name' => $name,
            'email' => $email,
            'logged_in' => true,
            'last_activity' => time()
        ];
        
        echo json_encode(['success' => true, 'user' => $_SESSION['user']]);
    } else {
        die(json_encode(['success' => false, 'message' => 'Invalid ID token.']));
    }
} catch (Exception $e) {
    die(json_encode(['success' => false, 'message' => 'Token verification failed: ' . $e->getMessage()]));
}