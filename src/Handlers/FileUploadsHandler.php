<?php

namespace System\Handlers;

class FileUploadsHandler
{
    public function upload($file, $option, $allowed_extensions = ["png", "jpg", "gif", "mp4"])
    {
        if ($file->getClientOriginalExtension() && !in_array($file->getClientOriginalExtension(), $allowed_extensions)) {
            return false;
        }

        // 获取文件相关信息
        // $originalName = $file->getClientOriginalName(); // 文件原名
        // $ext = $file->getClientOriginalExtension();     // 扩展名
        // $realPath = $file->getRealPath();   //临时文件的绝对路径
        // $type = $file->getClientMimeType();     // image/jpeg

        $destinationPath = uploads_path($option);
        $extension = $file->getClientOriginalExtension();
        $fileName = uniqid() . '.' . $extension;
        return $file->storeAs($destinationPath, $fileName);
    }
}