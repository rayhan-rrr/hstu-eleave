<?php

namespace App\Helper;

use App\Models\Accounts\AccountCustomer;
use App\Database\DbCon;

use App\Models\Customers;


class CustomerAccount extends DbCon
{


	public static function getCustomerBalance($customer_id)
	{

		$customerBalance = 0;


		$account = Customers::find($customer_id);
		

		return $account->balance;
		
	}	
	
}