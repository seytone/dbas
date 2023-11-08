<?php

namespace App\Traits;

use App\Models\Credit;
use App\Models\Student;

trait Creditable
{
    /**
     * Define a polymorphic one-to-many relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function credits()
    {
        return $this->morphMany(Credit::class, 'creditable');
    }

    /**
     * Scope a query to only include credits of a given student.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  App\Models\Student $student
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreditsBy($query, Student $student)
    {
        return $query->whereHas('credits', function ($q) use ($student) {
            $q->where('student_id', $student->id);
        });
    }

    /**
     * Adds a credits to a given student.
     *
     * @param int $student_id
     * @param int $credits
     */
    public function addCredit($student_id, $credits)
    {
        $before = $this->latestCredit($student_id);
        $summary = $before + $credits;

        $this->credits()->save(
            new Credit([
                'student_id' => $student_id,
                'transaction' => 'ingress',
                'credits' => $credits,
                'credits_before' => $before,
                'credits_summary' => $summary
            ])
        );
    }

    /**
     * Remove a credits to a given student.
     *
     * @param int $student_id
     * @param int $credits
     *
     * @return bool
     */
    public function removeCredit($student_id, $credits)
    {
        $before = $this->latestCredit($student_id);

        if ($before < $credits) {
            return false;
        }

        $summary = $before - $credits;

        $this->credits()->save(
            new Credit([
                'student_id' => $student_id,
                'transaction' => 'egress',
                'credits' => $credits,
                'credits_before' => $before,
                'credits_summary' => $summary
            ])
        );

        return true;
    }

    /**
     * Gets lastest credit summary
     *
     * @param int $student_id
     *
     * @return int
     */
    protected function latestCredit($student_id)
    {
        $query = Credit::where('student_id', $student_id);

        return ($query->exists())? $query->latest()->first()->credits_summary : 0;
    }
}
