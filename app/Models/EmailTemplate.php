<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $table = 'tblemailtemplates';
    protected $primaryKey = 'emailtemplateid';
    public $timestamps = false;

    protected $fillable = [
        'type', 'slug', 'language', 'name', 'subject', 'message', 
        'fromname', 'fromemail', 'plaintext', 'active', 'order'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];
    /**
     * Parse the template with dynamic data
     */
    public function parse($data = [])
    {
        $subject = $this->subject;
        $message = $this->message;

        foreach ($data as $key => $value) {
            $placeholder = '{' . $key . '}';
            $subject = str_replace($placeholder, $value, $subject);
            $message = str_replace($placeholder, $value, $message);
        }

        return [
            'subject' => $subject,
            'message' => $message
        ];
    }
}
