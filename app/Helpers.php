<?php

if (!function_exists('generateResponse')) {
    function generateResponse($success, $message, $data = [], $code)
    {
        return response()->json(['success' => $success, 'message' => $message, 'data' => $data], $code);
    }
}

if (!function_exists('uploads')) {
    function uploads($file, $path = 'uploads/')
    {
        $file_response = [
            'success' => false,
        ];
        $file_name = time().$file->getClientOriginalName();

        if ($file->move($path, $file_name)) {
            $file_response = [
                'success' => true,
                'file_name' => $path.$file_name
            ];
        }

        return $file_response;
    }
}

if (!function_exists('remove_uploads')) {
    function remove_uploads($path = '')
    {
        if ($path != "") {
            unlink($path);
        }
        return true;
    }
}