<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectLocation extends Model
{
    protected $table = 'tblproject_locations';

    protected $fillable = [
        'project_id', 'name', 'address', 'city', 'state',
        'country', 'postal_code', 'latitude', 'longitude', 'type'
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function getFullAddressAttribute()
    {
        $parts = array_filter([$this->address, $this->city, $this->state, $this->country]);
        return implode(', ', $parts);
    }

    public function getGoogleMapsUrlAttribute()
    {
        if ($this->latitude && $this->longitude) {
            return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
        }
        if ($this->address) {
            return "https://www.google.com/maps?q=" . urlencode($this->full_address);
        }
        return null;
    }
}
