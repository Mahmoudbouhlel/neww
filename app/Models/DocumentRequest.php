<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'document_template_id',
        'service_id',
        'data',
        'status',
        'agent_id',
        'agent_comment'
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function user() { return $this->belongsTo(User::class); }


    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function documentTemplate()
    {
        return $this->belongsTo(DocumentTemplate::class, 'document_template_id');
    }
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

// In App\Models\DocumentRequest.php

    public function template()
    {
        return $this->belongsTo(DocumentTemplate::class, 'document_template_id');
    }

    public function personnel()
    {
        return $this->belongsTo(\App\Models\Personnel::class, 'user_id', 'pers_no');
    }



}
