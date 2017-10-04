<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CourseStep extends Model
{
    protected $table = 'course_steps';

    protected $fillable = [
        'name', 'description', 'image', 'start_date'
    ];

    protected $dates = [
        'start_date'
    ];

    protected $results_cache = array();

    public function course()
    {
        return $this->belongsTo('App\Course', 'course_id', 'id');
    }

    public function questions()
    {
        return $this->hasMany('App\Question', 'step_id', 'id');
    }

    public function tasks()
    {
        return $this->hasMany('App\Task', 'step_id', 'id')->orderBy('id');
    }
    public function class_tasks()
    {
        return $this->hasMany('App\Task', 'step_id', 'id')->Where('only_remote', false)->orderBy('id');
    }
    public function remote_tasks()
    {
        return $this->hasMany('App\Task', 'step_id', 'id')->Where('only_class', false)->orderBy('id');
    }

    public static function createStep($course, $data)
    {
        $step = new CourseStep();
        $step->name = $data['name'];
        $step->description = $data['description'];
        $step->notes = $data['notes'];
        $step->theory = $data['theory'];
        $step->course_id = $course->id;
        $step->start_date = Carbon::createFromFormat('Y-m-d', $data['start_date']);
        $step->save();
        return $step;
    }
    public static function editStep($step, $data)
    {
        $step->name = $data['name'];
        $step->description = $data['description'];
        $step->notes = $data['notes'];
        $step->theory = $data['theory'];
        $step->start_date = Carbon::createFromFormat('Y-m-d', $data['start_date']);
        $step->save();
        return $step;
    }

    public function stats(User $student)
    {
        if (isset($this->results_cache[$student->id]))
        {
            return $this->results_cache[$student->id];
        }
        $results = ['percent'=>0, 'points'=>0, 'max_points'=>0];
        if ($this->course->students->contains($student))
        {
            if ($student->pivot->is_remote)
            {
                $tasks = $this->remote_tasks;
            }
            else {
                $tasks = $this->class_tasks;
            }
            foreach ($tasks as $task)
            {
                if (!$task->is_star) $results['max_points'] += $task->max_mark;
                $results['points'] += $student->submissions()->where('task_id', $task->id)->max('mark');
            }
            if ($results['max_points'] != 0)
            {
                $results['percent'] = $results['points'] * 100 / $results['max_points'];
            }
        }
        return $results;
    }
    public function percent(User $student)
    {
        return ($this->stats($student))['percent'];
    }
    public function points(User $student)
    {
        return ($this->stats($student))['points'];
    }
    public function max_points(User $student)
    {
        return ($this->stats($student))['max_points'];
    }

}
