<?php
require_once __DIR__ . '/config/connect.php';

// Fetch all lessons
$result = $mysqli->query("SELECT id, name FROM tb_content");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $old_name = $row['name'];
        
        // Match "บทที่ X" at the start of the string
        // \s* matches optional whitespace
        // \d+ matches the number
        // u modifier for UTF-8 support
        if (preg_match('/^(บทที่\s*\d+)/u', $old_name, $matches)) {
            $new_name = $matches[1];
            
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
                echo "Skipped ID $id: '$old_name' (Already correct or no change needed)\n";
            }
        } else {
            echo "Skipped ID $id: '$old_name' (Pattern not matched)\n";
        }
    }
} else {
    echo "Error fetching content: " . $mysqli->error . "\n";
}
?>
