<?php
namespace App\Exceptions;

use Exception;

class DuplicateMarkException extends Exception
{
    public function render($request)
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
        ], 422);
    }
}
