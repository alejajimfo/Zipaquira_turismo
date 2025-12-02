<?php
header('Content-Type: application/json');
echo json_encode(array(
    'status' => 'ok',
    'message' => 'API funcionando correctamente',
    'timestamp' => date('Y-m-d H:i:s')
));
?>
