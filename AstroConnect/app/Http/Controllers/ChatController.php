<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\ChMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChatController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'room_id' => ['required', 'regex:/^(\\d+)-(\\d+)$/'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        [$firstParticipantId, $secondParticipantId] = $this->participantsFromRoomId($validated['room_id']);
        $authUser = $request->user();

        if (! in_array($authUser->id, [$firstParticipantId, $secondParticipantId], true)) {
            abort(403, 'You are not a participant of this room.');
        }

        $firstParticipant = User::query()->find($firstParticipantId);
        $secondParticipant = User::query()->find($secondParticipantId);

        if (! $firstParticipant || ! $secondParticipant) {
            abort(403, 'Invalid room participants.');
        }

        $isValidAstrologerUserPair =
            ($firstParticipant->canAccessAstrologerPanel() && $secondParticipant->canAccessUserPanel())
            || ($secondParticipant->canAccessAstrologerPanel() && $firstParticipant->canAccessUserPanel());

        if (! $isValidAstrologerUserPair) {
            abort(403, 'Room must contain one astrologer and one user.');
        }

        $recipientId = $authUser->id === $firstParticipantId ? $secondParticipantId : $firstParticipantId;

        $message = new ChMessage();
        $message->from_id = $authUser->id;
        $message->to_id = $recipientId;
        $message->body = $validated['message'];
        $message->save();

        broadcast(new MessageSent(
            roomId: $validated['room_id'],
            message: $validated['message'],
            senderName: $authUser->name,
        ))->toOthers();

        return response()->json([
            'id' => $message->id,
            'room_id' => $validated['room_id'],
            'message' => $message->body,
            'sender_name' => $authUser->name,
            'sent_at' => $message->created_at?->toISOString(),
        ], 201);
    }

    /**
     * Parse room id format: "{smallerUserId}-{largerUserId}".
     *
     * @return array{0:int,1:int}
     */
    private function participantsFromRoomId(string $roomId): array
    {
        preg_match('/^(\\d+)-(\\d+)$/', $roomId, $matches);

        return [(int) $matches[1], (int) $matches[2]];
    }
}
