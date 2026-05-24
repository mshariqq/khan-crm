<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectFile extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'tblproject_files';
    public $timestamps = true;
    protected $fillable = [
        'file_name', 'original_file_name', 'subject', 'description', 'filetype',
        'dateadded', 'last_activity', 'project_id', 'folder_id', 'visible_to_customer', 'staffid',
        'contact_id', 'external', 'external_link', 'thumbnail_link', 'file_size',
        'file_version', 'expires_at', 'virus_scan_status', 'virus_scan_at', 'scan_result',
        'document_status',
    ];

    const DOCUMENT_STATUS_DRAFT = 'draft';
    const DOCUMENT_STATUS_REVIEWED = 'reviewed';
    const DOCUMENT_STATUS_APPROVED = 'approved';
    const DOCUMENT_STATUS_REJECTED = 'rejected';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'dateadded' => 'datetime',
        'last_activity' => 'datetime',
        'expires_at' => 'datetime',
        'virus_scan_at' => 'datetime',
        'project_id' => 'integer',
        'folder_id' => 'integer',
        'staffid' => 'integer',
        'contact_id' => 'integer',
        'visible_to_customer' => 'boolean',
        'file_size' => 'integer',
        'file_version' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staffid', 'staffid');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(ProjectFileFolder::class, 'folder_id', 'id');
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(ProjectFileDownload::class, 'file_id', 'id');
    }

    public function getDownloadUrlAttribute(): string
    {
        return route('admin.projects.files.download', [$this->project_id, $this->id]);
    }

    public function getFileSizeFormattedAttribute(): ?string
    {
        if (!$this->file_size) return null;
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $unit = 0;
        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }
        return round($size, 2) . ' ' . $units[$unit];
    }

    public function getExtensionAttribute(): string
    {
        return strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION));
    }

    public function getIsImageAttribute(): bool
    {
        return in_array($this->extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp']);
    }

    public function getIsVideoAttribute(): bool
    {
        return in_array($this->extension, ['mp4', 'avi', 'mov', 'wmv', 'mkv', 'webm']);
    }

    public function getIsAudioAttribute(): bool
    {
        return in_array($this->extension, ['mp3', 'wav', 'ogg', 'aac', 'flac']);
    }

    public function getIsPdfAttribute(): bool
    {
        return $this->extension === 'pdf';
    }

    public function getIsOfficeAttribute(): bool
    {
        return in_array($this->extension, ['doc', 'docx', 'xls', 'xlsx', 'csv']);
    }

    public function getFileIconAttribute(): string
    {
        return match (true) {
            $this->is_image => 'bi-file-earmark-image',
            $this->is_pdf => 'bi-file-earmark-pdf',
            $this->is_video => 'bi-file-earmark-play',
            $this->is_audio => 'bi-file-earmark-music',
            in_array($this->extension, ['doc', 'docx']) => 'bi-file-earmark-word',
            in_array($this->extension, ['xls', 'xlsx', 'csv']) => 'bi-file-earmark-excel',
            in_array($this->extension, ['zip', 'rar', '7z']) => 'bi-file-earmark-zip',
            default => 'bi-file-earmark',
        };
    }

    public function getPreviewTypeAttribute(): string
    {
        return match (true) {
            $this->is_image => 'image',
            $this->is_pdf => 'pdf',
            $this->is_video => 'video',
            $this->is_audio => 'audio',
            $this->is_office => 'office',
            default => 'none',
        };
    }

    public function scopeInFolder($query, ?int $folderId)
    {
        if ($folderId) {
            return $query->where('folder_id', $folderId);
        }
        return $query->whereNull('folder_id');
    }

    public function scopeVirusClean($query)
    {
        return $query->where(function ($q) {
            $q->where('virus_scan_status', 'clean')
              ->orWhereNull('virus_scan_status');
        });
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')->where('expires_at', '<=', now());
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function documentWorkflow(): ?string
    {
        return $this->document_status ?? self::DOCUMENT_STATUS_DRAFT;
    }

    public function getDocumentStatusLabelAttribute(): string
    {
        return match ($this->documentWorkflow()) {
            self::DOCUMENT_STATUS_DRAFT => 'Draft',
            self::DOCUMENT_STATUS_REVIEWED => 'Reviewed',
            self::DOCUMENT_STATUS_APPROVED => 'Approved',
            self::DOCUMENT_STATUS_REJECTED => 'Rejected',
            default => 'Draft',
        };
    }

    public function getDocumentStatusColorAttribute(): string
    {
        return match ($this->documentWorkflow()) {
            self::DOCUMENT_STATUS_DRAFT => 'bg-slate-100 text-slate-600',
            self::DOCUMENT_STATUS_REVIEWED => 'bg-blue-100 text-blue-700',
            self::DOCUMENT_STATUS_APPROVED => 'bg-emerald-100 text-emerald-700',
            self::DOCUMENT_STATUS_REJECTED => 'bg-red-100 text-red-700',
            default => 'bg-slate-100 text-slate-600',
        };
    }

    public static function documentStatusLabels(): array
    {
        return [
            self::DOCUMENT_STATUS_DRAFT => 'Draft',
            self::DOCUMENT_STATUS_REVIEWED => 'Reviewed',
            self::DOCUMENT_STATUS_APPROVED => 'Approved',
            self::DOCUMENT_STATUS_REJECTED => 'Rejected',
        ];
    }
}
