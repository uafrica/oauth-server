<?php
echo json_encode([
    'error' => $e->getErrorType(),
    'message' => $e->getMessage(),
]);
