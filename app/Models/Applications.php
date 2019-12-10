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
		'created_at',
		'updated_at',
		'active'
	];
}