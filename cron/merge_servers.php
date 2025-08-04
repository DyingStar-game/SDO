<?php

declare(strict_types=1);


// run all 30 seconds


// TODO NOT RUN IF transfer players not finished
// can have a server crash, and will block it.
// perhaps manage a bit timeout, for example all 2 minutes

// register new data in one time

// $serversModifications = [];

// function tryMergeServers()
// {
//   $smallestServer = null;


//   $servers = \App\Models\Server::
//       where('is_free', false)
//     ->orderBy('x_size', 'asc')
//     ->orderBy('y_size', 'asc')
//     ->orderBy('z_size', 'asc')
//     ->get();

//     foreach ($servers as $server)
//     {
//       if (is_null($server->to_merge_server_id))
//       {

//       } else {

//       }


//     }

//   // Begin with the server have smallest zone




//   // Because we can have multiple divisions of servers, when have no parent (with with the constraints),
//   // get smallest server not yet check

// }


$ctrlServer = new \App\Controllers\Server();
$ctrlServer->manageOneServerBranch([]);

