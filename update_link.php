<?php
$pdo = new PDO('mysql:host=127.0.0.1:3307;dbname=dbenrollment', 'root', '');
$stmt = $pdo->prepare('UPDATE tblinstructor SET user_id = ? WHERE instructor_id = ?');
$result = $stmt->execute([7, 99]);
echo 'Update result: ' . ($result ? 'Success' : 'Failed') . "\n";

$stmt2 = $pdo->prepare('SELECT user_id FROM tblinstructor WHERE instructor_id = 99');
$stmt2->execute();
echo 'Updated user_id: ' . $stmt2->fetchColumn() . "\n";
