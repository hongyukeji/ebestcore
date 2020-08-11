<?php

namespace System\Http\Controllers\Helper;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeController extends Controller
{
    public function index(Request $request)
    {
        $text = $request->input('text', '');
        $size = $request->input('size', '300');
        $margin = $request->input('margin', '0');
        $format = $request->input('format', 'png');
        $base64 = $request->input('base64', 0);

        $qr_code = QrCode::format($format)->size($size)->margin($margin);

        // 颜色: QrCode::color(255,0,255);
        if ($request->filled('color')) {
            $color = $request->input('color');
            $qr_code->color(explode(',', $color));
        }

        // 背景色
        if ($request->filled('background_color')) {
            $background_color = $request->input('background_color');
            $qr_code->color(explode(',', $background_color));
        }

        // 编码: 默认使用 ISO-8859-1
        if ($request->filled('encoding')) {
            $qr_code->encoding($request->input('encoding'));
        }

        // 容错级别设置
        if ($request->filled('error_correction')) {
            $qr_code->errorCorrection($request->input('error_correction'));
        }

        // 加LOGO图
        if ($request->filled('logo')) {
            $logo = $request->input('logo');
            $logo_proportion = $request->input('logo_proportion', '.3');
            $qr_code->merge($logo, $logo_proportion, true);
        }

        $image = $qr_code->generate($text);

        if ($base64) {
            return base64_encode($image);
        }

        //return response($img, 200, ['Content-Type' => 'image/jpg']);
        return response($image, 200)->header('Content-Type', 'image/jpg');
    }
}
