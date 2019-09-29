<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ToDo extends Model
{

    protected $fillable = ['desc', 'user_id'];

    /**
     * The accessors to append to the model's array.
     *
     * @var array
     */
    protected $appends = ['is_toggling_status', 'is_deleting'];


    /**
     * Will be used to show loader while deleting to_do.
     *
     * @return bool
     *
     */
    public function getIsTogglingStatusAttribute()
    {
        return $this->attributes['is_toggling_status'] = false;
    }


    /**
     * Will be used to show loader while making the to_do as complete or incomplete .
     *
     * @return bool
     *
     */
    public function getIsDeletingAttribute()
    {
        return $this->attributes['is_deleting'] = false;
    }

}
