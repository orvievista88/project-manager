<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo; 

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'deadline',
        'user_id',
    ];

    protected $casts = [
        'deadline' => 'date',
    ];

    // Relationship: Project has many Tasks
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    // Progress Indicator
    public function progress(): int
    {
        $total = $this->tasks()->count();

        if ($total === 0) {
            return 0;
        }

        $completed = $this->tasks()
            ->where('status', 'done')
            ->count();

        return (int) round(($completed / $total) * 100);
    }

    /**
     * Define the relationship: A project belongs to a User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
