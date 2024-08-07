<?php

namespace App\Models;

use App\Models\Traits\TableAlias;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Batch extends Model
{
    use HasFactory, TableAlias;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'session_id',
        'module_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'session_id' => 'integer',
        'module_id' => 'integer',
    ];


    public function bookings(){
        return $this->morphMany( Booking::class, 'bookable' );
    }

    public function doctorBatches(): HasMany
    {
        return $this->hasMany( DoctorBatch::class );
    }

    public function batch_bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function doctors(): BelongsToMany
    {
        return $this->belongsToMany(Doctor::class)
            ->using(DoctorBatch::class)
            ->as('doctor_batch')
            ->withPivot('id', 'doctor_id', 'batch_id')
            ->withTimestamps();
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function obtainableContentsQuery( $clinical_id = null ){


        $contents = Content::queryTableAs('c');

        $contents->join( 'batches as b', 'c.session_id', 'b.session_id' );
        $contents->leftJoin( 'module_topic as mt', 'c.topic_id', 'mt.topic_id' );


        $contents->where(function($contents) use( $clinical_id ) {
            
            $contents->where(function($contents){
                $contents->whereNull('c.clinical_id');
                
                $contents->where(function($contents){
                    $contents->whereColumn( 'c.batch_id', 'b.id' );
                    $contents->orWhereColumn( 'mt.module_id', 'b.module_id' );
                });
            });

            if( $clinical_id ) {
                $contents->{is_array($clinical_id) ? 'orWhereIn':'orWhere'}( 'c.clinical_id', $clinical_id );
            }

        });

        $contents->select(
            DB::raw('DISTINCT c.id'), 
            'c.topic_id', 
            'c.type',
            'c.material_id', 
            'c.material_type', 
            'c.session_id', 
            'b.id AS batch_id', 
            'c.clinical_id', 
            'c.created_at', 
            'c.updated_at'
        );

        return $contents;
    }

}
