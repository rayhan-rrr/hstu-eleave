<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class FixedUsers extends Model
{
	
	protected $table = 'fixed_users';

	protected $fillable = [

		'id',
		'designation',
		'department',
		'user',
		'updated_at',
		'active'
	];
}