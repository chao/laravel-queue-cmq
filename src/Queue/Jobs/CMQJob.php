<?php

namespace Freyo\LaravelQueueCMQ\Queue\Jobs;

use Freyo\LaravelQueueCMQ\Queue\CMQQueue;
use Freyo\LaravelQueueCMQ\Queue\Driver\Message;
use Freyo\LaravelQueueCMQ\Queue\Driver\Queue;
use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\Job as JobContract;
use Illuminate\Database\DetectsDeadlocks;
use Illuminate\Queue\Jobs\Job;

class CMQJob extends Job implements JobContract
{
    use DetectsDeadlocks;

    protected $connection;
    protected $message;

    public function __construct(Container $container, CMQQueue $connection, Message $message, Queue $queue)
    {
        $this->container  = $container;
        $this->connection = $connection;
        $this->message    = $message;
        $this->queue      = $queue->getQueueName();
    }

    /**
     * Get the job identifier.
     *
     * @return string
     */
    public function getJobId()
    {
        return $this->message->msgId;
    }

    /**
     * Get the raw body of the job.
     *
     * @return string
     */
    public function getRawBody()
    {
        return $this->message->msgBody;
    }

    /**
     * Get the number of times the job has been attempted.
     *
     * @return int
     */
    public function attempts()
    {
        return $this->message->dequeueCount;
    }

    /**
     * Fire the job.
     *
     * @return void
     */
    public function fire()
    {
        parent::fire();
    }

    /**
     * Delete the job from the queue.
     *
     * @return void
     */
    public function delete()
    {
        parent::delete();

        $this->connection->getQueue($this->getQueue())->delete_message($this->message->receiptHandle);
    }

    /**
     * Release the job back into the queue.
     *
     * @param  int $delay
     *
     * @return void
     */
    public function release($delay = 0)
    {
        parent::release($delay);
    }
}