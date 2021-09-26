<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\MailChimpService;
use Log;


class QueueUpsertContacts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $data;
    public $taskId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $data, $taskId)
    {
        $this->data = $data;
        $this->taskId = $taskId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $service = app(MailChimpService::class);
        if ($this->taskId !== 3) {
            $results = $service->upsertContact($this->data, $this->taskId); 
        } else {
            $results = $service->updateTags($this->data, $this->taskId);
        } 
    }
}
