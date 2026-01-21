<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentTemplate extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'service_id', 'fields'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
    public function documentTemplate()
{
    return $this->belongsTo(DocumentTemplate::class, 'document_template_id');
}

}
