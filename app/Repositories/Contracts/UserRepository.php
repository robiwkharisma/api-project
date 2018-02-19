<?php

namespace App\Repositories\Contracts;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface RaisonSocialeRepository
 * @package namespace App\Repositories\Contracts;
 */
interface UserRepository extends RepositoryInterface
{
	// Get User List
	function getList();

	// Toggle User Active Flag
	function toggleActive($id);

	// Toggle User Customer Flag
	function toggleCustomer($id);

	// Check User registered email
	function checkRegisteredEmail($email);
}