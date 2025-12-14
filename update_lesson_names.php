<?php
require_once __DIR__ . '/config/connect.php';

// Fetch all lessons
$result = $mysqli->query("SELECT id, name FROM tb_content");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $old_name = $row['name'];
        
        // Replace "Week" with "บทที่" (Case insensitive just in case)
        $new_name = preg_replace('/^Week\s*/i', 'บทที่ ', $old_name);
        
        // Also handling "Week" without space if it exists, though space is expected.
        // If the name doesn't start with Week, it might be skipped, but the request was specifically for "Week xy: ..." -> "บทที่ xy: ..."
        
        if ($old_name !== $new_name) {
            $stmt = $mysqli->prepare("UPDATE tb_content SET name = ? WHERE id = ?");
            $stmt->bind_param("si", $new_name, $id);
            if ($stmt->execute()) {
                echo "Updated ID $id: '$old_name' -> '$new_name'\n";
            } else {
                echo "Error updating ID $id: " . $stmt->error . "\n";
            }
            $stmt->close();
        } else {
            echo "Skipped ID $id: '$old_name' (Pattern not matched)\n";
        }
    }
} else {
    echo "Error fetching content: " . $mysqli->error . "\n";
}
?>
