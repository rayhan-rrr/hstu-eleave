<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class LeaveTypes extends Model
{
	
	protected $table = 'leave_types';

	protected $fillable = [

		'id',
		'name',
		'active'
	];
}