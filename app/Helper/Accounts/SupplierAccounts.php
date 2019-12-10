<?php

namespace App\Helper\Accounts;

use App\Models\Supplier;
use App\Models\Accounts\AccountSupplier;





class SupplierAccounts
{

	public static function updateSupplierBalance($supplierId, $item, $amountToAdjust)
	{
		//item is the row id of the account_supplier table

		//this will check if any previous account data is available or not. if available then last balance will remain there. if not found any data then the new amountToAdjust will be set as the balance of current data.

		$supplier = Supplier::find($supplierId);

		$oldBalance = $supplier->balance;
			
		$supplierAccount = AccountSupplier::find($item);

		//add the new amount with last record's amount and this becomes the new balance.

		$supplierAccount->balance = $oldBalance + $amountToAdjust;

		$supplierAccount->save();

		

		$supplier->balance = $supplierAccount->balance;

		
		$supplier->save();

		return true;
		
	}	
	
}



