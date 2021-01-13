<?php

namespace app\tool;
class Tool
{
    public static function showAjax($data)
    {
        $result['status'] = $data['status'] ?? 0;
        $result['message'] = $data['message'] ?? '';
        $result['data'] = $data['data'] ?? [];
        return json_encode($result, JSON_UNESCAPED_UNICODE);

    }
}