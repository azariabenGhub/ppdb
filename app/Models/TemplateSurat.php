<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateSurat extends Model
{
    use HasFactory;
    protected $table = 'template_surat';
    protected $fillable = ['nama', 'file_path', 'file_extension', 'mime_type'];
}
