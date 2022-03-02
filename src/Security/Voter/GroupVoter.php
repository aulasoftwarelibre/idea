<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Group;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

use function assert;
use function in_array;

class GroupVoter extends Voter
{
    /**
     * @inheritDoc
     */
    protected function supports(string $attribute, $subject): bool
    {
        return $attribute === 'GROUP_MEMBER'
            && $subject instanceof Group;
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        assert($subject instanceof Group);
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (! $user instanceof User) {
            return false;
        }

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        return match ($attribute) {
            'GROUP_MEMBER' => $user->getGroups()->contains($subject),
            default => false,
        };
    }
}
