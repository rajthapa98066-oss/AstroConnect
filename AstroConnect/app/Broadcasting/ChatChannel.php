<?php

namespace App\Broadcasting;

use App\Models\User;

class ChatChannel
{
    /**
     * Create a new channel instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     */
    public function join(User $user, string $roomId): array|bool
    {
        $participants = $this->participantsFromRoomId($roomId);

        if ($participants === null) {
            return false;
        }

        [$firstParticipantId, $secondParticipantId] = $participants;

        if (! in_array($user->id, [$firstParticipantId, $secondParticipantId], true)) {
            return false;
        }

        $firstParticipant = User::query()->find($firstParticipantId);
        $secondParticipant = User::query()->find($secondParticipantId);

        if (! $firstParticipant || ! $secondParticipant) {
            return false;
        }

        // Exactly one participant must be an astrologer and the other a standard user.
        return ($firstParticipant->canAccessAstrologerPanel() && $secondParticipant->canAccessUserPanel())
            || ($secondParticipant->canAccessAstrologerPanel() && $firstParticipant->canAccessUserPanel());
    }

    /**
     * Parse room id format: "{smallerUserId}-{largerUserId}".
     *
     * @return array{0:int,1:int}|null
     */
    private function participantsFromRoomId(string $roomId): ?array
    {
        if (! preg_match('/^(\d+)-(\d+)$/', $roomId, $matches)) {
            return null;
        }

        $firstParticipantId = (int) $matches[1];
        $secondParticipantId = (int) $matches[2];

        if ($firstParticipantId <= 0 || $secondParticipantId <= 0 || $firstParticipantId === $secondParticipantId) {
            return null;
        }

        return [$firstParticipantId, $secondParticipantId];
    }
}
