<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\EventAggregator; // Import the EventAggregator class
use App\Http\Controllers\Controller; // Import the Controller class
use App\Traits\ApiResponse;
use App\Models\Event;


class EventController extends Controller
{
    use ApiResponse;
    
    protected $eventAggregator;

    public function __construct(EventAggregator $eventAggregator)
    {
        $this->eventAggregator = $eventAggregator;
    }

    public function index(Request $request)
    {
        $this->eventAggregator->setUser($request->user())
            ->setTTL(5 * 60);
        return $this->success($this->eventAggregator->aggregateEvents(
            $request->input('last', 0),
            $request->input('limit', 100)
        ));
    }
    
    /**
     * Update event
     * @param UpdateEventRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $result = $this->eventAggregator
            ->setUser($request->user())
            ->update($id, $request->only(['is_read']));

        return $result
            ? $this->success([], 'Event successfully updated')
            : $this->error([], 'Event not found', 404);
    }

    public function updateStatus(Request $request, Event $event)
    {
        $event->update(['is_read' => 1]);

        return response()->json(['message' => 'Event status updated successfully']);
    }
}
