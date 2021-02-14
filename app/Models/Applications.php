<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Applications extends Model
{
	
	protected $table = 'applications';

	protected $fillable = [

		'user',
		'department',
		'faculty',
		'type',
		'subject',
		'days',
		'body',
		'attachment',
		'd_memorandum',
		'status',
		'final_action',
		'office_order',
		'leave_started',
		'leave_started_on',
		'created_at',
		'updated_at',
		'active'
	];
}