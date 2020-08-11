<?php

namespace System\Models;

use QCod\ImageUp\HasImageUploads;

class UserGrade extends Model
{
    use HasImageUploads;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setImagesField([
            'image' => [
                'path' => uploads_path('user.grade'),
                'disk' => config('filesystems.default', 'public'),
            ],
        ]);
    }
}
