<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Admin extends Model
{
	
	protected $table = 'admin_user';

	protected $fillable = [

		'teacher_id',
		'name',
		'username',
		'email',
		'password',
		'password_hash',
		'type',
		'department',
		'faculty',
		'description',
		'designation',
		'mobile',
		'address',
		'created_at',
		'logdate',
		'active'

	];
}