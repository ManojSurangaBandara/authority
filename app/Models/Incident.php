<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    protected $table = 'incidents';
    protected $fillable = ['incident_type_id', 'description', 'image1', 'image2', 'image3'];

    public function incidentType()
    {
        return $this->belongsTo(IncidentType::class);
    }
}
