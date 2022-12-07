<?php 

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

class UserChecker implements UserCheckerInterface
{
	public function checkPreAuth(UserInterface $user): void
	{
		if (!$user instanceof User){
			return;
		}

		if (!$user->isActive()){
			throw new CustomUserMessageAccountStatusException('Votre compte a été désactivé.');
		}

		// if ($user->isDeleted()){
		// 	throw new CustomUserMessageAccountStatusException('Votre compte a été supprimé.');
		// }
	}

	public function checkPostAuth(UserInterface $user): void
	{
		if (!$user instanceof User){
			return;
		}

		// if ($user->isExpired()){
		// 	throw new AccountExpiredException('...');
		// }
	}
}