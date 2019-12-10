<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ApplicationForwarding extends Model
{
	
	protected $table = 'application_forwarding';

	protected $fillable = [

		'application',
		'department',
		'faculty',
		'to_designation',
		'to_user',
		'forwarded_by',
		'by_designation',
		'forward_remark',
		'docket',
		'created_at',
		'active'
	];
}