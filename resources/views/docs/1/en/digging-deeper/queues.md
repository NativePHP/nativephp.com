---
title: Queues
order: 500
---
# Queues

Queueing tasks to be run in the background is a critical part of creating a great user experience.

NativePHP has built-in support for Laravel's [Queues](https://laravel.com/docs/queues).

## Queueing a job
If you're familiar with queueing jobs in Laravel, you should feel right at home. There's nothing special you need to do.

Jobs live in the SQLite [database](/docs/digging-deeper/databases) that your app uses by default and the `jobs` table
migration will have been created and migrated for you.

## Processing Jobs / Working the Queue
When your application boots up, NativePHP starts a single queue worker, ready to process any jobs you send its way.

There's nothing more required.

In the context of your user's device, it's very rare that you would need multiple queues or many workers, as your
application is likely to only be used by one user at a time.

## When to Queue
Given that your database and application typically exist on the same machine (i.e. there's no network involved),
queueing background tasks can mostly be left for very intense operations and when making API calls over the network.

Even so, you may find it more user-friendly to have slow tasks complete in the main application thread. You may simply
choose to have your UI indicate that something is occurring (e.g. with a loading spinner) while the user waits for the
process to finish.

This may be clearer for the user and easier to handle in case issues arise, as you can provide visual feedback to the
user and they can try again more easily.
