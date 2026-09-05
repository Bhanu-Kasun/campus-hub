<?php
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';
requireAdminLogin();

// Tell browser this is an XML file download
header('Content-Type: application/xml; charset=utf-8');
header('Content-Disposition: attachment; filename="events.xml"');

$xml = new SimpleXMLElement('<events/>');

// Fetch events from DB
$result = mysqli_query($conn, "SELECT event_id, title, description, event_date, venue 
                               FROM events ORDER BY event_date ASC");

while ($row = mysqli_fetch_assoc($result)) {
    $eventNode = $xml->addChild('event');
    $eventNode->addAttribute('id', $row['event_id']);
    $eventNode->addChild('title', htmlspecialchars($row['title']));
    $eventNode->addChild('description', htmlspecialchars($row['description']));
    $eventNode->addChild('date', $row['event_date']);
    $eventNode->addChild('venue', htmlspecialchars($row['venue']));
}

// Output XML
echo $xml->asXML();
?>
