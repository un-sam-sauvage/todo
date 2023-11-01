<?php
namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AccessVoter extends Voter
{
	// these strings are just invented: you can use anything
	const ACCESS_ADMIN = 'accessAdmin';
	const ACCESS_LOGGED = 'accessLogged';

	protected function supports(string $attribute, mixed $subject): bool
	{
		// if the attribute isn't one we support, return false
		if (!in_array($attribute, [self::ACCESS_ADMIN, self::ACCESS_LOGGED])) {
			return false;
		}

		return true;
	}

	protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
	{
		$user = $token->getUser();

		return match($attribute) {
			self::ACCESS_ADMIN => $this->isAdmin($user),
			self::ACCESS_LOGGED => $this->isLoggedIn($user),
			default => throw new \LogicException('This code should not be reached!')
		};
	}

	private function isAdmin(User | null $user): bool {
		if (!$this->isLoggedIn($user)) return false;
		if (in_array("ROLE_ADMIN", $user->getRoles())) return true;
		return false;
	}

	private function isLoggedIn (User | null $user): bool {
		if ($user) return true;
		return false;
	}
}