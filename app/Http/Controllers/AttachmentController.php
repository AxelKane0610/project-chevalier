<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    //
    public function show($folder, $id, $filename)
    {
        // 1. Tái tạo lại đường dẫn tương đối trong ổ D
        // Ví dụ: "1/98/overheat.png"
        $relativePath = "{$folder}/{$id}/{$filename}";

        // 2. Gọi ổ cứng thông qua disk 'attachments' đã cấu hình
        $disk = Storage::disk('attachments');

        // 3. Kiểm tra file có tồn tại thực tế ở ổ D:\storage\attachments\ không
        if (!$disk->exists($relativePath)) {
            abort(404, 'Tập tin đính kèm không tồn tại.');
        }

        // 4. Lấy đường dẫn tuyệt đối đầy đủ (D:\storage\attachments\1\98\overheat.png)
        $fullPath = $disk->path($relativePath);
        
        // 5. Tự động lấy định dạng File (image/png, application/pdf,...)
        $mimeType = $disk->mimeType($relativePath);

        // 6. Trả file về trình duyệt một cách an toàn
        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }
}
