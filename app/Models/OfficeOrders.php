<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class OfficeOrders extends Model
{
	
	protected $table = 'office_orders';

	protected $fillable = [

		'application',
		'office_order',
		'memorandum',
		'created_by',
		'created_at',
		'active'
	];
}